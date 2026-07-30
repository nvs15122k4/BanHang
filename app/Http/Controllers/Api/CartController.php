<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    private function getCart(): array
    {
        return session('cart', []);
    }

    private function saveCart(array $cart): void
    {
        session(['cart' => $cart]);
    }

    public function index(): JsonResponse
    {
        $cart = $this->getCart();
        $items = [];
        $total = 0;

        foreach ($cart as $cartKey => $item) {
            $productId = $item['product_id'] ?? explode('_', $cartKey)[0];
            $product = Product::find($productId);
            if ($product) {
                $promo = $product->getActivePromotion();
                $price = $promo ? $promo->getDiscountedPrice($product) : (int) $product->gia;
                $subtotal = $price * $item['so_luong'];
                $total += $subtotal;
                $items[] = [
                    'id' => $cartKey,
                    'cart_key' => $cartKey,
                    'product_id' => $product->id,
                    'product' => [
                        'id' => $product->id,
                        'ten_sp' => $product->ten_sp,
                        'slug' => $product->slug,
                        'image_url' => $product->image_url,
                        'anh' => $product->anh,
                        'gia' => (int) $product->gia,
                        'trang_thai' => $product->trang_thai,
                    ],
                    'so_luong' => $item['so_luong'],
                    'size' => $item['size'] ?? null,
                    'gia' => $price,
                    'thanh_tien' => $subtotal,
                ];
            }
        }

        return response()->json(['data' => $items, 'total' => $total]);
    }

    public function add(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'so_luong' => 'required|integer|min:1',
            'size' => 'nullable|string|max:255',
        ]);

        $product = Product::findOrFail($request->product_id);
        $cart = $this->getCart();

        $size = $request->filled('size') ? trim($request->size) : null;
        $cartKey = $product->id . ($size ? "_" . $size : "");

        if (isset($cart[$cartKey])) {
            $cart[$cartKey]['so_luong'] += $request->so_luong;
        } else {
            $cart[$cartKey] = [
                'product_id' => $product->id,
                'so_luong' => $request->so_luong,
                'size' => $size,
            ];
        }

        $this->saveCart($cart);

        return response()->json([
            'success' => true,
            'message' => 'Đã thêm vào giỏ hàng!',
            'cart_count' => count($cart),
        ]);
    }

    public function update(Request $request, string $cartKey): JsonResponse
    {
        $request->validate(['so_luong' => 'required|integer|min:1']);

        $cart = $this->getCart();
        if (!isset($cart[$cartKey])) {
            return response()->json(['message' => 'Sản phẩm không tồn tại trong giỏ'], 404);
        }

        $cart[$cartKey]['so_luong'] = $request->so_luong;
        $this->saveCart($cart);

        return response()->json(['success' => true, 'message' => 'Đã cập nhật giỏ hàng!']);
    }

    public function remove(string $cartKey): JsonResponse
    {
        $cart = $this->getCart();
        unset($cart[$cartKey]);
        $this->saveCart($cart);

        return response()->json(['success' => true, 'message' => 'Đã xóa sản phẩm khỏi giỏ!']);
    }

    public function clear(): JsonResponse
    {
        session()->forget('cart');
        return response()->json(['success' => true, 'message' => 'Đã xóa giỏ hàng!']);
    }
}
