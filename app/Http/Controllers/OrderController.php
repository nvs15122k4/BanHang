<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\InventoryLog;
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

        // Hoàn kho khi hủy
        if ($oldStatus !== 'cancelled' && $newStatus === 'cancelled') {
            $this->restoreInventory($order);
        }

        $order->update(['trang_thai' => $newStatus]);

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
            'trang_thai_thanh_toan' => 'required|in:unpaid,paid',
        ]);

        $oldStatus = $order->trang_thai_thanh_toan;

        $order->update(['trang_thai_thanh_toan' => $request->trang_thai_thanh_toan]);

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
    public function cancel(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        // Chỉ cho phép hủy khi đang ở trạng thái pending (Chờ duyệt)
        if ($order->trang_thai !== 'pending') {
            return back()->with('error', 'Chỉ có thể hủy đơn hàng khi đang ở trạng thái "Chờ duyệt"!');
        }

        $order->update([
            'previous_trang_thai' => $order->trang_thai,
            'trang_thai' => 'cancelling',
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
            'phuong_thuc_thanh_toan' => 'required|in:vietqr,bank_transfer,vnpay',
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
                'trang_thai' => 'pending',
                'phuong_thuc_thanh_toan' => $validated['phuong_thuc_thanh_toan'],
                'trang_thai_thanh_toan' => 'unpaid',
                'ghi_chu' => $validated['ghi_chu'] ?? null,
            ]);

            // Create order items
            foreach ($items as $item) {
                $order->orderItems()->create($item);
            }

            DB::commit();

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

        $order->update(array_merge($validated, ['refund_status' => 'pending']));

        return redirect()->route('orders.show', $order)->with('success', 'Thông tin hoàn tiền đã được gửi. Admin sẽ xử lý sớm nhất!');
    }

    /**
     * Admin đồng ý hủy đơn hàng
     */
    public function approveCancel(Order $order)
    {
        DB::beginTransaction();
        try {
            $oldStatus = $order->trang_thai;
            
            // Hoàn tồn kho
            $this->restoreInventory($order);

            $updateData = ['trang_thai' => 'cancelled'];

            // Nếu đã thanh toán, yêu cầu hoàn tiền
            if ($order->trang_thai_thanh_toan === 'paid') {
                $updateData['refund_status'] = 'pending';
                $message = 'Đơn hàng đã được chấp nhận hủy và cần cung cấp thông tin nhận hoàn tiền.';
            } else {
                $message = 'Đơn hàng đã được hủy thành công.';
            }

            $order->update($updateData);

            try {
                $order->user->notify(new OrderStatusUpdated($order, $oldStatus, 'cancelled'));
                if ($order->trang_thai_thanh_toan === 'paid') {
                    // Bạn có thể tạo thêm notification riêng cho việc hoàn tiền ở đây
                }
            } catch (\Exception $e) {
                \Log::warning('Could not send notification: ' . $e->getMessage());
            }

            DB::commit();
            return back()->with('success', 'Đã chấp nhận hủy đơn hàng!');
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
        $oldStatus = $order->trang_thai;
        $revertStatus = $order->previous_trang_thai ?? 'pending';

        $order->update([
            'trang_thai' => $revertStatus,
            'previous_trang_thai' => null,
        ]);

        try {
            // Thông báo cho user
            // $order->user->notify(new OrderStatusUpdated($order, $oldStatus, $revertStatus));
        } catch (\Exception $e) {}

        return back()->with('success', 'Đã từ chối yêu cầu hủy. Đơn hàng quay lại trạng thái ' . $revertStatus);
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

        $order->update([
            'refund_status' => $request->refund_status,
            'refund_admin_note' => $request->refund_admin_note,
        ]);

        return back()->with('success', 'Cập nhật thông tin hoàn tiền thành công!');
    }
}
