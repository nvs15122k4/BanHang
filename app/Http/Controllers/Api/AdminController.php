<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard(): JsonResponse
    {
        $stats = [
            'total_users' => User::count(),
            'total_products' => Product::count(),
            'active_products' => Product::where('trang_thai', 'con')->count(),
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('trang_thai', Order::STATUS_PENDING)->count(),
            'confirmed_orders' => Order::where('trang_thai', Order::STATUS_CONFIRMED)->count(),
            'shipping_orders' => Order::where('trang_thai', Order::STATUS_SHIPPING)->count(),
            'completed_orders' => Order::where('trang_thai', Order::STATUS_COMPLETED)->count(),
            'cancelled_orders' => Order::where('trang_thai', Order::STATUS_CANCELLED)->count(),
            'revenue' => Order::where('trang_thai', Order::STATUS_COMPLETED)->sum('thanh_tien'),
            'today_orders' => Order::whereDate('created_at', today())->count(),
            'today_revenue' => Order::whereDate('created_at', today())->sum('thanh_tien'),
        ];

        $recentOrders = Order::with('user')->orderBy('created_at', 'desc')->take(5)->get();
        $lowStockProducts = Product::where('so_luong', '<', 10)->orderBy('so_luong')->take(10)->get();

        return response()->json([
            'data' => $stats,
            'recent_orders' => $recentOrders,
            'low_stock_products' => $lowStockProducts,
        ]);
    }

    public function orders(Request $request): JsonResponse
    {
        $query = Order::with(['user', 'orderItems'])->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            $query->where('trang_thai', $request->status);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('ma_don_hang', 'like', "%{$s}%")
                    ->orWhere('ten_nguoi_nhan', 'like', "%{$s}%");
            });
        }

        return response()->json($query->paginate(20));
    }

    public function updateOrderStatus(Request $request, Order $order): JsonResponse
    {
        $allowedStatuses = array_keys(Order::adminStatusLabels());
        $request->validate([
            'trang_thai' => ['required', 'in:' . implode(',', $allowedStatuses)],
        ]);

        $oldStatus = $order->trang_thai;
        $newStatus = $request->trang_thai;

        if (!in_array($newStatus, Order::adminNextStatuses($oldStatus), true)) {
            return response()->json(['message' => 'Không thể chuyển trạng thái này'], 400);
        }

        // Nếu hủy đơn và đã trừ stock → hoàn stock
        if ($newStatus === Order::STATUS_CANCELLED && $oldStatus === Order::STATUS_CONFIRMED) {
            foreach ($order->orderItems as $item) {
                $item->product?->increment('so_luong', $item->so_luong);
            }
        }

        $order->update([
            'trang_thai' => $newStatus,
            'previous_trang_thai' => $oldStatus,
        ]);

        return response()->json(['success' => true, 'message' => 'Đã cập nhật trạng thái đơn hàng!']);
    }
}
