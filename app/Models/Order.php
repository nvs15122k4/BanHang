<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'ma_don_hang',
        'user_id',
        'ten_nguoi_nhan',
        'sdt_nguoi_nhan',
        'dia_chi_giao_hang',
        'tong_tien',
        'phi_van_chuyen',
        'giam_gia',
        'thanh_tien',
        'trang_thai',
        'phuong_thuc_thanh_toan',
        'trang_thai_thanh_toan',
        'ghi_chu',
    ];

    protected $casts = [
        'tong_tien' => 'decimal:2',
        'phi_van_chuyen' => 'decimal:2',
        'giam_gia' => 'decimal:2',
        'thanh_tien' => 'decimal:2',
    ];

    /**
     * Get the user that owns the order
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the order items
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the inventory logs
     */
    public function inventoryLogs()
    {
        return $this->hasMany(InventoryLog::class);
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute()
    {
        $labels = [
            'pending' => 'Chờ xác nhận',
            'confirmed' => 'Đã xác nhận',
            'shipping' => 'Đang giao hàng',
            'completed' => 'Hoàn thành',
            'cancelled' => 'Đã hủy',
        ];

        return $labels[$this->trang_thai] ?? $this->trang_thai;
    }

    /**
     * Get payment method label
     */
    public function getPaymentMethodLabelAttribute()
    {
        $labels = [
            'cod' => 'Thanh toán khi nhận hàng',
            'bank_transfer' => 'Chuyển khoản ngân hàng',
            'vnpay' => 'VNPay',
        ];

        return $labels[$this->phuong_thuc_thanh_toan] ?? $this->phuong_thuc_thanh_toan;
    }

    /**
     * Get payment status label
     */
    public function getPaymentStatusLabelAttribute()
    {
        return $this->trang_thai_thanh_toan === 'paid' ? 'Đã thanh toán' : 'Chưa thanh toán';
    }

    /**
     * Generate unique order code
     */
    public static function generateOrderCode()
    {
        do {
            $code = 'ORD-' . strtoupper(substr(uniqid(), -8));
        } while (self::where('ma_don_hang', $code)->exists());

        return $code;
    }
}
