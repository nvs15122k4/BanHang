<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    use HasFactory;

    protected $table = 'cart_items';

    protected $fillable = [
        'user_id',
        'product_id',
        'so_luong',
        'size',
    ];

    protected $casts = [
        'so_luong' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getCartKeyAttribute(): string
    {
        return $this->product_id . ($this->size ? "_" . $this->size : "");
    }

    public function getThanhTienAttribute(): float
    {
        $promo = $this->product->getActivePromotion();
        $price = $promo ? $promo->getDiscountedPrice($this->product) : (int) $this->product->gia;
        return $price * $this->so_luong;
    }
}
