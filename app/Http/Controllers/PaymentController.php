<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Notifications\PaymentStatusUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    /**
     * Webhook xử lý thông báo thanh toán (IPN)
     * Đây là endpoint để Postman hoặc Cổng thanh toán bắn dữ liệu vào
     */
    public function webhook(Request $request)
    {
        // 1. Lấy dữ liệu từ request
        $orderCode = $request->input('order_code'); // Mã đơn hàng (VD: ORD-12345)
        $amount    = $request->input('amount');
        $status    = $request->input('status'); // success
        $signature = $request->header('X-Payment-Signature'); // Chữ ký bảo mật

        Log::info("Payment Webhook received", $request->all());

        // 2. Kiểm tra tính hợp lệ (Bảo mật)
        // Nếu ở production, bắt buộc phải kiểm tra signature
        if (app()->environment('production')) {
            $secret = config('services.payment.secret');
            $validSignature = hash_hmac('sha256', $orderCode . $amount, $secret);
            
            if ($signature !== $validSignature) {
                return response()->json(['message' => 'Unauthorized signature'], 401);
            }
        } else {
            // Ở Local: Nếu bạn muốn chặn Postman "bừa bãi", có thể yêu cầu 1 mã test đơn giản
            // Ví dụ: header X-Payment-Signature phải là 'local-test-key'
            if ($signature !== 'local-test-key') {
                return response()->json(['message' => 'Missing local test signature'], 401);
            }
        }

        // 3. Xử lý cập nhật đơn hàng
        $order = Order::where('ma_don_hang', $orderCode)->first();

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        if ($status === 'success') {
            // Cập nhật trạng thái thanh toán
            $order->update([
                'trang_thai_thanh_toan' => 'paid'
            ]);

            // Gửi thông báo cho khách hàng
            try {
                $order->user->notify(new PaymentStatusUpdated($order, 'paid'));
            } catch (\Exception $e) {
                Log::warning('Payment notification failed: ' . $e->getMessage());
            }

            return response()->json(['message' => 'Payment updated successfully']);
        }

        return response()->json(['message' => 'Payment failed or invalid status'], 400);
    }
}
