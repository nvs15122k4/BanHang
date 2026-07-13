<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * Display reviews list (Admin)
     */
    public function index(Request $request)
    {
        $query = Review::with(['user', 'product', 'order'])->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('trang_thai', $request->status);
        }

        // Filter by rating
        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        // Filter by product
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('user', function($q2) use ($search) {
                    $q2->where('name', 'like', "%{$search}%");
                })->orWhereHas('product', function($q2) use ($search) {
                    $q2->where('ten_sp', 'like', "%{$search}%");
                });
            });
        }

        $reviews = $query->paginate(15);

        // Statistics
        $stats = [
            'total' => Review::count(),
            'pending' => Review::where('trang_thai', 'pending')->count(),
            'approved' => Review::where('trang_thai', 'approved')->count(),
            'rejected' => Review::where('trang_thai', 'rejected')->count(),
            'avg_rating' => Review::where('trang_thai', 'approved')->avg('rating') ?? 0,
        ];

        $products = Product::orderBy('ten_sp')->get();

        return view('admin.reviews.index', compact('reviews', 'stats', 'products'));
    }

    /**
     * Approve review
     */
    public function approve(Review $review)
    {
        $review->update(['trang_thai' => 'approved']);
        return back()->with('success', 'Đã duyệt đánh giá!');
    }

    /**
     * Reject review
     */
    public function reject(Review $review)
    {
        $review->update(['trang_thai' => 'rejected']);
        return back()->with('success', 'Đã từ chối đánh giá!');
    }

    /**
     * Delete review
     */
    public function destroy(Review $review)
    {
        // Kiểm tra quyền: Admin hoặc chính người viết đánh giá
        if (Auth::user()->role !== 'admin' && $review->user_id !== Auth::id()) {
            return back()->with('error', 'Bạn không có quyền xóa đánh giá này!');
        }

        $review->delete();
        return back()->with('success', 'Đã xóa đánh giá!');
    }

    /**
     * Store review (Customer)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'rating'     => 'required|integer|min:1|max:5',
            'comment'    => 'nullable|string|max:1000',
            'images.*'   => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120', // Max 5MB per image
            'video'      => 'nullable|mimes:mp4,mov,avi,wmv|max:20480', // Max 20MB for video
        ], [
            'product_id.required' => 'Vui lòng chọn sản phẩm',
            'rating.required'     => 'Vui lòng chọn số sao',
            'images.*.image'      => 'File tải lên phải là hình ảnh',
            'images.*.max'        => 'Ảnh không được vượt quá 5MB',
            'video.mimes'         => 'Định dạng video không hỗ trợ',
            'video.max'           => 'Video không được vượt quá 20MB',
        ]);

        // Kiểm tra đã mua sản phẩm và đơn hàng đã hoàn thành chưa
        $completedOrder = Auth::user()->orders()
            ->whereHas('orderItems', fn($q) => $q->where('product_id', $validated['product_id']))
            ->where('trang_thai', 'completed')
            ->latest()
            ->first();

        if (!$completedOrder) {
            return back()->with('error', 'Bạn chỉ có thể đánh giá sản phẩm sau khi đơn hàng đã hoàn thành!');
        }

        // Check if user already reviewed this product
        $existingReview = Review::where('user_id', Auth::id())
            ->where('product_id', $validated['product_id'])
            ->first();

        if ($existingReview) {
            return back()->with('error', 'Bạn đã đánh giá sản phẩm này rồi! Bạn có thể xóa đánh giá cũ để đánh giá lại.');
        }

        // Xử lý upload ảnh
        $imagePaths = [];
        if ($request->hasFile('images')) {
            $files = $request->file('images');
            if (count($files) > 10) {
                return back()->with('error', 'Bạn chỉ có thể tải lên tối đa 10 ảnh.');
            }
            foreach ($files as $file) {
                $path = $file->store('reviews/images', 'public');
                $imagePaths[] = $path;
            }
        }

        // Xử lý upload video
        $videoPath = null;
        if ($request->hasFile('video')) {
            $videoPath = $request->file('video')->store('reviews/videos', 'public');
        }

        Review::create([
            'user_id'    => Auth::id(),
            'product_id' => $validated['product_id'],
            'order_id'   => $completedOrder->id,
            'rating'     => $validated['rating'],
            'comment'    => $validated['comment'] ?? null,
            'images'     => $imagePaths,
            'video'      => $videoPath,
            'trang_thai' => 'approved',
        ]);

        return back()->with('success', 'Cảm ơn bạn đã đánh giá! Đánh giá của bạn đã được ghi nhận.');
    }
}
