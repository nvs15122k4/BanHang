<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'auditable_type',
        'auditable_id',
        'description',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function auditable()
    {
        return $this->morphTo();
    }

    public function getActionLabelAttribute(): string
    {
        return self::actionLabelFor($this->action);
    }

    public static function actionLabelFor(string $action): string
    {
        $labels = [
            'product_created' => 'Tạo sản phẩm',
            'product_updated' => 'Cập nhật sản phẩm',
            'product_deleted' => 'Xóa sản phẩm',
            'product_restored' => 'Khôi phục sản phẩm',
            'product_status_updated' => 'Cập nhật trạng thái sản phẩm',
            'product_stock_updated' => 'Cập nhật tồn kho sản phẩm',
            'inventory_imported' => 'Nhập kho',
            'inventory_exported' => 'Xuất kho',
            'inventory_adjusted' => 'Điều chỉnh tồn kho',
            'order_status_updated' => 'Cập nhật trạng thái đơn hàng',
            'payment_status_updated' => 'Cập nhật trạng thái thanh toán',
            'order_cancel_requested' => 'Khách yêu cầu hủy đơn',
            'customer_order_status_updated' => 'Khách cập nhật trạng thái đơn',
            'admin_order_created' => 'Admin tạo đơn hàng',
            'refund_info_submitted' => 'Khách gửi thông tin hoàn tiền',
            'order_cancel_approved' => 'Duyệt hủy đơn',
            'order_cancel_rejected' => 'Từ chối hủy đơn',
            'refund_status_updated' => 'Cập nhật trạng thái hoàn tiền',
            'user_role_updated' => 'Cập nhật vai trò người dùng',
            'user_status_updated' => 'Cập nhật trạng thái tài khoản',
            'user_created' => 'Tạo người dùng',
        ];

        return $labels[$action] ?? str($action)->replace('_', ' ')->title()->toString();
    }

    public function getAuditableLabelAttribute(): string
    {
        if (! $this->auditable_type) {
            return 'Không có đối tượng cụ thể';
        }

        $labels = [
            Product::class => 'Sản phẩm',
            Order::class => 'Đơn hàng',
            User::class => 'Người dùng',
            InventoryLog::class => 'Phiếu kho',
        ];

        $type = $labels[$this->auditable_type] ?? class_basename($this->auditable_type);

        return $this->auditable_id ? "{$type} #{$this->auditable_id}" : $type;
    }

    public function getDescriptionLabelAttribute(): string
    {
        if (! $this->description) {
            return $this->action_label;
        }

        $patterns = [
            '/^Created product (.+)$/u' => 'Đã tạo sản phẩm :value',
            '/^Updated product (.+)$/u' => 'Đã cập nhật sản phẩm :value',
            '/^Deleted product (.+)$/u' => 'Đã xóa sản phẩm :value',
            '/^Restored product (.+)$/u' => 'Đã khôi phục sản phẩm :value',
            '/^Updated status for (.+)$/u' => 'Đã cập nhật trạng thái cho :value',
            '/^Updated stock for (.+)$/u' => 'Đã cập nhật tồn kho cho sản phẩm :value',
            '/^Updated role for (.+)$/u' => 'Đã cập nhật vai trò cho tài khoản :value',
            '/^Imported stock for (.+)$/u' => 'Đã nhập kho cho sản phẩm :value',
            '/^Exported stock for (.+)$/u' => 'Đã xuất kho cho sản phẩm :value',
            '/^Adjusted stock for (.+)$/u' => 'Đã điều chỉnh tồn kho cho sản phẩm :value',
            '/^Admin tao don (.+)$/u' => 'Admin đã tạo đơn hàng :value',
        ];

        foreach ($patterns as $pattern => $replacement) {
            if (preg_match($pattern, $this->description, $matches)) {
                return str_replace(':value', $matches[1], $replacement);
            }
        }

        return $this->description;
    }

    public function getChangeRowsAttribute(): array
    {
        $oldValues = $this->old_values ?? [];
        $newValues = $this->new_values ?? [];
        $keys = array_values(array_unique(array_merge(array_keys($oldValues), array_keys($newValues))));

        return array_map(function (string $key) use ($oldValues, $newValues) {
            return [
                'field' => $this->fieldLabel($key),
                'old' => array_key_exists($key, $oldValues) ? $this->valueLabel($key, $oldValues[$key]) : null,
                'new' => array_key_exists($key, $newValues) ? $this->valueLabel($key, $newValues[$key]) : null,
            ];
        }, $keys);
    }

    private function fieldLabel(string $field): string
    {
        $labels = [
            'ten_sp' => 'Tên sản phẩm',
            'loai' => 'Loại sản phẩm',
            'gia' => 'Giá',
            'so_luong' => 'Số lượng tồn',
            'change' => 'Mức thay đổi',
            'trang_thai' => 'Trạng thái',
            'trang_thai_thanh_toan' => 'Trạng thái thanh toán',
            'thanh_tien' => 'Thành tiền',
            'role' => 'Vai trò',
            'is_active' => 'Trạng thái tài khoản',
            'previous_trang_thai' => 'Trạng thái trước đó',
            'refund_status' => 'Trạng thái hoàn tiền',
            'refund_bank_name' => 'Ngân hàng hoàn tiền',
            'refund_account_number' => 'Số tài khoản hoàn tiền',
            'refund_admin_note' => 'Ghi chú hoàn tiền của admin',
        ];

        return $labels[$field] ?? str($field)->replace('_', ' ')->ucfirst()->toString();
    }

    private function valueLabel(string $field, mixed $value): string
    {
        if ($value === null || $value === '') {
            return 'Không có';
        }

        if (is_bool($value)) {
            return $value ? 'Có' : 'Không';
        }

        if (is_array($value)) {
            return implode(', ', array_map(fn ($item) => $this->valueToString($item), $value));
        }

        $stringValue = (string) $value;

        $maps = [
            'trang_thai_thanh_toan' => Order::paymentStatusLabels(),
            'refund_status' => [
                Order::REFUND_STATUS_NONE => 'Không hoàn tiền',
                Order::REFUND_STATUS_PENDING => 'Chờ hoàn tiền',
                Order::REFUND_STATUS_COMPLETED => 'Đã hoàn tiền',
            ],
            'role' => [
                'admin' => 'Quản trị viên',
                'user' => 'Khách hàng',
            ],
            'is_active' => [
                '1' => 'Đang hoạt động',
                '0' => 'Đã vô hiệu hóa',
            ],
        ];

        if ($field === 'trang_thai' || $field === 'previous_trang_thai') {
            $statusLabels = str_starts_with($this->action, 'product_')
                ? ['con' => 'Còn hàng', 'het' => 'Hết hàng']
                : Order::adminStatusLabels();

            return $statusLabels[$stringValue] ?? $stringValue;
        }

        if (isset($maps[$field][$stringValue])) {
            return $maps[$field][$stringValue];
        }

        if (in_array($field, ['gia', 'thanh_tien'], true) && is_numeric($value)) {
            return number_format((float) $value, 0, ',', '.') . ' đ';
        }

        if ($field === 'change' && is_numeric($value)) {
            return ((float) $value > 0 ? '+' : '') . $stringValue;
        }

        return $stringValue;
    }

    private function valueToString(mixed $value): string
    {
        if ($value === null || $value === '') {
            return 'Không có';
        }

        if (is_bool($value)) {
            return $value ? 'Có' : 'Không';
        }

        return is_scalar($value) ? (string) $value : json_encode($value, JSON_UNESCAPED_UNICODE);
    }

    public static function record(
        string $action,
        ?Model $auditable = null,
        ?string $description = null,
        ?array $oldValues = null,
        ?array $newValues = null
    ): void {
        if (! Schema::hasTable('audit_logs')) {
            return;
        }

        self::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'auditable_type' => $auditable ? $auditable::class : null,
            'auditable_id' => $auditable?->getKey(),
            'description' => $description,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()?->ip(),
            'user_agent' => substr((string) request()?->userAgent(), 0, 500),
        ]);
    }
}
