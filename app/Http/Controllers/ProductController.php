<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\ProductService;
use App\Services\SizeRecommendationService;
use App\Exports\SanPhamExport;
use App\Exports\ThongKeExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ProductController extends Controller
{
    protected ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    /**
     * Trang chủ dashboard
     */
    public function home()
    {
        $totalProducts = Product::count();
        $inStockProducts = Product::where('trang_thai', 'con')->count();
        $outOfStockProducts = Product::where('trang_thai', 'het')->count();

        // Sử dụng raw SQL để tránh cast issue
        $totalValue = \DB::table('products')->sum('gia');

        // Lấy 8 sản phẩm mới nhất
        $latestProducts = Product::where('trang_thai', 'con')
            ->orderBy('created_at', 'desc')
            ->take(8)
            ->get();

        // Lấy danh sách sản phẩm có khuyến mãi
        $activePromotions = \App\Models\Promotion::currentlyActive()->with('items')->get();
        $allProducts = Product::where('trang_thai', 'con')->with(['wishlists'])->limit(1000)->get();

        $promoProducts = collect();
        foreach ($allProducts as $product) {
            $bestPromo = null;
            $bestDiscount = 0;
            foreach ($activePromotions as $promo) {
                $discountedPrice = $promo->getDiscountedPrice($product);
                if ($discountedPrice !== null) {
                    $discount = $product->gia - $discountedPrice;
                    if ($discount > $bestDiscount) {
                        $bestDiscount = $discount;
                        $bestPromo = $promo;
                        $product->promo_price = $discountedPrice;
                        $product->promo = $promo;
                    }
                }
            }
            if ($bestPromo) {
                $promoProducts->push($product);
            }
        }

        // Sắp xếp giảm nhiều nhất và lấy 8 sản phẩm
        $promoProducts = $promoProducts->sortByDesc(fn($p) => $p->gia - $p->promo_price)->take(8);

        return view('home.index', compact('latestProducts', 'promoProducts', 'totalProducts', 'inStockProducts', 'outOfStockProducts', 'totalValue'));
    }

    public function index(Request $request)
    {
        $filters = $request->only(['search', 'loai_filter', 'min_price', 'max_price', 'sort', 'trang_thai_filter']);
        $perPage = $request->get('per_page', 12); // Change to 12 for 3 or 4 columns grid

        $products = $this->productService->getFilteredProducts($filters, $perPage);

        $totalProducts    = Product::count();
        $inStockProducts  = Product::where('trang_thai', 'con')->count();
        $outOfStockProducts = Product::where('trang_thai', 'het')->count();
        $loaiList         = Product::getLoaiList();

        return view('products.index', compact('products', 'totalProducts', 'inStockProducts', 'outOfStockProducts', 'loaiList'));
    }

    public function store(Request $request)
    {
        $this->normalizePriceInput($request);

        $request->validate([
            'ten_sp'    => 'required|string|max:255',
            'loai'      => 'nullable|string|max:50',
            'mo_ta'     => 'nullable|string',
            'gia'       => 'required|integer|min:1',
            'so_luong'  => 'required|integer|min:0',
            'trang_thai' => 'required|in:con,het',
            'anh_file'  => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
            'anh'       => 'nullable|url|max:2048',
            'sizes'     => 'nullable|array',
            'sizes.*'   => 'string|in:XS,S,M,L,XL,XXL',
        ]);

        try {
            $data     = $request->only(['ten_sp', 'loai', 'mo_ta', 'gia', 'so_luong', 'trang_thai']);
            $data['sizes'] = array_values($request->input('sizes', []));
            $image    = $request->hasFile('anh_file') ? $request->file('anh_file') : null;
            $imageUrl = $request->filled('anh') ? trim($request->input('anh')) : null;

            $product = $this->productService->createProduct($data, $image, $imageUrl);

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Sản phẩm đã được tạo thành công!', 'product' => $product]);
            }

            $redirect = $this->resolveRedirect($request->input('_ref'));
            return redirect($redirect)->with('success', 'Sản phẩm đã được tạo thành công!');
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
            }
            return back()->withErrors(['anh_file' => $e->getMessage()])->withInput();
        }
    }

    public function create()
    {
        $loaiList = Product::getLoaiList();
        return view('admin.products.create', compact('loaiList'));
    }

    public function show(Product $product)
    {
        if (request()->ajax() || request()->expectsJson()) {
            return response()->json([
                'id'         => $product->id,
                'ten_sp'     => $product->ten_sp,
                'loai'       => $product->loai,
                'loai_label' => $product->loai_label,
                'mo_ta'      => $product->mo_ta,
                'gia'        => $product->gia,
                'so_luong'   => $product->so_luong,
                'trang_thai' => $product->trang_thai,
                'sizes'      => $product->sizes ?? [],
                'requires_size' => count($product->sizes ?? []) > 0,
                'image_path' => $product->image_path,
                'created_at' => $product->created_at,
                'updated_at' => $product->updated_at,
            ]);
        }

        $relatedProducts = Product::where('loai', $product->loai)
            ->where('id', '!=', $product->id)
            ->take(4)
            ->get();

        // Load approved reviews với user info
        $reviews = $product->approvedReviews()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        // Kiểm tra user hiện tại đã review chưa
        $userReview    = null;
        $hasPurchased  = false;  // Đã mua sản phẩm
        $paymentPaid   = false;  // Đã được xác nhận thanh toán
        $canReview     = false;  // Được phép đánh giá

        if (auth()->check()) {
            $userReview = \App\Models\Review::where('user_id', auth()->id())
                ->where('product_id', $product->id)
                ->first();

            // Kiểm tra có đơn hàng hoàn thành chứa sản phẩm này không
            $completedOrder = auth()->user()->orders()
                ->whereHas('orderItems', fn($q) => $q->where('product_id', $product->id))
                ->where('trang_thai', 'completed')
                ->latest()
                ->first();

            if ($completedOrder) {
                $hasPurchased = true;
                $paymentPaid  = true;
                $canReview    = !$userReview; // Chưa đánh giá thì mới được gửi
            } else {
                // Đã đặt mua nhưng chưa hoàn thành
                $pendingOrder = auth()->user()->orders()
                    ->whereHas('orderItems', fn($q) => $q->where('product_id', $product->id))
                    ->whereIn('trang_thai', ['pending', 'confirmed', 'shipping'])
                    ->latest()
                    ->first();

                if ($pendingOrder) {
                    $hasPurchased = true;
                }
            }
        }

        // Lấy size recommendation nếu user đã login và có height/weight
        $sizeRecommendation = null;
        if (auth()->check()) {
            $user = auth()->user();
            if ($user->height && $user->weight) {
                $sizeService = new SizeRecommendationService();
                $sizeRecommendation = $sizeService->recommendSize($user->height, $user->weight);
            }
        }

        return view('products.show', compact(
            'product',
            'relatedProducts',
            'reviews',
            'userReview',
            'hasPurchased',
            'paymentPaid',
            'canReview',
            'sizeRecommendation'
        ));
    }

    public function edit(Product $product)
    {
        if (request()->ajax() || request()->expectsJson()) {
            return response()->json([
                'id'         => $product->id,
                'ten_sp'     => $product->ten_sp,
                'loai'       => $product->loai,
                'mo_ta'      => $product->mo_ta,
                'gia'        => $product->gia,
                'so_luong'   => $product->so_luong,
                'trang_thai' => $product->trang_thai,
                'image_path' => $product->image_path,
                'created_at' => $product->created_at,
                'updated_at' => $product->updated_at,
            ]);
        }
        $loaiList = Product::getLoaiList();
        return view('admin.products.edit', compact('product', 'loaiList'));
    }

    public function update(Request $request, Product $product)
    {
        $this->normalizePriceInput($request);

        $request->validate([
            'ten_sp'     => 'required|string|max:255',
            'loai'       => 'nullable|string|max:50',
            'mo_ta'      => 'nullable|string',
            'gia'        => 'required|integer|min:1',
            'so_luong'   => 'required|integer|min:0',
            'trang_thai' => 'required|in:con,het',
            'anh_file'   => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
            'anh'        => 'nullable|url|max:2048',
            'sizes'      => 'nullable|array',
            'sizes.*'    => 'string|in:XS,S,M,L,XL,XXL',
        ]);

        try {
            $data     = $request->only(['ten_sp', 'loai', 'mo_ta', 'gia', 'so_luong', 'trang_thai']);
            $data['sizes'] = array_values($request->input('sizes', []));
            $image    = $request->hasFile('anh_file') ? $request->file('anh_file') : null;
            $imageUrl = $request->filled('anh') ? trim($request->input('anh')) : null;

            $updatedProduct = $this->productService->updateProduct($product, $data, $image, $imageUrl);

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Sản phẩm đã được cập nhật thành công!', 'product' => $updatedProduct]);
            }

            $redirect = $this->resolveRedirect($request->input('_ref'));
            return redirect($redirect)->with('success', 'Sản phẩm đã được cập nhật thành công!');
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
            }
            return back()->withErrors(['anh_file' => $e->getMessage()])->withInput();
        }
    }

    public function destroy(Product $product)
    {
        try {
            $this->productService->deleteProduct($product);

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Sản phẩm đã được chuyển vào thùng rác. Có thể khôi phục trong 60 ngày.'
                ]);
            }

            $redirect = $this->resolveRedirect(request()->headers->get('referer'));
            return redirect($redirect)->with('success', 'Sản phẩm đã được chuyển vào thùng rác. Có thể khôi phục trong 60 ngày.');
        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể xóa sản phẩm: ' . $e->getMessage()
                ], 422);
            }

            return back()->withErrors(['error' => 'Không thể xóa sản phẩm: ' . $e->getMessage()]);
        }
    }

    /**
     * Xác định URL redirect sau khi store/update/destroy.
     * Nếu ref là trang admin thì về admin.products, ngược lại về products.index.
     */
    private function resolveRedirect(?string $ref): string
    {
        if ($ref && str_contains($ref, '/admin')) {
            return route('admin.products');
        }
        return route('admin.products'); // Mặc định luôn về admin khi admin thao tác
    }

    private function normalizePriceInput(Request $request): void
    {
        if (! $request->has('gia')) {
            return;
        }

        $price = preg_replace('/\D+/', '', (string) $request->input('gia'));
        $request->merge(['gia' => $price]);
    }

    /**
     * Export products to Excel
     */
    public function exportProducts()
    {
        try {
            $filename = 'danh_sach_san_pham_' . date('Y-m-d_H-i-s') . '.xlsx';

            return Excel::download(new SanPhamExport, $filename);
        } catch (\Exception $e) {
            return back()->with('error', 'Không thể xuất file: ' . $e->getMessage());
        }
    }

    /**
     * Export statistics to Excel
     */
    public function exportStatistics()
    {
        try {
            $filename = 'thong_ke_san_pham_' . date('Y-m-d_H-i-s') . '.xlsx';

            return Excel::download(new ThongKeExport, $filename);
        } catch (\Exception $e) {
            return back()->with('error', 'Không thể xuất file thống kê: ' . $e->getMessage());
        }
    }
}
