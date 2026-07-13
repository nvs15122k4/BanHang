<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'ten_sp',
        'slug',
        'loai',
        'brand_id',
        'mo_ta',
        'anh',
        'trang_thai',
        'so_luong',
        'gia',
        'sizes',
    ];

    protected static function booted(): void
    {
        static::creating(function (Product $product): void {
            if (blank($product->slug)) {
                $product->slug = static::generateUniqueSlug($product->ten_sp);
            }
        });
    }

    public static function generateUniqueSlug(string $name): string
    {
        $baseSlug = Str::slug(Str::limit($name, 100, '')) ?: 'san-pham';
        $slug = $baseSlug;
        $suffix = 2;

        while (static::withTrashed()->where('slug', $slug)->exists()) {
            $slug = $baseSlug.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }

    public static function getLoaiList(): array
    {
        return Category::treeList()->pluck('path', 'slug')->toArray();
    }

    public static function getLoaiIcon(string $loai): string
    {
        return Category::where('slug', $loai)->value('icon') ?? 'fas fa-tag';
    }

    public function getLoaiLabelAttribute(): string
    {
        return $this->category?->name ?? $this->loai ?? 'Chua phan loai';
    }

    protected $casts = [
        'so_luong' => 'integer',
        'sizes' => 'array',
    ];

    public function getTrangThaiStatusAttribute()
    {
        return $this->trang_thai === 'con' ? 'Còn hàng' : 'Hết hàng';
    }

    public function getIsNewAttribute(): bool
    {
        return $this->created_at && $this->created_at > now()->subDays(14);
    }

    public function getImagePathAttribute(): string
    {
        $galleryImage = $this->relationLoaded('productImages')
            ? $this->productImages->sortBy([
                ['is_primary', 'desc'],
                ['sort_order', 'asc'],
            ])->first()?->image_url
            : ProductImage::where('product_id', $this->id)
                ->orderByDesc('is_primary')
                ->orderBy('sort_order')
                ->value('image_url');
        $image = $galleryImage ?: $this->anh;

        if (empty($image)) {
            return asset('images/default-product.svg');
        }

        // Cloudinary URL hoặc URL ngoài → dùng trực tiếp
        if (str_starts_with($image, 'http://') || str_starts_with($image, 'https://')) {
            return $image;
        }

        // File local cũ (backward compatibility)
        return asset('storage/products/'.$image);
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

    public function category()
    {
        return $this->belongsTo(Category::class, 'loai', 'slug');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class)->orderBy('id');
    }

    public function productImages()
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order')->orderBy('id');
    }

    public function getVariantOptionsAttribute(): array
    {
        $variants = $this->relationLoaded('variants')
            ? $this->variants->pluck('name')->all()
            : $this->variants()->pluck('name')->all();

        return $variants ?: array_values($this->sizes ?? []);
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
