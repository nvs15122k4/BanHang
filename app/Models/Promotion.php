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
