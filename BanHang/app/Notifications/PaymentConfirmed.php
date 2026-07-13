<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PaymentConfirmed extends Notification
{
    use Queueable;

    public function __construct(public Order $order) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'       => 'order_completed',
            'title'      => 'Đơn hàng đã hoàn thành',
            'message'    => "Đơn hàng #{$this->order->ma_don_hang} đã hoàn thành. Hãy đánh giá sản phẩm để chia sẻ trải nghiệm của bạn!",
            'order_id'   => $this->order->id,
            'order_code' => $this->order->ma_don_hang,
            'url'        => route('orders.show', $this->order->id),
        ];
    }
}
