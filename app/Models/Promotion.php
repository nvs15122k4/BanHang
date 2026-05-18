<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Promotion extends Model
{
    use HasFactory;

    protected $fillable = [
        'ten', 'mo_ta', 'loai_km', 'gia_tri', 'gia_tri_toi_da',
        'ngay_bat_dau', 'ngay_ket_thuc', 'pham_vi', 'trang_thai', 'tag',
        'used_count', 'usage_limit', 'usage_limit_per_user',
    ];

    protected $casts = [
        'ngay_bat_dau'   => 'datetime',
        'ngay_ket_thuc'  => 'datetime',
        'gia_tri'        => 'float',
        'gia_tri_toi_da' => 'float',
    ];

    // ─── Relationships ───────────────────────────────────────────────────────

    public function items()
    {
        return $this->hasMany(PromotionItem::class);
    }

    // ─── Scopes ──────────────────────────────────────────────────────────────

    /**
     * Chỉ lấy KM đang active và còn trong thời hạn
     */
    public function scopeCurrentlyActive($query)
    {
        return $query->where('trang_thai', 'active')
            ->where('ngay_bat_dau', '<=', now())
            ->where('ngay_ket_thuc', '>=', now());
    }

    // ─── Accessors ───────────────────────────────────────────────────────────

    public function getIsActiveNowAttribute(): bool
    {
        return $this->trang_thai === 'active'
            && now()->between($this->ngay_bat_dau, $this->ngay_ket_thuc);
    }

    public function getFormattedValueAttribute(): string
    {
        if ($this->loai_km === 'percent') {
            return '-' . number_format($this->gia_tri, 0) . '%';
        }
        return '-' . number_format($this->gia_tri, 0, ',', '.') . 'đ';
    }

    public function getStatusLabelAttribute(): string
    {
        if ($this->trang_thai === 'inactive') return 'Tắt';
        $now = now();
        if ($now->lt($this->ngay_bat_dau)) return 'Chưa bắt đầu';
        if ($now->gt($this->ngay_ket_thuc)) return 'Hết hạn';
        return 'Đang chạy';
    }

    public function getStatusClassAttribute(): string
    {
        return match($this->status_label) {
            'Đang chạy'    => 'success',
            'Chưa bắt đầu' => 'warning',
            'Hết hạn'      => 'secondary',
            default        => 'danger',
        };
    }

    // ─── Usage Tracking ────────────────────────────────────────────────────────

    /**
     * Kiểm tra xem KM có thể sử dụng được không (based on limits)
     * @return bool
     */
    public function canBeUsed(): bool
    {
        if (!$this->is_active_now) {
            return false;
        }

        if ($this->usage_limit !== null && $this->used_count >= $this->usage_limit) {
            return false;
        }

        return true;
    }

    /**
     * Tăng số lần sử dụng KM lên 1
     * @return void
     */
    public function incrementUsage(): void
    {
        $this->increment('used_count');
    }

    /**
     * Kiểm tra xem KM đã đạt giới hạn sử dụng chưa
     * @return bool
     */
    public function isUsageLimitReached(): bool
    {
        return $this->usage_limit !== null && $this->used_count >= $this->usage_limit;
    }

    /**
     * Lấy số lần sử dụng còn lại
     * @return int|null
     */
    public function getRemainingUsage(): ?int
    {
        if ($this->usage_limit === null) {
            return null;
        }

        return max(0, $this->usage_limit - $this->used_count);
    }

    /**
     * Lấy nhãn trạng thái sử dụng
     * @return string
     */
    public function getUsageStatusAttribute(): string
    {
        if (!$this->is_active_now) {
            return $this->getStatusLabelAttribute();
        }

        if ($this->usage_limit === null) {
            return 'Không giới hạn';
        }

        if ($this->isUsageLimitReached()) {
            return 'Đã hết quota';
        }

        return $this->getRemainingUsage() . ' lần còn lại';
    }

    // ─── Discount Logic (Option A: runtime calculation) ──────────────────────

    /**
     * Tính giá sau giảm cho một sản phẩm.
     * Trả về null nếu KM không áp dụng cho sản phẩm này.
     */
    public function getDiscountedPrice(Product $product): ?float
    {
        if (!$this->is_active_now) return null;
        if (!$this->appliesToProduct($product)) return null;

        $originalPrice = $product->gia;

        if ($this->loai_km === 'percent') {
            $discount = $originalPrice * ($this->gia_tri / 100);
            if ($this->gia_tri_toi_da && $discount > $this->gia_tri_toi_da) {
                $discount = $this->gia_tri_toi_da;
            }
            return max(0, $originalPrice - $discount);
        }

        // fixed
        return max(0, $originalPrice - $this->gia_tri);
    }

    /**
     * Kiểm tra KM này có áp dụng cho sản phẩm cụ thể không
     */
    public function appliesToProduct(Product $product): bool
    {
        if ($this->pham_vi === 'all') return true;

        $items = $this->items;

        if ($this->pham_vi === 'category') {
            return $items->where('loai', 'category')
                         ->pluck('gia_tri')
                         ->contains($product->loai);
        }

        if ($this->pham_vi === 'product') {
            return $items->where('loai', 'product')
                         ->pluck('gia_tri')
                         ->contains((string) $product->id);
        }

        return false;
    }

    /**
     * Lấy KM đang active tốt nhất (giảm nhiều nhất) cho một sản phẩm
     */
    public static function getBestForProduct(Product $product): ?self
    {
        $promotions = static::currentlyActive()->with('items')->get();

        $best = null;
        $bestDiscount = 0;

        foreach ($promotions as $promo) {
            $discounted = $promo->getDiscountedPrice($product);
            if ($discounted !== null) {
                $discount = $product->gia - $discounted;
                if ($discount > $bestDiscount) {
                    $bestDiscount = $discount;
                    $best = $promo;
                }
            }
        }

        return $best;
    }
}
