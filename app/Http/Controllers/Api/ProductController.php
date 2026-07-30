<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Product::with(['productImages', 'variants', 'category', 'brand']);

        if ($request->filled('search')) {
            $s = $request->input('search');
            $query->where(function ($q) use ($s) {
                $q->where('ten_sp', 'like', "%{$s}%")
                    ->orWhere('mo_ta', 'like', "%{$s}%");
            });
        }
        if ($request->filled('loai')) {
            $query->where('loai', $request->loai);
        }
        if ($request->filled('min_price')) {
            $query->where('gia', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('gia', '<=', $request->max_price);
        }
        if ($request->filled('status')) {
            $query->where('trang_thai', $request->status);
        }

        $sortField = $request->input('sort', 'created_at');
        $sortDir = $request->input('order', 'desc');
        $query->orderBy(in_array($sortField, ['gia', 'ten_sp', 'created_at']) ? $sortField : 'created_at', $sortDir === 'asc' ? 'asc' : 'desc');

        $products = $query->paginate($request->input('per_page', 20));

        // Transform: add computed fields
        $products->getCollection()->transform(function ($p) {
            return $this->transformProduct($p);
        });

        return response()->json($products);
    }

    public function show(Product $product): JsonResponse
    {
        $product->loadMissing(['productImages', 'variants', 'brand', 'category']);
        $reviews = $product->approvedReviews()->with('user')->orderBy('created_at', 'desc')->get();

        return response()->json([
            'data' => $this->transformProduct($product),
            'reviews' => $reviews,
        ]);
    }

    private function transformProduct(Product $p): array
    {
        $activePromotion = $p->getActivePromotion();
        $discountedPrice = $activePromotion ? $activePromotion->getDiscountedPrice($p) : null;

        return [
            'id' => $p->id,
            'ten_sp' => $p->ten_sp,
            'slug' => $p->slug,
            'loai' => $p->loai,
            'loai_label' => $p->loai_label,
            'brand_id' => $p->brand_id,
            'brand_name' => $p->brand?->name,
            'mo_ta' => $p->mo_ta,
            'anh' => $p->anh,
            'image_path' => $p->image_path,
            'image_url' => $p->image_url,
            'images' => $p->productImages->pluck('image_url')->all(),
            'trang_thai' => $p->trang_thai,
            'so_luong' => $p->so_luong,
            'gia' => (int) $p->gia,
            'gia_km' => $discountedPrice ? (int) $discountedPrice : null,
            'formatted_price' => $p->formatted_price,
            'sizes' => $p->variant_options,
            'is_new' => $p->is_new,
            'average_rating' => (float) $p->average_rating,
            'total_reviews' => (int) $p->total_reviews,
            'promotion' => $activePromotion ? [
                'id' => $activePromotion->id,
                'ten' => $activePromotion->ten,
                'loai_km' => $activePromotion->loai_km,
                'gia_tri' => $activePromotion->gia_tri,
            ] : null,
            'created_at' => $p->created_at,
            'updated_at' => $p->updated_at,
        ];
    }
}
