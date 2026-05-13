<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\InventoryLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    /**
     * Hiển thị trang checkout
     */
    public function index()
    {
        $cart = session('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng của bạn đang trống!');
        }

        $items = [];
        $total = 0;

        foreach ($cart as $productId => $item) {
            $product = Product::find($productId);
            if ($product) {
                $subtotal = $product->gia * $item['so_luong'];
                $total   += $subtotal;
                $items[]  = [
                    'product'  => $product,
                    'so_luong' => $item['so_luong'],
                    'subtotal' => $subtotal,
                ];
            }
        }

        $addresses = Auth::user()->addresses()->orderBy('is_default', 'desc')->get();

        return view('checkout.index', compact('items', 'total', 'addresses'));
    }

    /**
     * Xử lý đặt hàng
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ten_nguoi_nhan'        => 'required|string|max:255',
            'sdt_nguoi_nhan'        => 'required|string|max:20',
            'dia_chi_giao_hang'     => 'required|string',
            'phuong_thuc_thanh_toan'=> 'required|in:cod,bank_transfer',
            'ghi_chu'               => 'nullable|string|max:500',
        ], [
            'ten_nguoi_nhan.required'         => 'Vui lòng nhập tên người nhận',
            'sdt_nguoi_nhan.required'         => 'Vui lòng nhập số điện thoại',
            'dia_chi_giao_hang.required'      => 'Vui lòng nhập địa chỉ giao hàng',
            'phuong_thuc_thanh_toan.required' => 'Vui lòng chọn phương thức thanh toán',
        ]);

        $cart = session('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng của bạn đang trống!');
        }

        DB::beginTransaction();
        try {
            $tongTien = 0;
            $orderItems = [];

            // Kiểm tra tồn kho và tính tổng tiền
            foreach ($cart as $productId => $item) {
                $product = Product::lockForUpdate()->find($productId);

                if (!$product) {
                    throw new \Exception("Sản phẩm không tồn tại!");
                }
                if ($product->so_luong < $item['so_luong']) {
                    throw new \Exception("Sản phẩm \"{$product->ten_sp}\" chỉ còn {$product->so_luong} trong kho!");
                }

                $subtotal   = $product->gia * $item['so_luong'];
                $tongTien  += $subtotal;

                $orderItems[] = [
                    'product'    => $product,
                    'so_luong'   => $item['so_luong'],
                    'gia'        => $product->gia,
                    'subtotal'   => $subtotal,
                ];
            }

            // Tạo đơn hàng
            $order = Order::create([
                'ma_don_hang'            => Order::generateOrderCode(),
                'user_id'                => Auth::id(),
                'ten_nguoi_nhan'         => $validated['ten_nguoi_nhan'],
                'sdt_nguoi_nhan'         => $validated['sdt_nguoi_nhan'],
                'dia_chi_giao_hang'      => $validated['dia_chi_giao_hang'],
                'tong_tien'              => $tongTien,
                'phi_van_chuyen'         => 0,
                'giam_gia'               => 0,
                'thanh_tien'             => $tongTien,
                'trang_thai'             => 'pending',
                'phuong_thuc_thanh_toan' => $validated['phuong_thuc_thanh_toan'],
                'trang_thai_thanh_toan'  => 'unpaid',
                'ghi_chu'                => $validated['ghi_chu'] ?? null,
            ]);

            // Tạo order items và trừ tồn kho
            foreach ($orderItems as $item) {
                $product = $item['product'];

                OrderItem::create([
                    'order_id'    => $order->id,
                    'product_id'  => $product->id,
                    'ten_san_pham'=> $product->ten_sp,
                    'gia'         => $item['gia'],
                    'so_luong'    => $item['so_luong'],
                    'thanh_tien'  => $item['subtotal'],
                ]);

                // Trừ tồn kho
                $oldQty = $product->so_luong;
                $newQty = $oldQty - $item['so_luong'];
                $product->update(['so_luong' => $newQty]);

                // Ghi log kho
                InventoryLog::create([
                    'product_id'       => $product->id,
                    'loai'             => 'out',
                    'so_luong_truoc'   => $oldQty,
                    'so_luong_thay_doi'=> -$item['so_luong'],
                    'so_luong_sau'     => $newQty,
                    'ly_do'            => "Xuất kho cho đơn hàng #{$order->ma_don_hang}",
                    'order_id'         => $order->id,
                    'user_id'          => Auth::id(),
                ]);
            }

            // Xóa giỏ hàng
            session()->forget('cart');

            DB::commit();

            return redirect()->route('orders.show', $order->id)
                ->with('success', "Đặt hàng thành công! Mã đơn hàng: {$order->ma_don_hang}");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage())->withInput();
        }
    }
}
