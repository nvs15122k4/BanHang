<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function __construct(protected OrderService $orderService) {}

    /**
     * Đơn hàng của tôi (Customer)
     */
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

    /**
     * Chi tiết đơn hàng của tôi (Customer)
     */
    public function myOrderShow(Order $order)
    {
        // Chỉ cho xem đơn hàng của chính mình
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền xem đơn hàng này!');
        }

        $order->load(['orderItems.product']);

        return view('orders.show', compact('order'));
    }

    /**
     * Hủy đơn hàng (Customer) - Gửi yêu cầu hủy
     */
    public function cancel(Request $request, Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền thực hiện hành động này!');
        }

        // Chỉ cho phép hủy khi đang ở trạng thái pending (Chờ duyệt)
        if ($order->trang_thai !== 'pending') {
            return back()->with('error', 'Chỉ có thể hủy đơn hàng khi đang ở trạng thái "Chờ duyệt"!');
        }

        $this->orderService->requestCancelByUser($order);

        return back()->with('success', 'Yêu cầu hủy đơn hàng đã được gửi và đang chờ Admin duyệt!');
    }

    /**
     * User tự cập nhật trạng thái (xác nhận đã nhận / khiếu nại)
     */
    public function updateStatusByUser(Request $request, Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate(['trang_thai' => 'required|string']);

        $newStatus = $request->trang_thai;
        $oldStatus = $order->trang_thai;
        $userNext  = Order::userNextStatuses($oldStatus);

        if (!in_array($newStatus, $userNext)) {
            return back()->with('error', 'Hành động không hợp lệ ở trạng thái hiện tại!');
        }

        $message = $this->orderService->updateStatusByUser($order, $newStatus);

        return back()->with('success', $message);
    }

    /**
     * Hiển thị trang nhập thông tin hoàn tiền (Customer)
     */
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

    /**
     * Xử lý gửi thông tin hoàn tiền (Customer)
     */
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

        $order->update(array_merge($validated, ['refund_status' => 'pending']));

        return redirect()->route('orders.show', $order)->with('success', 'Thông tin hoàn tiền đã được gửi. Admin sẽ xử lý sớm nhất!');
    }
}
