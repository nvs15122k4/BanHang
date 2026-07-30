<?php

namespace App\Services;

use App\Models\Order;

class VnPayService
{
    private string $vnpUrl;
    private string $vnpTmnCode;
    private string $vnpHashSecret;
    private string $vnpReturnUrl;

    public function __construct()
    {
        $this->vnpUrl = env('VNPAY_URL', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html');
        $this->vnpTmnCode = env('VNPAY_TMN_CODE', '');
        $this->vnpHashSecret = env('VNPAY_HASH_SECRET', '');
        $this->vnpReturnUrl = env('VNPAY_RETURN_URL', 'https://banhang.vnsang.io.vn/api/payment/vnpay-return');
    }

    public function createPaymentUrl(Order $order): string
    {
        $vnpParams = [
            'vnp_Version' => '2.1.0',
            'vnp_TmnCode' => $this->vnpTmnCode,
            'vnp_Amount' => (int) round($order->thanh_tien * 100),
            'vnp_Command' => 'pay',
            'vnp_CreateDate' => now()->format('YmdHis'),
            'vnp_CurrCode' => 'VND',
            'vnp_IpAddr' => request()->ip() ?? '127.0.0.1',
            'vnp_Locale' => 'vn',
            'vnp_OrderInfo' => "Thanh toan don hang {$order->ma_don_hang}",
            'vnp_OrderType' => 'other',
            'vnp_ReturnUrl' => $this->vnpReturnUrl,
            'vnp_TxnRef' => $order->ma_don_hang,
        ];

        ksort($vnpParams);

        $query = http_build_query($vnpParams);
        $vnpSecureHash = hash_hmac('sha512', $query, $this->vnpHashSecret);
        $vnpParams['vnp_SecureHash'] = $vnpSecureHash;

        return $this->vnpUrl . '?' . http_build_query($vnpParams);
    }

    public function verifyReturn(array $input): array
    {
        $vnpSecureHash = $input['vnp_SecureHash'] ?? '';

        $data = array_filter($input, fn($key) => str_starts_with($key, 'vnp_'), ARRAY_FILTER_USE_KEY);
        unset($data['vnp_SecureHash']);

        ksort($data);
        $query = http_build_query($data);
        $checkHash = hash_hmac('sha512', $query, $this->vnpHashSecret);

        if ($vnpSecureHash !== $checkHash) {
            return ['success' => false, 'message' => 'Invalid signature'];
        }

        $responseCode = $input['vnp_ResponseCode'] ?? '99';
        $orderCode = $input['vnp_TxnRef'] ?? '';

        return [
            'success' => $responseCode === '00',
            'message' => $responseCode === '00' ? 'Thanh toán thành công!' : 'Thanh toán thất bại!',
            'order_code' => $orderCode,
            'response_code' => $responseCode,
            'transaction_no' => $input['vnp_TransactionNo'] ?? '',
            'bank_code' => $input['vnp_BankCode'] ?? '',
        ];
    }
}
