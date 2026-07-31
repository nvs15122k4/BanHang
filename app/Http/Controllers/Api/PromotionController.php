<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Promotion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class PromotionController extends Controller
{
    /**
     * Danh sách khuyến mãi đang hoạt động (public, dành cho mobile).
     */
    public function index(Request $request): JsonResponse
    {
        $activePromotions = Promotion::currentlyActive()->with('items')->get();

        $productsQuery = Product::where('trang_thai', 'con')
            ->with(['productImages', 'variants', 'category', 'brand'])
            ->limit(1000);

        if ($request->filled('search')) {
            $s = trim((string) $request->input('search'));
            if ($s !== '') {
                $productsQuery->where('ten_sp', 'like', '%'.$s.'%');
            }
        }

        $products = $productsQuery->get();

        $promoProducts = collect();
        foreach ($products as $product) {
            $bestPromo = null;
            $bestDiscount = 0;

            foreach ($activePromotions as $promo) {
                if (! $promo->canBeUsed()) {
                    continue;
                }

                $discountedPrice = $promo->getDiscountedPrice($product);
                if ($discountedPrice !== null) {
                    $discount = $product->gia - $discountedPrice;
                    if ($discount > $bestDiscount) {
                        $bestDiscount = $discount;
                        $bestPromo = $promo;
                        $product->promo_price = $discountedPrice;
                        $product->promo = $promo;
                    }
                }
            }

            if ($bestPromo) {
                $promoProducts->push($product);
            }
        }

        $sort = $request->get('sort', 'discount_desc');
        $promoProducts = match ($sort) {
            'price_asc' => $promoProducts->sortBy('promo_price'),
            'price_desc' => $promoProducts->sortByDesc('promo_price'),
            'discount_desc' => $promoProducts->sortByDesc(fn ($p) => $p->gia - $p->promo_price),
            'newest' => $promoProducts->sortByDesc('created_at'),
            default => $promoProducts->sortByDesc(fn ($p) => $p->gia - $p->promo_price),
        };

        $page = max(1, (int) $request->get('page', 1));
        $perPage = (int) $request->get('per_page', 12);
        $total = $promoProducts->count();
        $items = $promoProducts->values()->slice(($page - 1) * $perPage, $perPage);

        $paginator = new LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $paginator->getCollection()->transform(function ($product) {
            return [
                'id' => $product->id,
                'ten_sp' => $product->ten_sp,
                'slug' => $product->slug,
                'loai' => $product->loai,
                'loai_label' => $product->loai_label,
                'anh' => $product->anh,
                'image_url' => $product->image_url,
                'trang_thai' => $product->trang_thai,
                'so_luong' => $product->so_luong,
                'gia' => (int) $product->gia,
                'gia_km' => isset($product->promo_price) ? (int) $product->promo_price : null,
                'formatted_price' => $product->formatted_price,
                'promotion' => isset($product->promo) ? [
                    'id' => $product->promo->id,
                    'ten' => $product->promo->ten,
                    'loai_km' => $product->promo->loai_km,
                    'gia_tri' => $product->promo->gia_tri,
                ] : null,
            ];
        });

        return response()->json($paginator);
    }
}
