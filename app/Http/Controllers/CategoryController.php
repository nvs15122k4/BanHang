<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\PromotionItem;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CategoryController extends Controller
{
    public function __construct(protected ProductService $productService) {}

    public function show(Request $request, Category $category)
    {
        $filters = $request->only(['min_price', 'max_price', 'sort', 'trang_thai_filter']);
        $filters['loai_filter'] = $category->slug;
        $perPage = $request->integer('per_page', 12);

        $products = $this->productService->getFilteredProducts($filters, $perPage);
        $totalProducts = Product::count();
        $inStockProducts = Product::where('trang_thai', 'con')->count();
        $outOfStockProducts = Product::where('trang_thai', 'het')->count();
        $loaiList = Product::getLoaiList();

        return view('products.index', compact(
            'category',
            'products',
            'totalProducts',
            'inStockProducts',
            'outOfStockProducts',
            'loaiList'
        ));
    }

    /**
     * Display a listing of the categories.
     */
    public function index()
    {
        $categories = Category::treeList();

        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Store a newly created category in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:categories,slug',
            'icon' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
        ], [
            'name.required' => 'Vui lòng nhập tên danh mục.',
            'slug.unique' => 'Mã danh mục (slug) đã tồn tại.',
        ]);

        $data = $request->only(['name', 'slug', 'icon', 'description', 'parent_id']);
        if (empty($data['slug'])) {
            $data['slug'] = Category::generateUniqueSlug($data['name']);
        }

        if (empty($data['icon'])) {
            $data['icon'] = 'fas fa-tag';
        }

        $data['is_new'] = true;
        Category::create($data);

        return redirect()->route('admin.categories.index')->with('success', 'Thêm danh mục thành công.');
    }

    /**
     * Update the specified category in storage.
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:categories,slug,'.$category->id,
            'icon' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id|not_in:'.$category->id,
        ], [
            'name.required' => 'Vui lòng nhập tên danh mục.',
            'slug.required' => 'Mã danh mục (slug) là bắt buộc.',
            'slug.unique' => 'Mã danh mục (slug) đã tồn tại.',
        ]);

        $this->ensureValidParent($category, $request->integer('parent_id') ?: null);

        $data = $request->only(['name', 'slug', 'icon', 'description', 'parent_id']);
        if (empty($data['icon'])) {
            $data['icon'] = 'fas fa-tag';
        }

        $data['is_new'] = false;
        $oldSlug = $category->slug;

        DB::transaction(function () use ($category, $data, $oldSlug): void {
            $category->update($data);

            if ($oldSlug !== $category->slug) {
                Product::where('loai', $oldSlug)->update(['loai' => $category->slug]);
                PromotionItem::where('loai', 'category')
                    ->where('gia_tri', $oldSlug)
                    ->update(['gia_tri' => $category->slug]);
            }
        });

        return redirect()->route('admin.categories.index')->with('success', 'Cập nhật danh mục thành công.');
    }

    /**
     * Remove the specified category from storage.
     */
    public function destroy(Category $category)
    {
        // Check if there are products using this category's slug
        $hasProducts = Product::where('loai', $category->slug)->exists();
        $hasChildren = $category->children()->exists();

        if ($hasProducts || $hasChildren) {
            return redirect()->route('admin.categories.index')
                ->with('error', 'Không thể xóa danh mục đang có sản phẩm hoặc danh mục con. Hãy chuyển các liên kết trước.');
        }

        $category->delete();

        return redirect()->route('admin.categories.index')->with('success', 'Xóa danh mục thành công.');
    }

    public function markSeen(Category $category)
    {
        if ($category->is_new) {
            $category->update(['is_new' => false]);
        }

        return response()->json(['success' => true]);
    }

    private function ensureValidParent(Category $category, ?int $parentId): void
    {
        while ($parentId !== null) {
            if ($parentId === $category->id) {
                throw ValidationException::withMessages([
                    'parent_id' => 'Danh mục cha không thể là chính danh mục này hoặc danh mục con của nó.',
                ]);
            }

            $parentId = Category::whereKey($parentId)->value('parent_id');
        }
    }
}
