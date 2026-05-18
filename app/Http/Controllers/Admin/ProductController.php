<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Show products management page
     */
    public function products(Request $request)
    {
        $query = Product::query();

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('ten_sp', 'like', "%{$search}%")
                  ->orWhere('mo_ta', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('trang_thai', $request->status); // con / het
        }

        // Filter by loai
        if ($request->filled('loai')) {
            $query->where('loai', $request->loai);
        }

        // Filter by stock
        if ($request->filled('stock_filter')) {
            switch ($request->stock_filter) {
                case 'low':
                    $query->where('so_luong', '<', 10);
                    break;
                case 'out':
                    $query->where('so_luong', 0);
                    break;
                case 'in':
                    $query->where('so_luong', '>', 0);
                    break;
            }
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $products = $query->paginate(15);

        return view('admin.products.index', compact('products'));
    }

    /**
     * Quick update product status
     */
    public function updateProductStatus(Request $request, Product $product)
    {
        $request->validate([
            'trang_thai' => 'required|in:con,het',
        ]);

        $product->update(['trang_thai' => $request->trang_thai]);

        if ($request->expectsJson()) {
            return response()->json([
                'success'    => true,
                'trang_thai' => $product->trang_thai,
                'message'    => 'Cập nhật trạng thái thành công!',
            ]);
        }

        return back()->with('success', 'Cập nhật trạng thái sản phẩm thành công!');
    }

    /**
     * Quick update product stock
     */
    public function updateProductStock(Request $request, Product $product)
    {
        $request->validate([
            'so_luong' => 'required|integer|min:0',
        ]);

        $product->update(['so_luong' => $request->so_luong]);

        if ($request->expectsJson()) {
            return response()->json([
                'success'  => true,
                'so_luong' => $product->so_luong,
                'message'  => 'Cập nhật số lượng thành công!',
            ]);
        }

        return back()->with('success', 'Cập nhật số lượng thành công!');
    }
}
