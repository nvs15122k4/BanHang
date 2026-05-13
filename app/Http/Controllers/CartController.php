<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Lấy giỏ hàng từ session
     */
    private function getCart(): array
    {
        return session('cart', []);
    }

    /**
     * Lưu giỏ hàng vào session
     */
    private function saveCart(array $cart): void
    {
        session(['cart' => $cart]);
    }

    /**
     * Hiển thị giỏ hàng
     */
    public function index()
    {
        $cart = $this->getCart();
        $items = [];
        $total = 0;

        foreach ($cart as $productId => $item) {
            $product = Product::find($productId);
            if ($product) {
                $subtotal = $product->gia * $item['so_luong'];
                $total += $subtotal;
                $items[] = [
                    'product'   => $product,
                    'so_luong'  => $item['so_luong'],
                    'subtotal'  => $subtotal,
                ];
            }
        }

        return view('cart.index', compact('items', 'total'));
    }

    /**
     * Thêm sản phẩm vào giỏ
     */
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'so_luong'   => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);

        if ($product->trang_thai !== 'con' || $product->so_luong <= 0) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Sản phẩm này hiện không còn hàng!'], 422);
            }
            return back()->with('error', 'Sản phẩm này hiện không còn hàng!');
        }

        $cart = $this->getCart();
        $id   = $product->id;
        $qty  = (int) $request->so_luong;

        $currentQty = isset($cart[$id]) ? $cart[$id]['so_luong'] : 0;
        $newQty     = $currentQty + $qty;

        if ($newQty > $product->so_luong) {
            $msg = "Chỉ còn {$product->so_luong} sản phẩm trong kho!";
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return back()->with('error', $msg);
        }

        $cart[$id] = ['so_luong' => $newQty];
        $this->saveCart($cart);

        if ($request->ajax()) {
            return response()->json([
                'success'    => true,
                'message'    => "Đã thêm \"{$product->ten_sp}\" vào giỏ hàng!",
                'cart_count' => self::cartCount(),
            ]);
        }

        return back()->with('success', "Đã thêm \"{$product->ten_sp}\" vào giỏ hàng!");
    }

    /**
     * Cập nhật số lượng
     */
    public function update(Request $request, int $productId)
    {
        $request->validate(['so_luong' => 'required|integer|min:1']);

        $product = Product::findOrFail($productId);
        $qty     = (int) $request->so_luong;

        if ($qty > $product->so_luong) {
            return back()->with('error', "Chỉ còn {$product->so_luong} sản phẩm trong kho!");
        }

        $cart = $this->getCart();
        if (isset($cart[$productId])) {
            $cart[$productId]['so_luong'] = $qty;
            $this->saveCart($cart);
        }

        return back()->with('success', 'Đã cập nhật giỏ hàng!');
    }

    /**
     * Xóa sản phẩm khỏi giỏ
     */
    public function remove(int $productId)
    {
        $cart = $this->getCart();
        unset($cart[$productId]);
        $this->saveCart($cart);

        return back()->with('success', 'Đã xóa sản phẩm khỏi giỏ hàng!');
    }

    /**
     * Xóa toàn bộ giỏ hàng
     */
    public function clear()
    {
        session()->forget('cart');
        return back()->with('success', 'Đã xóa toàn bộ giỏ hàng!');
    }

    /**
     * Đếm số lượng item trong giỏ (dùng cho navbar)
     */
    public static function cartCount(): int
    {
        $cart = session('cart', []);
        return array_sum(array_column($cart, 'so_luong'));
    }
}
