<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Auth::user()->orders()
    ->with(['orderItems.product' => fn($q) => $q->withTrashed()->with('productImages')])
    ->orderBy('created_at', 'desc');

        if ($request->filled('trang_thai')) {
            $query->where('trang_thai', $request->trang_thai);
        }

        $orders = $query->paginate($request->input('per_page', 20));

        $orders->getCollection()->transform(function ($o) {
            return $this->transformOrder($o);
        });

        return response()->json($orders);
    }

    public function show(Order $order): JsonResponse
    {
        if ($order->user_id !== Auth::id()) {
            return response()->json(['message' => 'Không có quyền xem đơn hàng này'], 403);
        }

$order->load(['orderItems.product' => fn($q) => $q->withTrashed()->with('productImages')]);

        return response()->json(['data' => $this->transformOrder($order)]);
    }

    public function cancel(Order $order): JsonResponse
    {
        if ($order->user_id !== Auth::id()) {
            return response()->json(['message' => 'Không có quyền'], 403);
        }

        if (!in_array($order->trang_thai, [Order::STATUS_PENDING, Order::STATUS_CONFIRMED], true)) {
            return response()->json(['message' => 'Chỉ có thể hủy đơn hàng đang chờ duyệt hoặc đang chuẩn bị hàng'], 400);
        }

        // Nếu đơn đã duyệt (confirmed), hoàn lại stock đã trừ
        if ($order->trang_thai === Order::STATUS_CONFIRMED) {
            foreach ($order->orderItems as $item) {
                $item->product?->increment('so_luong', $item->so_luong);
            }
        }

        $order->update(['trang_thai' => Order::STATUS_CANCELLED]);

        return response()->json(['success' => true, 'message' => 'Đã hủy đơn hàng!']);
    }

    public function updateStatusByUser(Request $request, Order $order): JsonResponse
    {
        if ($order->user_id !== Auth::id()) {
            return response()->json(['message' => 'Không có quyền'], 403);
        }

        $request->validate(['trang_thai' => 'required|in:completed,disputing']);

        $allowed = Order::userNextStatuses($order->trang_thai);
        if (!in_array($request->trang_thai, $allowed)) {
            return response()->json(['message' => 'Không thể cập nhật trạng thái này'], 400);
        }

        $order->update(['trang_thai' => $request->trang_thai]);

        return response()->json(['success' => true, 'message' => 'Cập nhật thành công!']);
    }

    private function transformOrder(Order $o): array
    {
        return [
            'id' => $o->id,
            'ma_don_hang' => $o->ma_don_hang,
            'user_id' => $o->user_id,
            'ten_nguoi_nhan' => $o->ten_nguoi_nhan,
            'sdt_nguoi_nhan' => $o->sdt_nguoi_nhan,
            'dia_chi_giao_hang' => $o->dia_chi_giao_hang,
            'tong_tien' => (float) $o->tong_tien,
            'phi_van_chuyen' => (float) $o->phi_van_chuyen,
            'giam_gia' => (float) $o->giam_gia,
            'thanh_tien' => (float) $o->thanh_tien,
            'trang_thai' => $o->trang_thai,
            'phuong_thuc_thanh_toan' => $o->phuong_thuc_thanh_toan,
            'trang_thai_thanh_toan' => $o->trang_thai_thanh_toan,
            'ghi_chu' => $o->ghi_chu,
            'status_label' => $o->status_label,
            'user_status_label' => $o->user_status_label,
            'status_color' => $o->status_color,
            'payment_method_label' => $o->payment_method_label,
            'payment_status_label' => $o->payment_status_label,
            'vietqr_url' => $o->phuong_thuc_thanh_toan === 'vietqr' ? $o->vietqr_url : null,
            'timeline_index' => $o->timeline_index,
            'reason_cancel' => $o->reason_cancel,
            'order_items' => $o->orderItems->map(fn($i) => [
                'id' => $i->id,
                'product_id' => $i->product_id,
                'ten_san_pham' => $i->ten_san_pham,
                'gia' => (float) $i->gia,
                'so_luong' => $i->so_luong,
                'thanh_tien' => (float) $i->thanh_tien,
                'size' => $i->size,
            ]),
            'created_at' => $o->created_at,
            'updated_at' => $o->updated_at,
        ];
    }
}
