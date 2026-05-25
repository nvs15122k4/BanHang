<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Order;
use App\Notifications\OrderStatusUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function myOrders(Request $request)
    {
        $currentStatus = $request->get('status', '');
        $orders = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10);
        $reviews = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10);

        if ($currentStatus === 'reviewed') {
            $reviews = \App\Models\Review::where('user_id', Auth::id())
                ->with(['product'])
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        } else {
            $query = Auth::user()->orders()
                ->with(['orderItems.product'])
                ->orderBy('created_at', 'desc');

            if ($request->filled('status')) {
                $query->where('trang_thai', $request->status);
            }

            $orders = $query->paginate(10);
        }

        return view('orders.index', compact('orders', 'reviews'));
    }

    public function myOrderShow(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền xem đơn hàng này!');
        }

        $order->load(['orderItems.product']);

        return view('orders.show', compact('order'));
    }

    public function cancel(Request $request, Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền thực hiện hành động này!');
        }

        if ($order->trang_thai !== Order::STATUS_PENDING) {
            return back()->with('error', 'Chỉ có thể hủy đơn hàng khi đang ở trạng thái "Chờ duyệt"!');
        }

        $oldStatus = $order->trang_thai;

        $order->update([
            'previous_trang_thai' => $oldStatus,
            'trang_thai' => Order::STATUS_CANCELLING,
        ]);
        AuditLog::record('order_cancel_requested', $order, "Khách yêu cầu hủy đơn {$order->ma_don_hang}", [
            'trang_thai' => $oldStatus,
        ], [
            'trang_thai' => Order::STATUS_CANCELLING,
        ]);

        return back()->with('success', 'Yêu cầu hủy đơn hàng đã được gửi và đang chờ Admin duyệt!');
    }

    public function updateStatusByUser(Request $request, Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate(['trang_thai' => 'required|string']);

        $newStatus = $request->trang_thai;
        $oldStatus = $order->trang_thai;
        $userNext = Order::userNextStatuses($oldStatus);

        if (! in_array($newStatus, $userNext)) {
            return back()->with('error', 'Hành động không hợp lệ ở trạng thái hiện tại!');
        }

        $order->update(['trang_thai' => $newStatus]);
        AuditLog::record('customer_order_status_updated', $order, "Khách cập nhật trạng thái đơn {$order->ma_don_hang}", [
            'trang_thai' => $oldStatus,
        ], [
            'trang_thai' => $newStatus,
        ]);

        try {
            $order->user->notify(new OrderStatusUpdated($order, $oldStatus, $newStatus));
        } catch (\Exception $e) {
            \Log::warning('Could not send order status notification: ' . $e->getMessage());
        }

        $messages = [
            'completed' => 'Cảm ơn bạn đã xác nhận nhận hàng!',
            'disputing' => 'Khiếu nại của bạn đã được gửi. Chúng tôi sẽ xử lý sớm nhất.',
        ];

        return back()->with('success', $messages[$newStatus] ?? 'Cập nhật thành công!');
    }

    public function refundInfo(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        if ($order->refund_status === 'none') {
            return redirect()->route('orders.show', $order)->with('error', 'Đơn hàng này không yêu cầu thông tin hoàn tiền.');
        }

        return view('orders.refund', compact('order'));
    }

    public function submitRefundInfo(Request $request, Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'refund_bank_name' => 'required|string|max:255',
            'refund_account_number' => 'required|string|max:255',
            'refund_account_name' => 'required|string|max:255',
            'refund_user_note' => 'nullable|string',
        ]);

        $order->update(array_merge($validated, ['refund_status' => Order::REFUND_STATUS_PENDING]));
        AuditLog::record('refund_info_submitted', $order, "Khách gửi thông tin hoàn tiền đơn {$order->ma_don_hang}", null, [
            'refund_bank_name' => $validated['refund_bank_name'],
            'refund_account_number' => $validated['refund_account_number'],
            'refund_status' => Order::REFUND_STATUS_PENDING,
        ]);

        return redirect()->route('orders.show', $order)->with('success', 'Thông tin hoàn tiền đã được gửi. Admin sẽ xử lý sớm nhất!');
    }
}
