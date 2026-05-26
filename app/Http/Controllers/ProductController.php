<?php

namespace App\Http\Controllers;

use App\Exports\SanPhamExport;
use App\Exports\ThongKeExport;
use App\Models\AuditLog;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Promotion;
use App\Models\Review;
use App\Services\ProductService;
use App\Services\SizeRecommendationService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
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
        $latestProducts = Product::with(['productImages', 'variants'])->where('trang_thai', 'con')
            ->orderBy('created_at', 'desc')
            ->take(8)
            ->get();

        // Lấy danh sách sản phẩm có khuyến mãi
        $activePromotions = Promotion::currentlyActive()->with('items')->get();
        $allProducts = Product::where('trang_thai', 'con')->with(['wishlists', 'productImages', 'variants'])->limit(1000)->get();

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
        $promoProducts = $promoProducts->sortByDesc(fn ($p) => $p->gia - $p->promo_price)->take(8);

        return view('home.index', compact('latestProducts', 'promoProducts', 'totalProducts', 'inStockProducts', 'outOfStockProducts', 'totalValue'));
    }

    public function index(Request $request)
    {
        if ($request->filled('loai_filter') && count($request->query()) === 1) {
            $category = Category::where('slug', (string) $request->string('loai_filter'))->first();

            if ($category) {
                return redirect()->route('categories.show', ['category' => $category->slug], 301);
            }
        }

        $filters = $request->only(['search', 'loai_filter', 'min_price', 'max_price', 'sort', 'trang_thai_filter']);
        $perPage = $request->get('per_page', 12); // Change to 12 for 3 or 4 columns grid

        $products = $this->productService->getFilteredProducts($filters, $perPage);

        $totalProducts = Product::count();
        $inStockProducts = Product::where('trang_thai', 'con')->count();
        $outOfStockProducts = Product::where('trang_thai', 'het')->count();
        $loaiList = Product::getLoaiList();

        return view('products.index', compact('products', 'totalProducts', 'inStockProducts', 'outOfStockProducts', 'loaiList'));
    }

    public function store(Request $request)
    {
        $this->normalizePriceInput($request);

        $request->validate([
            'ten_sp' => 'required|string|max:255',
            'loai' => 'nullable|string|max:255|exists:categories,slug',
            'new_category_name' => 'nullable|string|max:255',
            'new_category_parent_id' => 'nullable|exists:categories,id',
            'brand_name' => 'nullable|string|max:255',
            'mo_ta' => 'nullable|string',
            'gia' => 'required|integer|min:1',
            'so_luong' => 'required|integer|min:0',
            'trang_thai' => 'required|in:con,het',
            'anh_file' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
            'anh' => 'nullable|url|max:2048',
            'image_files' => 'nullable|array',
            'image_files.*' => 'image|mimes:jpg,jpeg,png,gif,webp|max:2048',
            'image_urls' => 'nullable|string',
            'variants_text' => 'nullable|string',
            'sizes' => 'nullable|array',
            'sizes.*' => 'string|max:255',
        ]);

        try {
            $data = $request->only(['ten_sp', 'mo_ta', 'gia', 'so_luong', 'trang_thai']);
            $data['loai'] = $this->resolveCategorySlug($request);
            $data['brand_id'] = $this->resolveBrandId($request->input('brand_name'));
            $variants = $this->parseVariants($request);
            $galleryUrls = $this->parseImageUrls($request->input('image_urls'));
            $image = $request->hasFile('anh_file') ? $request->file('anh_file') : null;
            $imageUrl = $request->filled('anh') ? trim($request->input('anh')) : null;

            $product = $this->productService->createProduct(
                $data,
                $image,
                $imageUrl,
                $variants,
                $request->file('image_files', []),
                $galleryUrls
            );
            AuditLog::record('product_created', $product, "Created product {$product->ten_sp}", null, [
                'ten_sp' => $product->ten_sp,
                'gia' => $product->gia,
                'so_luong' => $product->so_luong,
            ]);

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Sản phẩm đã được tạo thành công!', 'product' => $product]);
            }

            $redirect = $this->resolveRedirect($request->input('_ref'));

            return redirect($redirect)->with('success', 'Sản phẩm đã được tạo thành công!');
        } catch (ValidationException $e) {
            throw $e;
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
        $categoryTree = Category::treeList();
        $brands = Brand::orderBy('name')->get();

        return view('admin.products.create', compact('loaiList', 'categoryTree', 'brands'));
    }

    public function show(Product $product)
    {
        $product->loadMissing(['productImages', 'variants', 'brand', 'category']);

        if (request()->ajax() || request()->expectsJson()) {
            return response()->json([
                'id' => $product->id,
                'ten_sp' => $product->ten_sp,
                'loai' => $product->loai,
                'loai_label' => $product->loai_label,
                'brand' => $product->brand?->name,
                'mo_ta' => $product->mo_ta,
                'gia' => $product->gia,
                'so_luong' => $product->so_luong,
                'trang_thai' => $product->trang_thai,
                'variants' => $product->variant_options,
                'sizes' => $product->variant_options,
                'requires_variant' => count($product->variant_options) > 0,
                'requires_size' => count($product->variant_options) > 0,
                'image_path' => $product->image_path,
                'images' => $product->productImages->pluck('image_url')->all(),
                'created_at' => $product->created_at,
                'updated_at' => $product->updated_at,
            ]);
        }

        $relatedProducts = Product::where('loai', $product->loai)
            ->where('id', '!=', $product->id)
            ->with(['productImages', 'variants'])
            ->take(4)
            ->get();

        // Load approved reviews với user info
        $reviews = $product->approvedReviews()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        $productOriginalPrice = (int) round((float) $product->gia);
        $productCurrentPrice = $productOriginalPrice;
        $activePromotion = $product->getActivePromotion();

        if ($activePromotion) {
            $discountedPrice = $activePromotion->getDiscountedPrice($product);

            if ($discountedPrice !== null && $discountedPrice < $productOriginalPrice) {
                $productCurrentPrice = (int) round($discountedPrice);
            }
        }

        // Kiểm tra user hiện tại đã review chưa
        $userReview = null;
        $hasPurchased = false;  // Đã mua sản phẩm
        $paymentPaid = false;  // Đã được xác nhận thanh toán
        $canReview = false;  // Được phép đánh giá

        if (auth()->check()) {
            $userReview = Review::where('user_id', auth()->id())
                ->where('product_id', $product->id)
                ->first();

            // Kiểm tra có đơn hàng hoàn thành chứa sản phẩm này không
            $completedOrder = auth()->user()->orders()
                ->whereHas('orderItems', fn ($q) => $q->where('product_id', $product->id))
                ->where('trang_thai', 'completed')
                ->latest()
                ->first();

            if ($completedOrder) {
                $hasPurchased = true;
                $paymentPaid = true;
                $canReview = ! $userReview; // Chưa đánh giá thì mới được gửi
            } else {
                // Đã đặt mua nhưng chưa hoàn thành
                $pendingOrder = auth()->user()->orders()
                    ->whereHas('orderItems', fn ($q) => $q->where('product_id', $product->id))
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
            $hasClothingSize = collect($product->variant_options)->contains(
                fn ($option) => preg_match('/\b(?:XS|S|M|L|XL|XXL)\b/i', $option)
            );

            if ($user->height && $user->weight && $hasClothingSize) {
                $sizeService = new SizeRecommendationService;
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
            'sizeRecommendation',
            'productOriginalPrice',
            'productCurrentPrice'
        ));
    }

    public function redirectLegacy(Product $product)
    {
        return redirect()->route('products.show', ['product' => $product->slug], 301);
    }

    public function edit(Product $product)
    {
        $product->loadMissing(['productImages', 'variants', 'brand']);

        if (request()->ajax() || request()->expectsJson()) {
            return response()->json([
                'id' => $product->id,
                'ten_sp' => $product->ten_sp,
                'loai' => $product->loai,
                'brand' => $product->brand?->name,
                'mo_ta' => $product->mo_ta,
                'gia' => $product->gia,
                'so_luong' => $product->so_luong,
                'trang_thai' => $product->trang_thai,
                'variants' => $product->variant_options,
                'images' => $product->productImages->pluck('image_url')->all(),
                'image_path' => $product->image_path,
                'created_at' => $product->created_at,
                'updated_at' => $product->updated_at,
            ]);
        }
        $loaiList = Product::getLoaiList();
        $categoryTree = Category::treeList();
        $brands = Brand::orderBy('name')->get();

        return view('admin.products.edit', compact('product', 'loaiList', 'categoryTree', 'brands'));
    }

    public function update(Request $request, Product $product)
    {
        $this->normalizePriceInput($request);

        $request->validate([
            'ten_sp' => 'required|string|max:255',
            'loai' => 'nullable|string|max:255|exists:categories,slug',
            'new_category_name' => 'nullable|string|max:255',
            'new_category_parent_id' => 'nullable|exists:categories,id',
            'brand_name' => 'nullable|string|max:255',
            'mo_ta' => 'nullable|string',
            'gia' => 'required|integer|min:1',
            'so_luong' => 'required|integer|min:0',
            'trang_thai' => 'required|in:con,het',
            'anh_file' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
            'anh' => 'nullable|url|max:2048',
            'image_files' => 'nullable|array',
            'image_files.*' => 'image|mimes:jpg,jpeg,png,gif,webp|max:2048',
            'image_urls' => 'nullable|string',
            'variants_text' => 'nullable|string',
            'sizes' => 'nullable|array',
            'sizes.*' => 'string|max:255',
        ]);

        try {
            $data = $request->only(['ten_sp', 'mo_ta', 'gia', 'so_luong', 'trang_thai']);
            $data['loai'] = $this->resolveCategorySlug($request);
            $data['brand_id'] = $request->has('brand_name')
                ? $this->resolveBrandId($request->input('brand_name'))
                : $product->brand_id;
            $variants = $request->has('variants_text') || $request->has('sizes')
                ? $this->parseVariants($request)
                : $product->variant_options;
            $galleryUrls = $this->parseImageUrls($request->input('image_urls'));
            $image = $request->hasFile('anh_file') ? $request->file('anh_file') : null;
            $imageUrl = $request->filled('anh') ? trim($request->input('anh')) : null;

            $oldValues = $product->only(['ten_sp', 'loai', 'brand_id', 'gia', 'so_luong', 'trang_thai']);
            $updatedProduct = $this->productService->updateProduct(
                $product,
                $data,
                $image,
                $imageUrl,
                $variants,
                $request->file('image_files', []),
                $galleryUrls
            );
            AuditLog::record('product_updated', $updatedProduct, "Updated product {$updatedProduct->ten_sp}", $oldValues, [
                'ten_sp' => $updatedProduct->ten_sp,
                'loai' => $updatedProduct->loai,
                'brand_id' => $updatedProduct->brand_id,
                'gia' => $updatedProduct->gia,
                'so_luong' => $updatedProduct->so_luong,
                'trang_thai' => $updatedProduct->trang_thai,
            ]);

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Sản phẩm đã được cập nhật thành công!', 'product' => $updatedProduct]);
            }

            $redirect = $this->resolveRedirect($request->input('_ref'));

            return redirect($redirect)->with('success', 'Sản phẩm đã được cập nhật thành công!');
        } catch (ValidationException $e) {
            throw $e;
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
            $oldValues = $product->only(['ten_sp', 'loai', 'gia', 'so_luong', 'trang_thai']);
            $this->productService->deleteProduct($product);
            AuditLog::record('product_deleted', $product, "Deleted product {$product->ten_sp}", $oldValues);

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Sản phẩm đã được chuyển vào thùng rác. Có thể khôi phục trong 60 ngày.',
                ]);
            }

            $redirect = $this->resolveRedirect(request()->headers->get('referer'));

            return redirect($redirect)->with('success', 'Sản phẩm đã được chuyển vào thùng rác. Có thể khôi phục trong 60 ngày.');
        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể xóa sản phẩm: '.$e->getMessage(),
                ], 422);
            }

            return back()->withErrors(['error' => 'Không thể xóa sản phẩm: '.$e->getMessage()]);
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

    private function resolveBrandId(?string $brandName): ?int
    {
        $brandName = trim((string) $brandName);

        if ($brandName === '') {
            return null;
        }

        $slug = Str::slug($brandName) ?: 'brand';

        return Brand::firstOrCreate(
            ['slug' => $slug],
            ['name' => $brandName]
        )->id;
    }

    private function resolveCategorySlug(Request $request): ?string
    {
        $categoryName = trim((string) $request->input('new_category_name'));

        if ($categoryName !== '') {
            return Category::create([
                'name' => $categoryName,
                'slug' => Category::generateUniqueSlug($categoryName),
                'parent_id' => $request->integer('new_category_parent_id') ?: null,
                'icon' => 'fas fa-tag',
                'is_new' => true,
            ])->slug;
        }

        return $request->filled('loai') ? (string) $request->input('loai') : null;
    }

    private function parseVariants(Request $request): array
    {
        if ($request->filled('variants_text')) {
            return collect(preg_split('/\R/u', (string) $request->input('variants_text')))
                ->map(fn ($variant) => ltrim(trim($variant), "-* \t"))
                ->filter()
                ->unique()
                ->values()
                ->all();
        }

        return array_values($request->input('sizes', []));
    }

    private function parseImageUrls(?string $value): array
    {
        $urls = collect(preg_split('/\R/u', (string) $value))
            ->map(fn ($url) => trim($url))
            ->filter()
            ->unique()
            ->values();

        if ($urls->contains(fn ($url) => ! filter_var($url, FILTER_VALIDATE_URL))) {
            throw ValidationException::withMessages([
                'image_urls' => 'Mỗi dòng ảnh bổ sung phải là một URL hợp lệ.',
            ]);
        }

        return $urls->all();
    }

    /**
     * Export products to Excel
     */
    public function exportProducts()
    {
        try {
            $filename = 'danh_sach_san_pham_'.date('Y-m-d_H-i-s').'.xlsx';

            return Excel::download(new SanPhamExport, $filename);
        } catch (\Exception $e) {
            return back()->with('error', 'Không thể xuất file: '.$e->getMessage());
        }
    }

    /**
     * Export statistics to Excel
     */
    public function exportStatistics()
    {
        try {
            $filename = 'thong_ke_san_pham_'.date('Y-m-d_H-i-s').'.xlsx';

            return Excel::download(new ThongKeExport, $filename);
        } catch (\Exception $e) {
            return back()->with('error', 'Không thể xuất file thống kê: '.$e->getMessage());
        }
    }
}
