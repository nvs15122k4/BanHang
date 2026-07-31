<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'ten_nguoi_nhan' => 'required|string|max:255',
            'sdt_nguoi_nhan' => 'required|string|max:20',
            'dia_chi_giao_hang' => 'required|string',
            'phuong_thuc_thanh_toan' => 'required|in:cod,bank_transfer,vietqr',
            'ghi_chu' => 'nullable|string|max:1000',
        ]);

        $cartItems = CartItem::where('user_id', Auth::id())
            ->with('product')
            ->get();

        if ($cartItems->isEmpty()) {
            return response()->json(['message' => 'Giỏ hàng trống!'], 400);
        }

        $user = Auth::user();

        $order = DB::transaction(function () use ($request, $cartItems, $user) {
            $items = [];
            $total = 0;

            foreach ($cartItems as $cartItem) {
                $product = $cartItem->product;
                if (!$product) continue;

                $promo = $product->getActivePromotion();
                $price = $promo ? $promo->getDiscountedPrice($product) : (int) $product->gia;
                $subtotal = $price * $cartItem->so_luong;
                $total += $subtotal;

                $items[] = [
                    'product_id' => $product->id,
                    'ten_san_pham' => $product->ten_sp,
                    'gia' => $price,
                    'so_luong' => $cartItem->so_luong,
                    'thanh_tien' => $subtotal,
                    'size' => $cartItem->size,
                ];
            }

            $order = Order::create([
                'ma_don_hang' => Order::generateOrderCode(),
                'user_id' => $user->id,
                'ten_nguoi_nhan' => $request->ten_nguoi_nhan,
                'sdt_nguoi_nhan' => $request->sdt_nguoi_nhan,
                'dia_chi_giao_hang' => $request->dia_chi_giao_hang,
                'tong_tien' => $total,
                'phi_van_chuyen' => 0,
                'giam_gia' => 0,
                'thanh_tien' => $total,
                'trang_thai' => Order::STATUS_PENDING,
                'phuong_thuc_thanh_toan' => $request->phuong_thuc_thanh_toan,
                'trang_thai_thanh_toan' => 'unpaid',
                'ghi_chu' => $request->ghi_chu,
            ]);

            foreach ($items as $item) {
                $order->orderItems()->create($item);
            }

            // Clear cart
            CartItem::where('user_id', $user->id)->delete();

            return $order->load('orderItems');
        });

        return response()->json([
            'success' => true,
            'message' => 'Đặt hàng thành công!',
            'data' => $order,
        ], 201);
    }
}
