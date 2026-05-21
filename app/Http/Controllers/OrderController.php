<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\InventoryLog;
use App\Models\AuditLog;
use App\Notifications\OrderStatusUpdated;
use App\Notifications\PaymentStatusUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display orders list (Admin)
     */
    public function index(Request $request)
    {
        $query = Order::with(['user', 'orderItems'])->orderBy('created_at', 'desc');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('ma_don_hang', 'like', "%{$search}%")
                  ->orWhere('ten_nguoi_nhan', 'like', "%{$search}%")
                  ->orWhere('sdt_nguoi_nhan', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('trang_thai', $request->status);
        }

        // Filter by payment status
        if ($request->filled('payment_status')) {
            $query->where('trang_thai_thanh_toan', $request->payment_status);
        }

        $orders = $query->paginate(15);

        // Statistics
        $stats = [
            'total'         => Order::count(),
            'pending'       => Order::where('trang_thai', 'pending')->count(),
            'confirmed'     => Order::where('trang_thai', 'confirmed')->count(),
            'shipping'      => Order::where('trang_thai', 'shipping')->count(),
            'disputing'     => Order::where('trang_thai', 'disputing')->count(),
            'completed'     => Order::where('trang_thai', 'completed')->count(),
            'cancelling'    => Order::where('trang_thai', 'cancelling')->count(),
            'cancelled'     => Order::where('trang_thai', 'cancelled')->count(),
            'total_revenue' => Order::where('trang_thai', 'completed')->sum('thanh_tien'),
        ];

        return view('admin.orders.index', compact('orders', 'stats'));
    }

    /**
     * Show order detail
     */
    public function show(Order $order)
    {
        $order->load(['user', 'orderItems.product', 'inventoryLogs']);
        
        return view('admin.orders.show', compact('order'));
    }

    /**
     * Update order status (Admin)
     */
    public function updateStatus(Request $request, Order $order)
    {
        $allowedStatuses = array_keys(Order::adminStatusLabels());

        $request->validate([
            'trang_thai' => ['required', 'in:' . implode(',', $allowedStatuses)],
        ]);

        $oldStatus = $order->trang_thai;
        $newStatus = $request->trang_thai;

        // Kiểm tra chuyển trạng thái hợp lệ (đã được nới lỏng theo yêu cầu hiển thị tất cả trạng thái)
        // $adminNext = Order::adminNextStatuses($oldStatus);
        // if (!empty($adminNext) && !in_array($newStatus, $adminNext)) {
        //     return back()->with('error', 'Không thể chuyển từ "' . $order->status_label . '" sang trạng thái này!');
        // }

        // Hoàn kho khi hủy (Chỉ hoàn nếu đã từng trừ kho - tức là đã confirmed, shipping, delivered)
        if ($newStatus === Order::STATUS_CANCELLED && $this->inventoryWasDeducted($order)) {
            $this->restoreInventory($order);
            
            // Nếu đã thanh toán, chuyển sang chờ hoàn tiền
            if ($order->trang_thai_thanh_toan === Order::PAYMENT_PAID && $order->refund_status === Order::REFUND_STATUS_NONE) {
                $order->update(['refund_status' => Order::REFUND_STATUS_PENDING]);
            }
        }

        // Trừ kho khi duyệt đơn
        if ($oldStatus === Order::STATUS_PENDING && $newStatus === Order::STATUS_CONFIRMED && ! $this->inventoryWasDeducted($order)) {
            $this->deductInventory($order);
        }

        $order->update(['trang_thai' => $newStatus]);
        AuditLog::record('order_status_updated', $order, "Cập nhật trạng thái đơn {$order->ma_don_hang}", [
            'trang_thai' => $oldStatus,
        ], [
            'trang_thai' => $newStatus,
        ]);

        if ($oldStatus !== $newStatus) {
            try {
                $order->user->notify(new OrderStatusUpdated($order, $oldStatus, $newStatus));
            } catch (\Exception $e) {
                \Log::warning('Could not send order status notification: ' . $e->getMessage());
            }
        }

        return back()->with('success', 'Cập nhật trạng thái đơn hàng thành công!');
    }

    /**
     * Update payment status
     */
    public function updatePaymentStatus(Request $request, Order $order)
    {
        $request->validate([
            'trang_thai_thanh_toan' => 'required|in:' . implode(',', array_keys(Order::paymentStatusLabels())),
        ]);

        $oldStatus = $order->trang_thai_thanh_toan;

        $order->update(['trang_thai_thanh_toan' => $request->trang_thai_thanh_toan]);
        AuditLog::record('payment_status_updated', $order, "Cập nhật thanh toán đơn {$order->ma_don_hang}", [
            'trang_thai_thanh_toan' => $oldStatus,
        ], [
            'trang_thai_thanh_toan' => $request->trang_thai_thanh_toan,
        ]);

        // Gửi thông báo khi trạng thái thanh toán thay đổi
        if ($oldStatus !== $request->trang_thai_thanh_toan) {
            try {
                $order->user->notify(new PaymentStatusUpdated($order, $request->trang_thai_thanh_toan));
            } catch (\Exception $e) {
                \Log::warning('Could not send payment status notification: ' . $e->getMessage());
            }
        }

        return back()->with('success', 'Cập nhật trạng thái thanh toán thành công!');
    }

    /**
     * Deduct inventory when order is confirmed
     */
    private function deductInventory(Order $order)
    {
        DB::transaction(function () use ($order) {
            foreach ($order->orderItems as $item) {
                $product = $item->product;
                
                if ($product) {
                    $oldQuantity = $product->so_luong;
                    $newQuantity = $oldQuantity - $item->so_luong;
                    
                    $product->update(['so_luong' => max(0, $newQuantity)]);
                    
                    // Log inventory change
                    InventoryLog::create([
                        'product_id' => $product->id,
                        'loai' => 'out',
                        'so_luong_truoc' => $oldQuantity,
                        'so_luong_thay_doi' => -$item->so_luong,
                        'so_luong_sau' => $newQuantity,
                        'ly_do' => "Xuất kho cho đơn hàng #{$order->ma_don_hang}",
                        'order_id' => $order->id,
                        'user_id' => Auth::id(),
                    ]);
                }
            }
        });
    }

    /**
     * Restore inventory when order is cancelled
     */
    private function restoreInventory(Order $order)
    {
        DB::transaction(function () use ($order) {
            foreach ($order->orderItems as $item) {
                $product = $item->product;
                
                if ($product) {
                    $oldQuantity = $product->so_luong;
                    $newQuantity = $oldQuantity + $item->so_luong;
                    
                    $product->update(['so_luong' => $newQuantity]);
                    
                    // Log inventory change
                    InventoryLog::create([
                        'product_id' => $product->id,
                        'loai' => 'in',
                        'so_luong_truoc' => $oldQuantity,
                        'so_luong_thay_doi' => $item->so_luong,
                        'so_luong_sau' => $newQuantity,
                        'ly_do' => "Hoàn kho do hủy đơn hàng #{$order->ma_don_hang}",
                        'order_id' => $order->id,
                        'user_id' => Auth::id(),
                    ]);
                }
            }
        });
    }

    private function inventoryWasDeducted(Order $order): bool
    {
        return (int) InventoryLog::where('order_id', $order->id)->sum('so_luong_thay_doi') < 0;
    }

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

    /**
     * Create new order (for testing)
     */
    public function create()
    {
        $products = Product::where('trang_thai', 'active')->where('so_luong', '>', 0)->get();
        return view('admin.orders.create', compact('products'));
    }

    /**
     * Store new order
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'ten_nguoi_nhan' => 'required|string|max:255',
            'sdt_nguoi_nhan' => 'required|string|max:20',
            'dia_chi_giao_hang' => 'required|string',
            'phuong_thuc_thanh_toan' => 'required|in:vietqr,cod,vnpay',
            'ghi_chu' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.so_luong' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            // Calculate totals
            $tongTien = 0;
            $items = [];
            
            foreach ($validated['items'] as $item) {
                $product = Product::find($item['product_id']);
                $thanhTien = $product->gia * $item['so_luong'];
                $tongTien += $thanhTien;
                
                $items[] = [
                    'product_id' => $product->id,
                    'ten_san_pham' => $product->ten_sp,
                    'gia' => $product->gia,
                    'so_luong' => $item['so_luong'],
                    'thanh_tien' => $thanhTien,
                ];
            }

            // Create order
            $order = Order::create([
                'ma_don_hang' => Order::generateOrderCode(),
                'user_id' => $validated['user_id'],
                'ten_nguoi_nhan' => $validated['ten_nguoi_nhan'],
                'sdt_nguoi_nhan' => $validated['sdt_nguoi_nhan'],
                'dia_chi_giao_hang' => $validated['dia_chi_giao_hang'],
                'tong_tien' => $tongTien,
                'phi_van_chuyen' => 0,
                'giam_gia' => 0,
                'thanh_tien' => $tongTien,
                'trang_thai' => Order::STATUS_PENDING,
                'phuong_thuc_thanh_toan' => $validated['phuong_thuc_thanh_toan'],
                'trang_thai_thanh_toan' => Order::PAYMENT_UNPAID,
                'ghi_chu' => $validated['ghi_chu'] ?? null,
            ]);

            // Create order items
            foreach ($items as $item) {
                $order->orderItems()->create($item);
            }

            DB::commit();
            AuditLog::record('admin_order_created', $order, "Admin tao don {$order->ma_don_hang}", null, [
                'trang_thai' => Order::STATUS_PENDING,
                'trang_thai_thanh_toan' => Order::PAYMENT_UNPAID,
                'thanh_tien' => $order->thanh_tien,
            ]);

            return redirect()->route('admin.orders.show', $order)->with('success', 'Tạo đơn hàng thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage())->withInput();
        }
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

        $order->update(array_merge($validated, ['refund_status' => Order::REFUND_STATUS_PENDING]));
        AuditLog::record('refund_info_submitted', $order, "Khách gửi thông tin hoàn tiền đơn {$order->ma_don_hang}", null, [
            'refund_bank_name' => $validated['refund_bank_name'],
            'refund_account_number' => $validated['refund_account_number'],
            'refund_status' => Order::REFUND_STATUS_PENDING,
        ]);

        return redirect()->route('orders.show', $order)->with('success', 'Thông tin hoàn tiền đã được gửi. Admin sẽ xử lý sớm nhất!');
    }

    /**
     * Admin đồng ý hủy đơn hàng
     */
    public function approveCancel(Order $order)
    {
        if ($order->trang_thai !== 'cancelling') {
            return back()->with('error', 'Đơn hàng không ở trạng thái chờ duyệt hủy!');
        }

        DB::beginTransaction();
        try {
            $oldStatus = $order->trang_thai;
            
            // Hoàn tồn kho (Chỉ hoàn nếu đã từng trừ kho)
            // Vì inventory được trừ ngay khi checkout (trạng thái pending), nên cần hoàn kho cho tất cả các trạng thái này
            if ($this->inventoryWasDeducted($order)) {
                $this->restoreInventory($order);
            }

            $updateData = [
                'trang_thai' => Order::STATUS_CANCELLED,
                'previous_trang_thai' => null,
            ];

            // Nếu đã thanh toán, yêu cầu hoàn tiền
            $message = 'Đơn hàng đã được hủy thành công.';
            if ($order->trang_thai_thanh_toan === Order::PAYMENT_PAID) {
                $updateData['refund_status'] = Order::REFUND_STATUS_PENDING;
                $message = 'Đơn hàng đã được chấp nhận hủy. Vui lòng kiểm tra và xử lý hoàn tiền cho khách hàng.';
            }

            $order->update($updateData);
            AuditLog::record('order_cancel_approved', $order, "Duyệt hủy đơn {$order->ma_don_hang}", [
                'trang_thai' => $oldStatus,
            ], $updateData);

            try {
                $order->user->notify(new OrderStatusUpdated($order, $oldStatus, 'cancelled'));
                if ($order->trang_thai_thanh_toan === Order::PAYMENT_PAID) {
                    // Bạn có thể tạo thêm notification riêng cho việc hoàn tiền ở đây
                }
            } catch (\Exception $e) {
                \Log::warning('Could not send notification: ' . $e->getMessage());
            }

            DB::commit();
            return back()->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Admin từ chối hủy đơn hàng
     */
    public function rejectCancel(Order $order)
    {
        if ($order->trang_thai !== 'cancelling') {
            return back()->with('error', 'Đơn hàng không ở trạng thái chờ duyệt hủy!');
        }

        $oldStatus = $order->trang_thai;
        $revertStatus = $order->previous_trang_thai ?? 'pending';

        $order->update([
            'trang_thai' => $revertStatus,
            'previous_trang_thai' => null,
        ]);
        AuditLog::record('order_cancel_rejected', $order, "Từ chối hủy đơn {$order->ma_don_hang}", [
            'trang_thai' => $oldStatus,
        ], [
            'trang_thai' => $revertStatus,
        ]);

        try {
            // Thông báo cho user
            $order->user->notify(new OrderStatusUpdated($order, $oldStatus, $revertStatus));
        } catch (\Exception $e) {
            \Log::warning('Could not send notification: ' . $e->getMessage());
        }

        return back()->with('success', 'Đã từ chối yêu cầu hủy. Đơn hàng quay lại trạng thái ' . (Order::adminStatusLabels()[$revertStatus] ?? $revertStatus));
    }

    /**
     * Admin cập nhật trạng thái hoàn tiền
     */
    public function updateRefund(Request $request, Order $order)
    {
        $request->validate([
            'refund_status' => 'required|in:pending,completed',
            'refund_admin_note' => 'nullable|string',
        ]);

        $oldRefundStatus = $order->refund_status;

        $order->update([
            'refund_status' => $request->refund_status,
            'refund_admin_note' => $request->refund_admin_note,
            'trang_thai_thanh_toan' => $request->refund_status === Order::REFUND_STATUS_COMPLETED
                ? Order::PAYMENT_REFUNDED
                : $order->trang_thai_thanh_toan,
        ]);
        AuditLog::record('refund_status_updated', $order, "Cập nhật hoàn tiền đơn {$order->ma_don_hang}", [
            'refund_status' => $oldRefundStatus,
        ], [
            'refund_status' => $request->refund_status,
            'refund_admin_note' => $request->refund_admin_note,
        ]);

        if ($oldRefundStatus !== $request->refund_status) {
            try {
                $order->user->notify(new \App\Notifications\RefundStatusUpdated($order, $request->refund_status));
            } catch (\Exception $e) {
                \Log::warning('Could not send refund status notification: ' . $e->getMessage());
            }
        }

        return back()->with('success', 'Cập nhật thông tin hoàn tiền thành công!');
    }
}
