<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    /**
     * Toggle sản phẩm trong wishlist (thêm nếu chưa có, xóa nếu đã có)
     */
    public function toggle(Request $request, int $productId)
    {
        $product = Product::findOrFail($productId);
        $user    = Auth::user();

        $existing = Wishlist::where('user_id', $user->id)
                            ->where('product_id', $productId)
                            ->first();

        if ($existing) {
            $existing->delete();
            $inWishlist = false;
            $message    = "Đã xóa \"{$product->ten_sp}\" khỏi danh sách yêu thích!";
        } else {
            Wishlist::create(['user_id' => $user->id, 'product_id' => $productId]);
            $inWishlist = true;
            $message    = "Đã thêm \"{$product->ten_sp}\" vào danh sách yêu thích!";
        }

        if ($request->ajax()) {
            return response()->json([
                'success'     => true,
                'in_wishlist' => $inWishlist,
                'message'     => $message,
            ]);
        }

        return back()->with('success', $message);
    }

    /**
     * Hiển thị trang wishlist của user
     */
    public function index()
    {
        $wishlists = Auth::user()
            ->wishlists()
            ->with(['product'])
            ->latest()
            ->paginate(12);

        return view('profile.wishlist', compact('wishlists'));
    }
}
