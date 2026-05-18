<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Display a listing of the categories.
     */
    public function index()
    {
        $categories = Category::latest()->get();
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
            'description' => 'nullable|string'
        ], [
            'name.required' => 'Vui lòng nhập tên danh mục.',
            'slug.unique' => 'Mã danh mục (slug) đã tồn tại.'
        ]);

        $data = $request->all();
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
            // Ensure unique slug
            $originalSlug = $data['slug'];
            $count = 1;
            while (Category::where('slug', $data['slug'])->exists()) {
                $data['slug'] = $originalSlug . '-' . $count;
                $count++;
            }
        }
        
        if (empty($data['icon'])) {
            $data['icon'] = 'fas fa-tag';
        }

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
            'slug' => 'required|string|max:255|unique:categories,slug,' . $category->id,
            'icon' => 'nullable|string|max:255',
            'description' => 'nullable|string'
        ], [
            'name.required' => 'Vui lòng nhập tên danh mục.',
            'slug.required' => 'Mã danh mục (slug) là bắt buộc.',
            'slug.unique' => 'Mã danh mục (slug) đã tồn tại.'
        ]);

        $data = $request->all();
        if (empty($data['icon'])) {
            $data['icon'] = 'fas fa-tag';
        }

        $category->update($data);

        return redirect()->route('admin.categories.index')->with('success', 'Cập nhật danh mục thành công.');
    }

    /**
     * Remove the specified category from storage.
     */
    public function destroy(Category $category)
    {
        // Check if there are products using this category's slug
        $hasProducts = \App\Models\Product::where('loai', $category->slug)->exists();
        
        if ($hasProducts) {
            return redirect()->route('admin.categories.index')
                ->with('error', 'Không thể xóa danh mục đang có sản phẩm. Hãy chuyển các sản phẩm sang danh mục khác trước.');
        }

        $category->delete();

        return redirect()->route('admin.categories.index')->with('success', 'Xóa danh mục thành công.');
    }
}
