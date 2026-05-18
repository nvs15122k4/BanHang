<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    /**
     * Display shopping cart page
     */
    public function index()
    {
        $cartItems = Auth::user()->carts()->with('product')->get();
        
        $totalAmount = $cartItems->sum(function($item) {
            return ($item->product->promo_price ?? $item->product->gia) * $item->so_luong;
        });

        return view('cart.index', compact('cartItems', 'totalAmount'));
    }

    /**
     * Add product to cart (supports AJAX)
     */
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'so_luong'   => 'required|integer|min:1',
        ]);

        $productId = $request->product_id;
        $quantity  = $request->so_luong;
        $product   = Product::findOrFail($productId);
        $user      = Auth::user();

        // Check stock availability
        if ($product->so_luong < $quantity) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => "Sản phẩm \"{$product->ten_sp}\" chỉ còn {$product->so_luong} sản phẩm trong kho!"
                ], 422);
            }
            return back()->with('error', "Sản phẩm \"{$product->ten_sp}\" chỉ còn {$product->so_luong} sản phẩm trong kho!");
        }

        $existing = Cart::where('user_id', $user->id)
                        ->where('product_id', $productId)
                        ->first();

        if ($existing) {
            $newQuantity = $existing->so_luong + $quantity;
            if ($product->so_luong < $newQuantity) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => "Tổng số lượng trong giỏ hàng vượt quá tồn kho hiện tại ({$product->so_luong} sản phẩm)!"
                    ], 422);
                }
                return back()->with('error', "Tổng số lượng trong giỏ hàng vượt quá tồn kho!");
            }
            $existing->update(['so_luong' => $newQuantity]);
        } else {
            Cart::create([
                'user_id'    => $user->id,
                'product_id' => $productId,
                'so_luong'   => $quantity,
            ]);
        }

        $cartCount = Cart::where('user_id', $user->id)->sum('so_luong');

        if ($request->ajax()) {
            return response()->json([
                'success'    => true,
                'cart_count' => $cartCount,
                'message'    => "Đã thêm \"{$product->ten_sp}\" vào giỏ hàng thành công!"
            ]);
        }

        return back()->with('success', "Đã thêm \"{$product->ten_sp}\" vào giỏ hàng thành công!");
    }

    /**
     * Update cart quantity (supports AJAX)
     */
    public function update(Request $request, int $productId)
    {
        $request->validate([
            'so_luong' => 'required|integer|min:1',
        ]);

        $quantity = $request->so_luong;
        $product  = Product::findOrFail($productId);
        $user     = Auth::user();

        if ($product->so_luong < $quantity) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => "Không thể cập nhật! Chỉ còn {$product->so_luong} sản phẩm trong kho!"
                ], 422);
            }
            return back()->with('error', "Không đủ tồn kho!");
        }

        Cart::where('user_id', $user->id)
            ->where('product_id', $productId)
            ->update(['so_luong' => $quantity]);

        $cartItems = Cart::where('user_id', $user->id)->with('product')->get();
        $totalAmount = $cartItems->sum(function($item) {
            return ($item->product->promo_price ?? $item->product->gia) * $item->so_luong;
        });

        $cartCount = $cartItems->sum('so_luong');

        if ($request->ajax()) {
            return response()->json([
                'success'      => true,
                'cart_count'   => $cartCount,
                'total_amount' => number_format($totalAmount, 0, ',', '.') . ' VNĐ',
                'message'      => 'Đã cập nhật số lượng thành công!'
            ]);
        }

        return back()->with('success', 'Đã cập nhật số lượng thành công!');
    }

    /**
     * Remove item from cart (supports AJAX)
     */
    public function remove(int $productId)
    {
        $user = Auth::user();
        Cart::where('user_id', $user->id)->where('product_id', $productId)->delete();

        $cartItems = Cart::where('user_id', $user->id)->with('product')->get();
        $totalAmount = $cartItems->sum(function($item) {
            return ($item->product->promo_price ?? $item->product->gia) * $item->so_luong;
        });

        $cartCount = $cartItems->sum('so_luong');

        if (request()->ajax()) {
            return response()->json([
                'success'      => true,
                'cart_count'   => $cartCount,
                'total_amount' => number_format($totalAmount, 0, ',', '.') . ' VNĐ',
                'message'      => 'Đã xóa sản phẩm khỏi giỏ hàng!'
            ]);
        }

        return back()->with('success', 'Đã xóa sản phẩm khỏi giỏ hàng!');
    }

    /**
     * Clear all items from cart (supports AJAX)
     */
    public function clear()
    {
        Auth::user()->carts()->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Giỏ hàng đã được làm trống!'
            ]);
        }

        return back()->with('success', 'Giỏ hàng đã được làm trống!');
    }
}
