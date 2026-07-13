<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class OrderStatusUpdated extends Notification
{
    use Queueable;

    public function __construct(public Order $order, public string $oldStatus, public string $newStatus) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $labels = [
            'pending'   => 'Chờ xác nhận',
            'confirmed' => 'Đã xác nhận',
            'shipping'  => 'Đang giao hàng',
            'completed' => 'Hoàn thành',
            'cancelling' => 'Hủy đơn hàng',
            'cancelled' => 'Đã hủy',
        ];

        $icons = [
            'pending'   => 'clock',
            'confirmed' => 'check-circle',
            'shipping'  => 'shipping-fast',
            'completed' => 'box-open',
            'cancelling' => 'undo-alt',
            'cancelled' => 'times-circle',
        ];

        $newLabel = $labels[$this->newStatus] ?? $this->newStatus;
        $icon     = $icons[$this->newStatus] ?? 'bell';

        $messages = [
            'confirmed' => "Đơn hàng #{$this->order->ma_don_hang} đã được xác nhận và đang được chuẩn bị.",
            'shipping'  => "Đơn hàng #{$this->order->ma_don_hang} đang trên đường giao đến bạn.",
            'completed' => "Đơn hàng #{$this->order->ma_don_hang} đã hoàn thành. Hãy đánh giá sản phẩm nhé!",
            'cancelled' => $this->order->trang_thai_thanh_toan === 'paid' 
                ? "Đơn hàng #{$this->order->ma_don_hang} đã được chấp nhận hủy. Vui lòng cung cấp: Tên ngân hàng, Tên chủ TK, Số TK để nhận hoàn trả số tiền " . number_format($this->order->thanh_tien) . "đ."
                : "Đơn hàng #{$this->order->ma_don_hang} đã bị hủy.",
            'cancelling' => "Yêu cầu hủy đơn hàng #{$this->order->ma_don_hang} đang được xử lý.",
            'pending'   => "Đơn hàng #{$this->order->ma_don_hang} đang chờ xác nhận.",
        ];

        $message = $messages[$this->newStatus] ?? "Đơn hàng #{$this->order->ma_don_hang} đã được cập nhật sang trạng thái {$newLabel}.";

        // Đặc biệt: Nếu Admin từ chối hủy (cancelling -> bất kỳ trạng thái nào khác cancelled)
        if ($this->oldStatus === 'cancelling' && $this->newStatus !== 'cancelled') {
            $message = "Yêu cầu hủy đơn hàng #{$this->order->ma_don_hang} đã bị từ chối. Đơn hàng sẽ tiếp tục được xử lý.";
            $newLabel = "Từ chối hủy đơn";
        }

        return [
            'type'       => 'order_status',
            'status'     => $this->newStatus,
            'icon'       => $icon,
            'title'      => "Đơn hàng: {$newLabel}",
            'message'    => $message,
            'order_id'   => $this->order->id,
            'order_code' => $this->order->ma_don_hang,
            'url'        => route('orders.show', $this->order->id),
        ];
    }
}
