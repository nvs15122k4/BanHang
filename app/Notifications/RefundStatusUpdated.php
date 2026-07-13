<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class RefundStatusUpdated extends Notification
{
    use Queueable;

    public function __construct(public Order $order, public string $status) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $statusLabels = [
            'pending' => 'Đang xử lý',
            'completed' => 'Đã hoàn tiền',
        ];

        $label = $statusLabels[$this->status] ?? $this->status;
        
        $message = "Yêu cầu hoàn tiền cho đơn hàng #{$this->order->ma_don_hang} đã được cập nhật sang trạng thái: {$label}.";
        
        if ($this->status === 'completed') {
            $message = "Đơn hàng #{$this->order->ma_don_hang} đã được hoàn tiền thành công số tiền " . number_format($this->order->thanh_tien) . "đ. Vui lòng kiểm tra tài khoản ngân hàng của bạn.";
        }

        return [
            'type'       => 'refund_status',
            'status'     => $this->status,
            'icon'       => $this->status === 'completed' ? 'check-double' : 'clock',
            'title'      => "Hoàn tiền: {$label}",
            'message'    => $message,
            'order_id'   => $this->order->id,
            'order_code' => $this->order->ma_don_hang,
            'url'        => route('orders.show', $this->order->id),
        ];
    }
}
