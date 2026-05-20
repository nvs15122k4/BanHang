<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'ten_sp',
        'loai',
        'mo_ta',
        'anh',
        'trang_thai',
        'so_luong',
        'gia',
        'sizes',
    ];

    // Category management has been moved to the Category model and categories table

    public static function getLoaiList(): array
    {
        return \App\Models\Category::pluck('name', 'slug')->toArray();
    }

    public static function getLoaiIcon(string $loai): string
    {
        return \App\Models\Category::where('slug', $loai)->value('icon') ?? 'fas fa-tag';
    }

    public function getLoaiLabelAttribute(): string
    {
        return $this->loai ?? 'Chua phan loai';
    }

    protected $casts = [
        'so_luong' => 'integer',
        'sizes' => 'array',
    ];

    public function getTrangThaiStatusAttribute()
    {
        return $this->trang_thai === 'con' ? 'Con hang' : 'Het hang';
    }

    public function getIsNewAttribute(): bool
    {
        return $this->created_at && $this->created_at > now()->subDays(14);
    }

    public function getImagePathAttribute(): string
    {
        if (empty($this->anh)) {
            return asset('images/default-product.svg');
        }

        // Cloudinary URL hoặc URL ngoài → dùng trực tiếp
        if (str_starts_with($this->anh, 'http://') || str_starts_with($this->anh, 'https://')) {
            return $this->anh;
        }

        // File local cũ (backward compatibility)
        return asset('storage/products/'.$this->anh);
    }

    public function getImageUrlAttribute()
    {
        return $this->image_path;
    }

    public function getIsImageUrlAttribute(): bool
    {
        return ! empty($this->anh) && (
            str_starts_with($this->anh, 'http://') ||
            str_starts_with($this->anh, 'https://')
        );
    }

    public function getGiaFormatted()
    {
        return (int) $this->gia;
    }

    public function getFormattedPriceAttribute()
    {
        return number_format($this->gia, 0, ',', '.');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    /**
     * Lấy KM đang active tốt nhất cho sản phẩm này (Option A: runtime)
     */
    public function getActivePromotion(): ?Promotion
    {
        return Promotion::getBestForProduct($this);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function approvedReviews()
    {
        return $this->hasMany(Review::class)->where('trang_thai', 'approved');
    }

    public function inventoryLogs()
    {
        return $this->hasMany(InventoryLog::class);
    }

    public function getAverageRatingAttribute()
    {
        return $this->approvedReviews()->avg('rating') ?? 0;
    }

    public function getTotalReviewsAttribute()
    {
        return $this->approvedReviews()->count();
    }
}
