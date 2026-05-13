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
     * Nhãn trạng thái hiển thị cho ADMIN
     */
    public static function adminStatusLabels(): array
    {
        return [
            'pending'   => 'Chờ duyệt đơn',
            'confirmed' => 'Đã duyệt đơn',
            'shipping'  => 'Đang giao hàng',
            'delivered' => 'Chờ KH xác nhận',
            'disputing' => 'Đang khiếu nại',
            'completed' => 'Hoàn thành',
            'cancelled' => 'Đã hủy',
        ];
    }

    /**
     * Nhãn trạng thái hiển thị cho USER
     * (mapping từ internal status → text user thấy)
     */
    public static function userStatusLabels(): array
    {
        return [
            'pending'   => 'Chờ duyệt đơn',
            'confirmed' => 'Đang chuẩn bị hàng',
            'shipping'  => 'Đang giao hàng',
            'delivered' => 'Chờ xác nhận',
            'disputing' => 'Đang xử lý khiếu nại',
            'completed' => 'Hoàn thành',
            'cancelled' => 'Đã hủy',
        ];
    }

    /**
     * Các bước admin được phép chuyển tiếp (theo thứ tự flow)
     */
    public static function adminNextStatuses(string $current): array
    {
        $map = [
            'pending'   => ['confirmed', 'cancelled'],
            'confirmed' => ['shipping', 'cancelled'],
            'shipping'  => ['delivered'],
            'delivered' => ['completed'],
            'disputing' => ['completed', 'shipping'],
            'completed' => [],
            'cancelled' => [],
        ];

        return $map[$current] ?? [];
    }

    /**
     * Các hành động user được phép thực hiện khi đơn đang giao
     * shipping → completed (đã nhận) hoặc disputing (chưa nhận / khiếu nại)
     */
    public static function userNextStatuses(string $current): array
    {
        $map = [
            'delivered' => ['completed', 'disputing'],
        ];

        return $map[$current] ?? [];
    }

    /**
     * Get status label (dùng nhãn admin làm mặc định)
     */
    public function getStatusLabelAttribute(): string
    {
        return self::adminStatusLabels()[$this->trang_thai] ?? $this->trang_thai;
    }

    /**
     * Get status label hiển thị cho user
     */
    public function getUserStatusLabelAttribute(): string
    {
        return self::userStatusLabels()[$this->trang_thai] ?? $this->trang_thai;
    }

    /**
     * Get payment method label
     */
    public function getPaymentMethodLabelAttribute()
    {
        $labels = [
            'cod' => 'Thanh toán khi nhận hàng',
            'vietqr' => 'Chuyển khoản VietQR',
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

    /**
     * Get VietQR image URL
     */
    public function getVietqrUrlAttribute()
    {
        $bankId = env('VIETQR_BANK_ID', 'vietcombank');
        $accountNo = env('VIETQR_ACCOUNT_NO', '1014232408');
        $template = env('VIETQR_TEMPLATE', 'print');
        $accountName = env('VIETQR_ACCOUNT_NAME', 'nguyen van sang');
        $amount = (int) $this->thanh_tien;
        $description = $this->ma_don_hang;

        return "https://img.vietqr.io/image/{$bankId}-{$accountNo}-{$template}.png?amount={$amount}&addInfo={$description}&accountName=" . urlencode($accountName);
    }
}
