<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PaymentStatusUpdated extends Notification
{
    use Queueable;

    public function __construct(public Order $order, public string $newStatus) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $isPaid = $this->newStatus === 'paid';

        return [
            'type'       => 'payment_status',
            'status'     => $this->newStatus,
            'icon'       => $isPaid ? 'check-circle' : 'times-circle',
            'title'      => $isPaid ? 'Thanh toán xác nhận' : 'Chưa thanh toán',
            'message'    => $isPaid
                ? "Đơn hàng #{$this->order->ma_don_hang} đã được xác nhận thanh toán."
                : "Đơn hàng #{$this->order->ma_don_hang} được đánh dấu chưa thanh toán.",
            'order_id'   => $this->order->id,
            'order_code' => $this->order->ma_don_hang,
            'url'        => route('orders.show', $this->order->id),
        ];
    }
}
