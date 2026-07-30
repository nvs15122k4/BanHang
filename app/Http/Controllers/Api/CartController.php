<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index(): JsonResponse
    {
        $items = CartItem::where('user_id', Auth::id())
            ->with('product')
            ->get()
            ->map(function (CartItem $item) {
                $product = $item->product;
                if (!$product) return null;

                $promo = $product->getActivePromotion();
                $price = $promo ? $promo->getDiscountedPrice($product) : (int) $product->gia;
                $subtotal = $price * $item->so_luong;

                return [
                    'id' => $item->cart_key,
                    'cart_key' => $item->cart_key,
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
                    'so_luong' => $item->so_luong,
                    'size' => $item->size,
                    'gia' => $price,
                    'thanh_tien' => $subtotal,
                ];
            })
            ->filter()
            ->values();

        $total = $items->sum('thanh_tien');

        return response()->json(['data' => $items, 'total' => $total]);
    }

    public function add(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'so_luong' => 'required|integer|min:1',
            'size' => 'nullable|string|max:255',
        ]);

        $product = Product::findOrFail($validated['product_id']);
        $size = $validated['size'] ?? null;

        $existing = CartItem::where('user_id', Auth::id())
            ->where('product_id', $product->id)
            ->where('size', $size)
            ->first();

        if ($existing) {
            $existing->increment('so_luong', $validated['so_luong']);
        } else {
            CartItem::create([
                'user_id' => Auth::id(),
                'product_id' => $product->id,
                'so_luong' => $validated['so_luong'],
                'size' => $size,
            ]);
        }

        $count = CartItem::where('user_id', Auth::id())->count();

        return response()->json([
            'success' => true,
            'message' => 'Đã thêm vào giỏ hàng!',
            'cart_count' => $count,
        ]);
    }

    public function update(Request $request, string $cartKey): JsonResponse
    {
        $validated = $request->validate([
            'so_luong' => 'required|integer|min:1',
        ]);

        $item = $this->resolveCartItem($cartKey);
        if (!$item) {
            return response()->json(['message' => 'Sản phẩm không tồn tại trong giỏ'], 404);
        }

        $item->update(['so_luong' => $validated['so_luong']]);

        return response()->json(['success' => true, 'message' => 'Đã cập nhật giỏ hàng!']);
    }

    public function remove(string $cartKey): JsonResponse
    {
        $item = $this->resolveCartItem($cartKey);
        if (!$item) {
            return response()->json(['message' => 'Sản phẩm không tồn tại trong giỏ'], 404);
        }

        $item->delete();

        return response()->json(['success' => true, 'message' => 'Đã xóa sản phẩm khỏi giỏ!']);
    }

    public function clear(): JsonResponse
    {
        CartItem::where('user_id', Auth::id())->delete();

        return response()->json(['success' => true, 'message' => 'Đã xóa giỏ hàng!']);
    }

    /**
     * Resolve cart item from composite key "productId_size" or "productId"
     */
    private function resolveCartItem(string $cartKey): ?CartItem
    {
        $parts = explode('_', $cartKey);
        $productId = (int) $parts[0];
        $size = $parts[1] ?? null;

        return CartItem::where('user_id', Auth::id())
            ->where('product_id', $productId)
            ->where('size', $size)
            ->first();
    }
}
