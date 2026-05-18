<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use App\Models\Product;
use Illuminate\Http\Request;

class PromotionController extends Controller
{
    /**
     * Public: trang khuyến mãi cho khách hàng
     */
    public function publicIndex(Request $request)
    {
        // Lấy tất cả KM đang active
        $activePromotions = Promotion::currentlyActive()->with('items')->get();

        // Lấy danh sách sản phẩm (giới hạn 1000 để tránh tràn bộ nhớ nếu database quá lớn)
        $allProducts = Product::where('trang_thai', 'con')->with(['wishlists'])->limit(1000)->get();

        // Gán KM vào từng sản phẩm (runtime)
        $promoProducts = collect();
        foreach ($allProducts as $product) {
            $bestPromo = null;
            $bestDiscount = 0;

            foreach ($activePromotions as $promo) {
                // Check if promotion can still be used (usage limits)
                if (!$promo->canBeUsed()) {
                    continue;
                }
                
                $discountedPrice = $promo->getDiscountedPrice($product);
                if ($discountedPrice !== null) {
                    $discount = $product->gia - $discountedPrice;
                    if ($discount > $bestDiscount) {
                        $bestDiscount     = $discount;
                        $bestPromo        = $promo;
                        $product->promo_price = $discountedPrice;
                        $product->promo       = $promo;
                    }
                }
            }

            if ($bestPromo) {
                $promoProducts->push($product);
            }
        }

        // Filter theo danh mục
        if ($request->filled('loai')) {
            $promoProducts = $promoProducts->filter(fn($p) => $p->loai === $request->loai);
        }

        // Filter theo % giảm tối thiểu
        if ($request->filled('min_discount')) {
            $min = (int) $request->min_discount;
            $promoProducts = $promoProducts->filter(function ($p) use ($min) {
                $pct = $p->gia > 0 ? (($p->gia - $p->promo_price) / $p->gia * 100) : 0;
                return $pct >= $min;
            });
        }

        // Sort
        $sort = $request->get('sort', 'discount_desc');
        $promoProducts = match($sort) {
            'price_asc'      => $promoProducts->sortBy('promo_price'),
            'price_desc'     => $promoProducts->sortByDesc('promo_price'),
            'discount_desc'  => $promoProducts->sortByDesc(fn($p) => $p->gia - $p->promo_price),
            'newest'         => $promoProducts->sortByDesc('created_at'),
            default          => $promoProducts->sortByDesc(fn($p) => $p->gia - $p->promo_price),
        };

        // Paginate manually
        $page     = $request->get('page', 1);
        $perPage  = 12;
        $total    = $promoProducts->count();
        $items    = $promoProducts->values()->slice(($page - 1) * $perPage, $perPage);

        $paginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $items, $total, $perPage, $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $categories  = Product::whereNotNull('loai')->where('loai', '!=', '')
                               ->distinct()->orderBy('loai')->pluck('loai');
        $bannerPromos = Promotion::currentlyActive()->orderByDesc('gia_tri')->take(3)->get();

        return view('promotions.index', compact('paginated', 'categories', 'bannerPromos', 'activePromotions'));
    }
}
