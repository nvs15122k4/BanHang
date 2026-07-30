<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\VnPayService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VnPayController extends Controller
{
    public function __construct(
        protected VnPayService $vnpay
    ) {}

    public function createPayment(Request $request): JsonResponse
    {
        $request->validate([
            'order_id' => 'required|integer|exists:orders,id',
        ]);

        $order = Order::where('id', $request->order_id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ($order->trang_thai_thanh_toan === 'paid') {
            return response()->json(['message' => 'Đơn hàng đã được thanh toán!'], 400);
        }

        $paymentUrl = $this->vnpay->createPaymentUrl($order);

        return response()->json([
            'success' => true,
            'payment_url' => $paymentUrl,
            'order_code' => $order->ma_don_hang,
        ]);
    }

    public function handleReturn(Request $request): \Illuminate\Http\Response
    {
        $result = $this->vnpay->verifyReturn($request->all());

        if ($result['success']) {
            $order = Order::where('ma_don_hang', $result['order_code'])->first();
            if ($order) {
                $order->update(['trang_thai_thanh_toan' => 'paid']);
            }
        }

        $status = $result['success'] ? 'success' : 'failure';
        $orderCode = $result['order_code'];
        $html = <<<HTML
<!DOCTYPE html>
<html><head><meta charset="utf-8"><title>Kết quả thanh toán</title></head>
<body>
<script>
    window.location.href = 'banhang://payment/{$status}?order_code={$orderCode}';
</script>
<p>Đang chuyển hướng về ứng dụng...</p>
</body>
</html>
HTML;

        return response($html, 200, ['Content-Type' => 'text/html']);
    }
}
