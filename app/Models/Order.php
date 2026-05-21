<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_SHIPPING = 'shipping';
    public const STATUS_DELIVERED = 'delivered';
    public const STATUS_DISPUTING = 'disputing';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLING = 'cancelling';
    public const STATUS_CANCELLED = 'cancelled';

    public const PAYMENT_PENDING = 'pending_payment';
    public const PAYMENT_UNPAID = 'unpaid';
    public const PAYMENT_PAID = 'paid';
    public const PAYMENT_FAILED = 'failed';
    public const PAYMENT_REFUNDED = 'refunded';

    public const REFUND_STATUS_NONE = 'none';
    public const REFUND_STATUS_PENDING = 'pending';
    public const REFUND_STATUS_COMPLETED = 'completed';

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
        'previous_trang_thai',
        'refund_bank_name',
        'refund_account_number',
        'refund_account_name',
        'refund_user_note',
        'refund_status',
        'refund_admin_note',
        'reason_cancel',
    ];

    protected $casts = [
        'tong_tien' => 'decimal:2',
        'phi_van_chuyen' => 'decimal:2',
        'giam_gia' => 'decimal:2',
        'thanh_tien' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function inventoryLogs()
    {
        return $this->hasMany(InventoryLog::class);
    }

    public static function adminStatusLabels(): array
    {
        return [
            self::STATUS_PENDING => 'Chờ duyệt đơn',
            self::STATUS_CONFIRMED => 'Đã duyệt đơn',
            self::STATUS_SHIPPING => 'Đang giao hàng',
            self::STATUS_DELIVERED => 'Chờ KH xác nhận',
            self::STATUS_DISPUTING => 'Đang khiếu nại',
            self::STATUS_COMPLETED => 'Hoàn thành',
            self::STATUS_CANCELLING => 'Chờ duyệt hủy',
            self::STATUS_CANCELLED => 'Đã hủy',
        ];
    }

    public static function userStatusLabels(): array
    {
        return [
            self::STATUS_PENDING => 'Chờ duyệt đơn',
            self::STATUS_CONFIRMED => 'Đang chuẩn bị hàng',
            self::STATUS_SHIPPING => 'Đang giao hàng',
            self::STATUS_DELIVERED => 'Chờ xác nhận',
            self::STATUS_DISPUTING => 'Đang xử lý khiếu nại',
            self::STATUS_COMPLETED => 'Hoàn thành',
            self::STATUS_CANCELLING => 'Đang xử lý đơn hủy',
            self::STATUS_CANCELLED => 'Đã hủy',
        ];
    }

    public static function paymentStatusLabels(): array
    {
        return [
            self::PAYMENT_PENDING => 'Chờ thanh toán',
            self::PAYMENT_UNPAID => 'Chưa thanh toán',
            self::PAYMENT_PAID => 'Đã thanh toán',
            self::PAYMENT_FAILED => 'Thanh toán thất bại',
            self::PAYMENT_REFUNDED => 'Đã hoàn tiền',
        ];
    }

    public static function adminNextStatuses(string $current): array
    {
        $map = [
            self::STATUS_PENDING => [self::STATUS_CONFIRMED, self::STATUS_CANCELLED, self::STATUS_CANCELLING],
            self::STATUS_CONFIRMED => [self::STATUS_SHIPPING, self::STATUS_CANCELLED, self::STATUS_CANCELLING],
            self::STATUS_SHIPPING => [self::STATUS_DELIVERED],
            self::STATUS_DELIVERED => [self::STATUS_COMPLETED],
            self::STATUS_DISPUTING => [self::STATUS_COMPLETED, self::STATUS_SHIPPING],
            self::STATUS_CANCELLING => [self::STATUS_CANCELLED, self::STATUS_PENDING],
            self::STATUS_COMPLETED => [],
            self::STATUS_CANCELLED => [],
        ];

        return $map[$current] ?? [];
    }

    public static function userNextStatuses(string $current): array
    {
        $map = [
            self::STATUS_DELIVERED => [self::STATUS_COMPLETED, self::STATUS_DISPUTING],
        ];

        return $map[$current] ?? [];
    }

    public static function timelineSteps(): array
    {
        return [
            ['key' => self::STATUS_PENDING, 'icon' => 'clipboard-list', 'label' => 'Đặt hàng'],
            ['key' => self::STATUS_CONFIRMED, 'icon' => 'box', 'label' => 'Chuẩn bị'],
            ['key' => self::STATUS_SHIPPING, 'icon' => 'shipping-fast', 'label' => 'Đang giao'],
            ['key' => self::STATUS_DELIVERED, 'icon' => 'user-check', 'label' => 'Đã giao'],
            ['key' => self::STATUS_COMPLETED, 'icon' => 'box-open', 'label' => 'Hoàn thành'],
        ];
    }

    public function getStatusColorAttribute(): string
    {
        $map = [
            self::STATUS_PENDING => 'warning',
            self::STATUS_CONFIRMED => 'info',
            self::STATUS_SHIPPING => 'primary',
            self::STATUS_DELIVERED => 'warning',
            self::STATUS_DISPUTING => 'danger',
            self::STATUS_COMPLETED => 'success',
            self::STATUS_CANCELLING => 'warning',
            self::STATUS_CANCELLED => 'danger',
        ];

        return $map[$this->trang_thai] ?? 'secondary';
    }

    public function getStatusLabelAttribute(): string
    {
        return self::adminStatusLabels()[$this->trang_thai] ?? $this->trang_thai;
    }

    public function getUserStatusLabelAttribute(): string
    {
        return self::userStatusLabels()[$this->trang_thai] ?? $this->trang_thai;
    }

    public function getPaymentMethodLabelAttribute(): string
    {
        $labels = [
            'cod' => 'Thanh toán khi nhận hàng',
            'vietqr' => 'Chuyển khoản VietQR',
            'bank_transfer' => 'Thanh toán khi nhận hàng',
            'vnpay' => 'VNPay',
        ];

        return $labels[$this->phuong_thuc_thanh_toan] ?? $this->phuong_thuc_thanh_toan;
    }

    public function getPaymentStatusLabelAttribute(): string
    {
        return self::paymentStatusLabels()[$this->trang_thai_thanh_toan] ?? $this->trang_thai_thanh_toan;
    }

    public function getPaymentStatusColorAttribute(): string
    {
        $map = [
            self::PAYMENT_PENDING => 'warning',
            self::PAYMENT_UNPAID => 'secondary',
            self::PAYMENT_PAID => 'success',
            self::PAYMENT_FAILED => 'danger',
            self::PAYMENT_REFUNDED => 'info',
        ];

        return $map[$this->trang_thai_thanh_toan] ?? 'secondary';
    }

    public function getTimelineIndexAttribute(): int
    {
        $trackKey = $this->trang_thai === self::STATUS_DISPUTING ? self::STATUS_DELIVERED : $this->trang_thai;
        $keys = array_column(self::timelineSteps(), 'key');
        $index = array_search($trackKey, $keys, true);

        return $index === false ? 0 : $index;
    }

    public static function generateOrderCode(): string
    {
        do {
            $code = 'ORD-' . strtoupper(substr(uniqid(), -8));
        } while (self::where('ma_don_hang', $code)->exists());

        return $code;
    }

    public function getVietqrUrlAttribute(): string
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
