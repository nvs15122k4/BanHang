<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Product;
use App\Models\Promotion;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * Cache cho trang chủ (public, đọc nhiều ghi ít).
 * Render free tier chậm vì mỗi request đều hit DB xuyên Thái Bình Dương (Supabase AP-Northeast-2).
 * Cache ngắn hạn trong file driver giúp cắt số query đi xa.
 */
class HomeCacheService
{
    /** TTL 5 phút — đủ fresh cho trang public, cắt gần hết DB round-trip. */
    public const TTL = 300;

    public const CATEGORIES_KEY = 'home.categories';
    public const STATS_KEY = 'home.stats';
    public const PROMO_PRODUCTS_KEY = 'home.promo_products.v3';
    public const LATEST_PRODUCTS_KEY = 'home.latest_products.v3';

    /**
     * Toàn bộ dữ liệu trang chủ, cache chung 1 key.
     * Key có prefix biến thể → admin đổi sản phẩm/KM là xoá được ngay.
     */
    public function getAll(?string $categorySlug = null): array
    {
        $key = 'home.page.' . ($categorySlug ?: 'all');

        return $this->remember($key, fn () => $this->buildHomeData($categorySlug));
    }

    /**
     * Cache::remember có try/catch — nếu unserialize file cache lỗi
     * (serializable_classes thiếu class), flush + tính lại thay vì ném 500.
     */
    private function remember(string $key, callable $callback): mixed
    {
        try {
            $value = Cache::remember($key, self::TTL, $callback);
            // __PHP_Incomplete_Class = serialization thiếu class → coi như miss
            if ($this->containsIncompleteClass($value)) {
                throw new \RuntimeException('Cache unserialize produced incomplete class');
            }

            return $value;
        } catch (\Throwable $e) {
            $this->flush();
            $value = $callback();

            // Cache lại để request sau không phải tính lại
            try {
                Cache::put($key, $value, self::TTL);
            } catch (\Throwable) {
                // bỏ qua — request hiện tại vẫn trả về đúng
            }

            return $value;
        }
    }

    /**
     * Đệ quy check object hoặc Collection có phần tử __PHP_Incomplete_Class không.
     * File cache với allowed_classes thiếu class sẽ sinh incomplete class ẩn
     * bên trong Collection — check nông (instanceof) sẽ bỏ lọt.
     */
    private function containsIncompleteClass(mixed $value): bool
    {
        if ($value instanceof \__PHP_Incomplete_Class) {
            return true;
        }

        if ($value instanceof \Illuminate\Support\Collection || $value instanceof \Illuminate\Database\Eloquent\Collection) {
            return $value->contains(fn ($item) => $this->containsIncompleteClass($item));
        }

        if (is_array($value)) {
            foreach ($value as $item) {
                if ($this->containsIncompleteClass($item)) {
                    return true;
                }
            }
        }

        return false;
    }

    public function flush(): void
    {
        Cache::forget(self::CATEGORIES_KEY);
        Cache::forget(self::STATS_KEY);
        Cache::forget(self::PROMO_PRODUCTS_KEY);
        Cache::forget(self::LATEST_PRODUCTS_KEY);
        Cache::forget('home.page.all');

        // Xoá luôn các key có category (chỉ tầm vài chục danh mục, chạy được)
        collect(static::knownCategorySlugs())->each(fn ($slug) => Cache::forget('home.page.' . $slug));
    }

    public function categories(): Collection
    {
        return $this->remember(self::CATEGORIES_KEY, function () {
            return Category::with('children')->whereNull('parent_id')->get();
        });
    }

    /**
     * Các query thống kê nặng cho dashboard home.
     * Trả về array để tránh cache model → serialize vấn đề.
     */
    public function statistics(): array
    {
        return $this->remember(self::STATS_KEY, function () {
            return [
                'totalProducts' => Product::count(),
                'inStock' => Product::where('trang_thai', 'con')->count(),
                'outOfStock' => Product::where('trang_thai', 'het')->count(),
                'totalValue' => (int) \DB::table('products')->sum('gia'),
            ];
        });
    }

    /**
     * Promo products: tính trước trong SQL để khỏi loop 1000 sp trong PHP.
     * Cache 5 phút — đủ chấp nhận cho trang public.
     */
    public function promoProducts(?string $categorySlug = null): Collection
    {
        return $this->remember(self::PROMO_PRODUCTS_KEY . '.' . ($categorySlug ?: 'all'), function () use ($categorySlug) {
            return $this->computePromoProducts($categorySlug);
        });
    }

