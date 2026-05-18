<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function __construct(protected OrderService $orderService) {}

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

        $this->orderService->updateStatus($order, $request->trang_thai);

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

        $this->orderService->updatePaymentStatus($order, $request->trang_thai_thanh_toan);

        return back()->with('success', 'Cập nhật trạng thái thanh toán thành công!');
    }

    /**
     * Create new order (for testing)
     */
    public function create()
    {
        $products = Product::where('trang_thai', 'con')->where('so_luong', '>', 0)->get();
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
            'phuong_thuc_thanh_toan' => 'required|in:vietqr,bank_transfer',
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
     * Admin đồng ý hủy đơn hàng
     */
    public function approveCancel(Order $order)
    {
        if ($order->trang_thai !== 'cancelling') {
            return back()->with('error', 'Đơn hàng không ở trạng thái chờ duyệt hủy!');
        }

        $message = $this->orderService->approveCancel($order);

        return back()->with('success', $message);
    }

    /**
     * Admin từ chối hủy đơn hàng
     */
    public function rejectCancel(Order $order)
    {
        if ($order->trang_thai !== 'cancelling') {
            return back()->with('error', 'Đơn hàng không ở trạng thái chờ duyệt hủy!');
        }

        $message = $this->orderService->rejectCancel($order);

        return back()->with('success', $message);
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
