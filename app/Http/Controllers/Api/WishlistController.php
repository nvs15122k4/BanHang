<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function index(): JsonResponse
    {
        $items = Auth::user()->wishlists()->with('product')->get();

        return response()->json([
            'data' => $items->map(fn($w) => [
                'id' => $w->id,
                'product_id' => $w->product_id,
                'product' => $w->product,
                'created_at' => $w->created_at,
            ]),
        ]);
    }

    public function toggle(int $productId): JsonResponse
    {
        $product = Product::findOrFail($productId);
        $user = Auth::user();

        $existing = Wishlist::where('user_id', $user->id)
            ->where('product_id', $productId)
            ->first();

        if ($existing) {
            $existing->delete();
            $inWishlist = false;
            $message = "Đã xóa \"{$product->ten_sp}\" khỏi danh sách yêu thích!";
        } else {
            Wishlist::create(['user_id' => $user->id, 'product_id' => $productId]);
            $inWishlist = true;
            $message = "Đã thêm vào danh sách yêu thích!";
        }

        return response()->json([
            'success' => true,
            'in_wishlist' => $inWishlist,
            'message' => $message,
        ]);
    }
}
