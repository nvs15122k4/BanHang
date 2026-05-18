<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    protected ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    /**
     * Home storefront landing
     */
    public function home()
    {
        $totalProducts = Product::count();
        $inStockProducts = Product::where('trang_thai', 'con')->count();
        $outOfStockProducts = Product::where('trang_thai', 'het')->count();
        
        // Sử dụng raw SQL để tránh cast issue
        $totalValue = \Illuminate\Support\Facades\DB::table('products')->sum('gia');

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

    /**
     * Public index / Catalog search storefront
     */
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'loai_filter', 'min_price', 'max_price', 'sort', 'trang_thai_filter']);
        $perPage = $request->get('per_page', 12);

        $products = $this->productService->getFilteredProducts($filters, $perPage);

        $totalProducts    = Product::count();
        $inStockProducts  = Product::where('trang_thai', 'con')->count();
        $outOfStockProducts = Product::where('trang_thai', 'het')->count();
        $loaiList         = Product::getLoaiList();

        return view('products.index', compact('products', 'totalProducts', 'inStockProducts', 'outOfStockProducts', 'loaiList'));
    }

    /**
     * Show single product details
     */
    public function show(Product $product)
    {
        $product->load(['reviews.user']);

        // Related items
        $relatedProducts = Product::where('loai', $product->loai)
            ->where('id', '!=', $product->id)
            ->take(4)
            ->get();

        return view('products.show', compact('product', 'relatedProducts'));
    }

    /**
     * Admin: Show form to create product
     */
    public function create()
    {
        $categories = Category::latest()->get();
        return view('admin.products.create', compact('categories'));
    }

    /**
     * Admin: Store product
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ten_sp'     => 'required|string|max:255',
            'loai'       => 'required|string|max:255',
            'gia'        => 'required|numeric|min:0',
            'so_luong'   => 'required|integer|min:0',
            'anh'        => 'nullable|string|max:2048',
            'mo_ta'      => 'nullable|string',
            'trang_thai' => 'required|in:con,het',
        ], [
            'ten_sp.required' => 'Vui lòng nhập tên sản phẩm',
            'loai.required'   => 'Vui lòng chọn loại sản phẩm',
            'gia.required'    => 'Vui lòng nhập giá',
            'so_luong.required'=> 'Vui lòng nhập số lượng',
        ]);

        Product::create($validated);

        return redirect()->route('products.index')->with('success', 'Tạo sản phẩm thành công!');
    }

    /**
     * Admin: Show form to edit product
     */
    public function edit(Product $product)
    {
        $categories = Category::latest()->get();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    /**
     * Admin: Update product
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'ten_sp'     => 'required|string|max:255',
            'loai'       => 'required|string|max:255',
            'gia'        => 'required|numeric|min:0',
            'so_luong'   => 'required|integer|min:0',
            'anh'        => 'nullable|string|max:2048',
            'mo_ta'      => 'nullable|string',
            'trang_thai' => 'required|in:con,het',
        ], [
            'ten_sp.required' => 'Vui lòng nhập tên sản phẩm',
            'loai.required'   => 'Vui lòng chọn loại sản phẩm',
            'gia.required'    => 'Vui lòng nhập giá',
            'so_luong.required'=> 'Vui lòng nhập số lượng',
        ]);

        $product->update($validated);

        return redirect()->route('products.index')->with('success', 'Cập nhật sản phẩm thành công!');
    }

    /**
     * Admin: Delete product
     */
    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Xóa sản phẩm thành công!');
    }

    /**
     * Admin: Export products CSV
     */
    public function exportProducts()
    {
        $products = Product::all();
        $csvFileName = 'products_' . date('Ymd_His') . '.csv';
        
        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$csvFileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['ID', 'Tên Sản Phẩm', 'Danh Mục', 'Giá', 'Số Lượng', 'Trạng Thái', 'Ngày Tạo'];

        $callback = function() use($products, $columns) {
            $file = fopen('php://output', 'w');
            // Add UTF-8 BOM
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($file, $columns);

            foreach ($products as $p) {
                fputcsv($file, [
                    $p->id,
                    $p->ten_sp,
                    $p->loai,
                    $p->gia,
                    $p->so_luong,
                    $p->trang_thai === 'con' ? 'Còn hàng' : 'Hết hàng',
                    $p->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Admin: Export statistics CSV
     */
    public function exportStatistics()
    {
        $csvFileName = 'statistics_' . date('Ymd_His') . '.csv';
        
        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$csvFileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, ['Chỉ Số Thống Kê', 'Giá Trị']);
            fputcsv($file, ['Tổng số người dùng', \App\Models\User::count()]);
            fputcsv($file, ['Tổng số quản trị viên', \App\Models\User::where('role', 'admin')->count()]);
            fputcsv($file, ['Tổng số sản phẩm', Product::count()]);
            fputcsv($file, ['Sản phẩm còn hàng', Product::where('trang_thai', 'con')->count()]);
            fputcsv($file, ['Tổng tồn kho', Product::sum('so_luong')]);
            fputcsv($file, ['Đơn hàng đã hoàn thành', \App\Models\Order::where('trang_thai', 'completed')->count()]);
            fputcsv($file, ['Doanh thu (VNĐ)', \App\Models\Order::where('trang_thai', 'completed')->sum('thanh_tien')]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
