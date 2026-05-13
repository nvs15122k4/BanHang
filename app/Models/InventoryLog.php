<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'loai',
        'so_luong_truoc',
        'so_luong_thay_doi',
        'so_luong_sau',
        'ly_do',
        'order_id',
        'user_id',
    ];

    /**
     * Get the product
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the order
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get type label
     */
    public function getTypeLabelAttribute()
    {
        $labels = [
            'in' => 'Nhập kho',
            'out' => 'Xuất kho',
            'adjust' => 'Điều chỉnh',
        ];

        return $labels[$this->loai] ?? $this->loai;
    }
}
