<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\InventoryLog;
use App\Models\Order;
use App\Models\Product;
use App\Notifications\OrderStatusUpdated;
use App\Notifications\PaymentStatusUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['user', 'orderItems'])->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ma_don_hang', 'like', "%{$search}%")
                    ->orWhere('ten_nguoi_nhan', 'like', "%{$search}%")
                    ->orWhere('sdt_nguoi_nhan', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('trang_thai', $request->status);
        }

        if ($request->filled('payment_status')) {
            $query->where('trang_thai_thanh_toan', $request->payment_status);
        }

        $orders = $query->paginate(15);
        $stats = [
            'total' => Order::count(),
            'pending' => Order::where('trang_thai', 'pending')->count(),
            'confirmed' => Order::where('trang_thai', 'confirmed')->count(),
            'shipping' => Order::where('trang_thai', 'shipping')->count(),
            'disputing' => Order::where('trang_thai', 'disputing')->count(),
            'completed' => Order::where('trang_thai', 'completed')->count(),
            'cancelling' => Order::where('trang_thai', 'cancelling')->count(),
            'cancelled' => Order::where('trang_thai', 'cancelled')->count(),
            'total_revenue' => Order::where('trang_thai', 'completed')->sum('thanh_tien'),
        ];

        return view('admin.orders.index', compact('orders', 'stats'));
    }

    public function show(Order $order)
    {
        $order->load(['user', 'orderItems.product', 'inventoryLogs']);

        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $allowedStatuses = array_keys(Order::adminStatusLabels());

        $request->validate([
            'trang_thai' => ['required', 'in:' . implode(',', $allowedStatuses)],
        ]);

        $oldStatus = $order->trang_thai;
        $newStatus = $request->trang_thai;

        if ($newStatus === Order::STATUS_CANCELLED && $this->inventoryWasDeducted($order)) {
            $this->restoreInventory($order);

            if ($order->trang_thai_thanh_toan === Order::PAYMENT_PAID && $order->refund_status === Order::REFUND_STATUS_NONE) {
                $order->update(['refund_status' => Order::REFUND_STATUS_PENDING]);
            }
        }

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

        if ($oldStatus !== $request->trang_thai_thanh_toan) {
            try {
                $order->user->notify(new PaymentStatusUpdated($order, $request->trang_thai_thanh_toan));
            } catch (\Exception $e) {
                \Log::warning('Could not send payment status notification: ' . $e->getMessage());
            }
        }

        return back()->with('success', 'Cập nhật trạng thái thanh toán thành công!');
    }

    public function create()
    {
        $products = Product::where('trang_thai', 'active')->where('so_luong', '>', 0)->get();

        return view('admin.orders.create', compact('products'));
    }

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

    public function approveCancel(Order $order)
    {
        if ($order->trang_thai !== 'cancelling') {
            return back()->with('error', 'Đơn hàng không ở trạng thái chờ duyệt hủy!');
        }

        DB::beginTransaction();
        try {
            $oldStatus = $order->trang_thai;

            if ($this->inventoryWasDeducted($order)) {
                $this->restoreInventory($order);
            }

            $updateData = [
                'trang_thai' => Order::STATUS_CANCELLED,
                'previous_trang_thai' => null,
            ];

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
            $order->user->notify(new OrderStatusUpdated($order, $oldStatus, $revertStatus));
        } catch (\Exception $e) {
            \Log::warning('Could not send notification: ' . $e->getMessage());
        }

        return back()->with('success', 'Đã từ chối yêu cầu hủy. Đơn hàng quay lại trạng thái ' . (Order::adminStatusLabels()[$revertStatus] ?? $revertStatus));
    }

    public function updateRefund(Request $request, Order $order)
    {
        $request->validate([
            'refund_status' => 'required|in:pending,completed',
            'refund_admin_note' => 'nullable|string',
        ]);

        $oldRefundStatus = $order->refund_status;
        $nextPaymentStatus = $order->trang_thai_thanh_toan;

        if ($request->refund_status === Order::REFUND_STATUS_COMPLETED) {
            $nextPaymentStatus = Order::PAYMENT_PAID;
        } elseif (
            $request->refund_status === Order::REFUND_STATUS_PENDING
            && $oldRefundStatus === Order::REFUND_STATUS_COMPLETED
        ) {
            $nextPaymentStatus = Order::PAYMENT_PAID;
        }

        $order->update([
            'refund_status' => $request->refund_status,
            'refund_admin_note' => $request->refund_admin_note,
            'trang_thai_thanh_toan' => $nextPaymentStatus,
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

    private function deductInventory(Order $order)
    {
        DB::transaction(function () use ($order) {
            foreach ($order->orderItems as $item) {
                $product = $item->product;

                if ($product) {
                    $oldQuantity = $product->so_luong;
                    $newQuantity = $oldQuantity - $item->so_luong;

                    $product->update(['so_luong' => max(0, $newQuantity)]);

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

    private function restoreInventory(Order $order)
    {
        DB::transaction(function () use ($order) {
            foreach ($order->orderItems as $item) {
                $product = $item->product;

                if ($product) {
                    $oldQuantity = $product->so_luong;
                    $newQuantity = $oldQuantity + $item->so_luong;

                    $product->update(['so_luong' => $newQuantity]);

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
}
