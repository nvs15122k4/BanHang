<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'order_id' => 'nullable|exists:orders,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:2000',
            'images' => 'nullable|array',
            'images.*' => 'url|max:2048',
        ]);

        $existing = Review::where('user_id', Auth::id())
            ->where('product_id', $validated['product_id'])
            ->first();

        if ($existing) {
            return response()->json(['message' => 'Bạn đã đánh giá sản phẩm này rồi!'], 400);
        }

        $review = Review::create([
            'user_id' => Auth::id(),
            'product_id' => $validated['product_id'],
            'order_id' => $validated['order_id'] ?? null,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'] ?? null,
            'images' => $validated['images'] ?? null,
            'trang_thai' => 'pending',
        ]);

        $review->load('user');

        return response()->json([
            'success' => true,
            'message' => 'Đánh giá của bạn đang chờ duyệt!',
            'data' => $review,
        ], 201);
    }

    public function destroy(Review $review): JsonResponse
    {
        if (Auth::user()->role !== 'admin' && $review->user_id !== Auth::id()) {
            return response()->json(['message' => 'Không có quyền xóa'], 403);
        }

        $review->delete();

        return response()->json(['success' => true, 'message' => 'Đã xóa đánh giá!']);
    }
}
