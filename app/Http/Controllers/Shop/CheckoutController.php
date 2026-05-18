<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    public function __construct(protected OrderService $orderService) {}

    /**
     * Show checkout page
     */
    public function index()
    {
        $user      = Auth::user();
        $cartItems = $user->carts()->with('product')->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng của bạn đang trống!');
        }

        $addresses = $user->addresses()->orderBy('is_default', 'desc')->get();

        // Calculate totals
        $subtotal = $cartItems->sum(function($item) {
            return ($item->product->promo_price ?? $item->product->gia) * $item->so_luong;
        });

        $shippingFee = 0; // free shipping
        $discount    = 0;
        $totalAmount = $subtotal + $shippingFee - $discount;

        return view('checkout.index', compact('cartItems', 'addresses', 'subtotal', 'shippingFee', 'discount', 'totalAmount'));
    }

    /**
     * Store new order from checkout
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'recipient_name'         => 'required|string|max:255',
            'phone'                  => 'required|string|max:20',
            'dia_chi_giao_hang'      => 'required|string',
            'phuong_thuc_thanh_toan' => 'required|in:vietqr,bank_transfer',
            'ghi_chu'                => 'nullable|string',
        ], [
            'recipient_name.required' => 'Vui lòng nhập tên người nhận',
            'phone.required'          => 'Vui lòng nhập số điện thoại',
            'dia_chi_giao_hang.required' => 'Vui lòng nhập địa chỉ giao hàng',
            'phuong_thuc_thanh_toan.required' => 'Vui lòng chọn phương thức thanh toán',
        ]);

        $user      = Auth::user();
        $cartItems = $user->carts()->with('product')->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng của bạn đang trống!');
        }

        try {
            $order = $this->orderService->createOrderFromCart($user, $validated, $cartItems);

            return redirect()->route('orders.show', $order)->with('success', 'Đặt hàng thành công!');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }
}