    public function latestProducts(?string $categorySlug = null): Collection
    {
        return $this->remember(self::LATEST_PRODUCTS_KEY . '.' . ($categorySlug ?: 'all'), function () use ($categorySlug) {
            $q = Product::where('trang_thai', 'con');

            if ($categorySlug) {
                $slugs = [$categorySlug];
                $cat = Category::with('children')->where('slug', $categorySlug)->first();
                if ($cat) {
                    $slugs = array_merge($slugs, $cat->children->pluck('slug')->all());
                }
                $q->whereIn('loai', $slugs);
            }

            return $q->orderBy('created_at', 'desc')
                ->select(['id', 'ten_sp', 'slug', 'loai', 'gia', 'so_luong', 'trang_thai', 'anh', 'created_at'])
                ->take(8)
                ->get();
        });
    }

    private function buildHomeData(?string $categorySlug): array
    {
        $categories = $this->categories();
        $stats = $this->statistics();
        $latest = $this->latestProducts($categorySlug);
        $promo = $this->promoProducts($categorySlug);

        return compact('categories', 'stats', 'latest', 'promo');
    }

    /**
     * Compute promo products in SQL, no PHP loop over all products.
     */
    private function computePromoProducts(?string $categorySlug): Collection
    {
        $activePromotions = Promotion::currentlyActive()->with('items')->get();

        if ($activePromotions->isEmpty()) {
            return collect();
        }

        $q = Product::where('trang_thai', 'con')
            ->select(['id', 'ten_sp', 'slug', 'loai', 'gia', 'so_luong', 'trang_thai', 'anh', 'created_at']);

        if ($categorySlug) {
            $slugs = [$categorySlug];
            $cat = Category::with('children')->where('slug', $categorySlug)->first();
            if ($cat) {
                $slugs = array_merge($slugs, $cat->children->pluck('slug')->all());
            }
            $q->whereIn('loai', $slugs);
        }

        // Chỉ lấy sản phẩm trong phạm vi KM → ít dòng SQL hơn nhiều so với get() toàn bộ
        // $applicableIds === null nghĩa là có KM phạm vi "all" → không lọc id
        $applicableIds = $this->promotionProductIds($activePromotions);
        if ($applicableIds !== null) {
            $q->whereIn('id', $applicableIds ?: [0]);
        }
        $q->limit(200);

        $candidates = $q->get();

        $result = collect();
        foreach ($candidates as $product) {
            $bestDiscount = 0;
            foreach ($activePromotions as $promo) {
                $discountedPrice = $promo->getDiscountedPrice($product);
                if ($discountedPrice !== null) {
                    $discount = $product->gia - $discountedPrice;
                    if ($discount > $bestDiscount) {
                        $bestDiscount = $discount;
                        $product->promo_price = $discountedPrice;
                        $product->promo = $promo;
                    }
                }
            }
            if ($product->promo_price !== null) {
                $result->push($product);
            }
        }

        return $result
            ->sortByDesc(fn ($p) => (float) $p->gia - (float) $p->promo_price)
            ->take(8)
            ->values();
    }

    /**
     * Tập id sản phẩm nằm trong phạm vi KM đang active (all / category / product).
     * Trả về null nghĩa là có KM phạm vi "all" → không lọc.
     */
    private function promotionProductIds(Collection $promotions): ?array
    {
        $ids = [];
        $categorySlugs = collect();

        foreach ($promotions as $promo) {
            if ($promo->pham_vi === 'all') {
                return null; // có KM áp dụng toàn bộ → không lọc id
            }
            foreach ($promo->items as $item) {
                if ($item->loai === 'product') {
                    $ids[] = (int) $item->gia_tri;
                } elseif ($item->loai === 'category') {
                    $categorySlugs->push($item->gia_tri);
                }
            }
        }

        if ($categorySlugs->isNotEmpty()) {
            $catProductIds = Product::whereIn('loai', $categorySlugs->unique()->values()->all())
                ->pluck('id')
                ->all();
            $ids = array_merge($ids, $catProductIds);
        }

        return array_unique($ids);
    }

    private static function knownCategorySlugs(): array
    {
        return Category::pluck('slug')->all();
    }
}
