<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\InventoryLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    /**
     * Display inventory list
     */
    public function index(Request $request)
    {
        $query = Product::query();

        // Search - dùng đúng field ten_sp
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('ten_sp', 'like', "%{$search}%");
        }

        // Filter by stock level
        if ($request->filled('stock_filter')) {
            switch ($request->stock_filter) {
                case 'low':
                    $query->where('so_luong', '<', 10)->where('so_luong', '>', 0);
                    break;
                case 'out':
                    $query->where('so_luong', 0);
                    break;
                case 'in':
                    $query->where('so_luong', '>', 0);
                    break;
            }
        }

        $products = $query->orderBy('so_luong', 'asc')->paginate(20);

        // Statistics
        $stats = [
            'total_products' => Product::count(),
            'total_stock'    => Product::sum('so_luong'),
            'low_stock'      => Product::where('so_luong', '<', 10)->where('so_luong', '>', 0)->count(),
            'out_of_stock'   => Product::where('so_luong', 0)->count(),
            'total_value'    => Product::selectRaw('SUM(so_luong * gia) as total')->value('total') ?? 0,
        ];

        return view('admin.inventory.index', compact('products', 'stats'));
    }

    /**
     * Show inventory logs
     */
    public function logs(Request $request)
    {
        $query = InventoryLog::with(['product', 'user', 'order'])->orderBy('created_at', 'desc');

        // Filter by product
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        // Filter by type
        if ($request->filled('loai')) {
            $query->where('loai', $request->loai);
        }

        // Filter by date range
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $logs = $query->paginate(20);
        $products = Product::orderBy('ten_sp')->get();

        return view('admin.inventory.logs', compact('logs', 'products'));
    }

    /**
     * Tự động cập nhật trạng thái sản phẩm dựa trên số lượng tồn kho
     * so_luong <= 0 → 'het' (ngừng bán)
     * so_luong > 0  → 'con' (còn hàng)
     */
    private function syncProductStatus(Product $product): void
    {
        $newStatus = $product->so_luong > 0 ? 'con' : 'het';
        if ($product->trang_thai !== $newStatus) {
            $product->update(['trang_thai' => $newStatus]);
        }
    }

    /**
     * Import stock
     */
    public function import(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'so_luong'   => 'required|integer|min:1',
            'ly_do'      => 'required|string|max:255',
        ]);

        DB::transaction(function () use ($validated) {
            $product     = Product::findOrFail($validated['product_id']);
            $oldQuantity = $product->so_luong;
            $newQuantity = $oldQuantity + $validated['so_luong'];

            $product->update(['so_luong' => $newQuantity]);
            $this->syncProductStatus($product->fresh());

            InventoryLog::create([
                'product_id'        => $product->id,
                'loai'              => 'in',
                'so_luong_truoc'    => $oldQuantity,
                'so_luong_thay_doi' => $validated['so_luong'],
                'so_luong_sau'      => $newQuantity,
                'ly_do'             => $validated['ly_do'],
                'user_id'           => Auth::id(),
            ]);
        });

        return back()->with('success', 'Nhập kho thành công! Trạng thái sản phẩm đã được cập nhật.');
    }

    /**
     * Export stock
     */
    public function export(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'so_luong'   => 'required|integer|min:1',
            'ly_do'      => 'required|string|max:255',
        ]);

        DB::transaction(function () use ($validated) {
            $product     = Product::findOrFail($validated['product_id']);
            $oldQuantity = $product->so_luong;

            if ($oldQuantity < $validated['so_luong']) {
                throw new \Exception('Số lượng xuất kho vượt quá tồn kho hiện tại!');
            }

            $newQuantity = $oldQuantity - $validated['so_luong'];
            $product->update(['so_luong' => $newQuantity]);
            $this->syncProductStatus($product->fresh());

            InventoryLog::create([
                'product_id'        => $product->id,
                'loai'              => 'out',
                'so_luong_truoc'    => $oldQuantity,
                'so_luong_thay_doi' => -$validated['so_luong'],
                'so_luong_sau'      => $newQuantity,
                'ly_do'             => $validated['ly_do'],
                'user_id'           => Auth::id(),
            ]);
        });

        return back()->with('success', 'Xuất kho thành công! Trạng thái sản phẩm đã được cập nhật.');
    }

    /**
     * Adjust stock
     */
    public function adjust(Request $request)
    {
        $validated = $request->validate([
            'product_id'   => 'required|exists:products,id',
            'so_luong_moi' => 'required|integer|min:0',
            'ly_do'        => 'required|string|max:255',
        ]);

        DB::transaction(function () use ($validated) {
            $product     = Product::findOrFail($validated['product_id']);
            $oldQuantity = $product->so_luong;
            $newQuantity = $validated['so_luong_moi'];
            $change      = $newQuantity - $oldQuantity;

            $product->update(['so_luong' => $newQuantity]);
            $this->syncProductStatus($product->fresh());

            InventoryLog::create([
                'product_id'        => $product->id,
                'loai'              => 'adjust',
                'so_luong_truoc'    => $oldQuantity,
                'so_luong_thay_doi' => $change,
                'so_luong_sau'      => $newQuantity,
                'ly_do'             => $validated['ly_do'],
                'user_id'           => Auth::id(),
            ]);
        });

        return back()->with('success', 'Điều chỉnh tồn kho thành công! Trạng thái sản phẩm đã được cập nhật.');
    }
}
