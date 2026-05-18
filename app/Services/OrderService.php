<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\InventoryLog;
use App\Models\User;
use App\Notifications\OrderStatusUpdated;
use App\Notifications\PaymentStatusUpdated;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderService
{
    /**
     * Deduct inventory when order is confirmed
     */
    public function deductInventory(Order $order): void
    {
        DB::transaction(function () use ($order) {
            foreach ($order->orderItems as $item) {
                $product = $item->product;
                
                if ($product) {
                    $oldQuantity = $product->so_luong;
                    $newQuantity = $oldQuantity - $item->so_luong;
                    
                    $product->update(['so_luong' => max(0, $newQuantity)]);
                    $this->syncProductStatus($product->fresh());
                    
                    // Log inventory change
                    InventoryLog::create([
                        'product_id' => $product->id,
                        'loai' => 'out',
                        'so_luong_truoc' => $oldQuantity,
                        'so_luong_thay_doi' => -$item->so_luong,
                        'so_luong_sau' => $newQuantity,
                        'ly_do' => "Xuất kho cho đơn hàng #{$order->ma_don_hang}",
                        'order_id' => $order->id,
                        'user_id' => Auth::id(),
                    ]);
                }
            }
        });
    }

    /**
     * Restore inventory when order is cancelled
     */
    public function restoreInventory(Order $order): void
    {
        DB::transaction(function () use ($order) {
            foreach ($order->orderItems as $item) {
                $product = $item->product;
                
                if ($product) {
                    $oldQuantity = $product->so_luong;
                    $newQuantity = $oldQuantity + $item->so_luong;
                    
                    $product->update(['so_luong' => $newQuantity]);
                    $this->syncProductStatus($product->fresh());
                    
                    // Log inventory change
                    InventoryLog::create([
                        'product_id' => $product->id,
                        'loai' => 'in',
                        'so_luong_truoc' => $oldQuantity,
                        'so_luong_thay_doi' => $item->so_luong,
                        'so_luong_sau' => $newQuantity,
                        'ly_do' => "Hoàn kho do hủy đơn hàng #{$order->ma_don_hang}",
                        'order_id' => $order->id,
                        'user_id' => Auth::id(),
                    ]);
                }
            }
        });
    }

    /**
     * Tự động đồng bộ trạng thái sản phẩm
     */
    private function syncProductStatus(Product $product): void
    {
        $newStatus = $product->so_luong > 0 ? 'con' : 'het';
        if ($product->trang_thai !== $newStatus) {
            $product->update(['trang_thai' => $newStatus]);
        }
    }

    /**
     * Admin: Cập nhật trạng thái đơn hàng
     */
    public function updateStatus(Order $order, string $newStatus): void
    {
        DB::transaction(function () use ($order, $newStatus) {
            $oldStatus = $order->trang_thai;

            // Hoàn kho khi hủy (Chỉ hoàn nếu đã từng trừ kho)
            $statusesWithDeductedInventory = ['confirmed', 'shipping', 'delivered', 'disputing'];
            if (in_array($oldStatus, $statusesWithDeductedInventory) && $newStatus === 'cancelled') {
                $this->restoreInventory($order);
                
                if ($order->trang_thai_thanh_toan === 'paid' && $order->refund_status === 'none') {
                    $order->update(['refund_status' => 'pending']);
                }
            }

            // Trừ kho khi duyệt đơn
            if ($oldStatus === 'pending' && $newStatus === 'confirmed') {
                $this->deductInventory($order);
            }

            $order->update(['trang_thai' => $newStatus]);

            if ($oldStatus !== $newStatus) {
                try {
                    $order->user->notify(new OrderStatusUpdated($order, $oldStatus, $newStatus));
                } catch (\Exception $e) {
                    Log::warning('Could not send order status notification: ' . $e->getMessage());
                }
            }
        });
    }

    /**
     * Admin: Cập nhật trạng thái thanh toán
     */
    public function updatePaymentStatus(Order $order, string $paymentStatus): void
    {
        $oldStatus = $order->trang_thai_thanh_toan;
        $order->update(['trang_thai_thanh_toan' => $paymentStatus]);

        if ($oldStatus !== $paymentStatus) {
            try {
                $order->user->notify(new PaymentStatusUpdated($order, $paymentStatus));
            } catch (\Exception $e) {
                Log::warning('Could not send payment status notification: ' . $e->getMessage());
            }
        }
    }

    /**
     * Admin: Đồng ý hủy đơn hàng
     */
    public function approveCancel(Order $order): string
    {
        return DB::transaction(function () use ($order) {
            $oldStatus = $order->trang_thai;
            
            // Hoàn tồn kho (Chỉ hoàn nếu đã từng trừ kho)
            $statusesWithDeductedInventory = ['pending', 'confirmed', 'shipping', 'delivered', 'disputing', 'cancelling'];
            if (in_array($oldStatus, $statusesWithDeductedInventory)) {
                $this->restoreInventory($order);
            }

            $updateData = [
                'trang_thai' => 'cancelled',
                'previous_trang_thai' => null,
            ];

            $message = 'Đơn hàng đã được hủy thành công.';
            if ($order->trang_thai_thanh_toan === 'paid') {
                $updateData['refund_status'] = 'pending';
                $message = 'Đơn hàng đã được chấp nhận hủy. Vui lòng kiểm tra và xử lý hoàn tiền cho khách hàng.';
            }

            $order->update($updateData);

            try {
                $order->user->notify(new OrderStatusUpdated($order, $oldStatus, 'cancelled'));
            } catch (\Exception $e) {
                Log::warning('Could not send notification: ' . $e->getMessage());
            }

            return $message;
        });
    }

    /**
     * Admin: Từ chối hủy đơn hàng
     */
    public function rejectCancel(Order $order): string
    {
        $oldStatus = $order->trang_thai;
        $revertStatus = $order->previous_trang_thai ?? 'pending';

        $order->update([
            'trang_thai' => $revertStatus,
            'previous_trang_thai' => null,
        ]);

        try {
            $order->user->notify(new OrderStatusUpdated($order, $oldStatus, $revertStatus));
        } catch (\Exception $e) {
            Log::warning('Could not send notification: ' . $e->getMessage());
        }

        return 'Đã từ chối yêu cầu hủy. Đơn hàng quay lại trạng thái ' . (Order::adminStatusLabels()[$revertStatus] ?? $revertStatus);
    }

    /**
     * Customer: Gửi yêu cầu hủy đơn hàng
     */
    public function requestCancelByUser(Order $order): void
    {
        $order->update([
            'previous_trang_thai' => $order->trang_thai,
            'trang_thai' => 'cancelling',
        ]);
    }

    /**
     * Customer: Xác nhận nhận hàng hoặc khiếu nại
     */
    public function updateStatusByUser(Order $order, string $newStatus): string
    {
        $oldStatus = $order->trang_thai;
        $order->update(['trang_thai' => $newStatus]);

        try {
            $order->user->notify(new OrderStatusUpdated($order, $oldStatus, $newStatus));
        } catch (\Exception $e) {
            Log::warning('Could not send order status notification: ' . $e->getMessage());
        }

        $messages = [
            'completed' => 'Cảm ơn bạn đã xác nhận nhận hàng!',
            'disputing' => 'Khiếu nại của bạn đã được gửi. Chúng tôi sẽ xử lý sớm nhất.',
        ];

        return $messages[$newStatus] ?? 'Cập nhật trạng thái thành công!';
    }

    /**
     * Checkout: Tạo đơn hàng mới từ giỏ hàng
     */
    public function createOrderFromCart(User $user, array $validated, $cartItems): Order
    {
        return DB::transaction(function () use ($user, $validated, $cartItems) {
            // Verify stock availability one more time
            foreach ($cartItems as $item) {
                if ($item->product->so_luong < $item->so_luong) {
                    throw new \Exception("Sản phẩm \"{$item->product->ten_sp}\" đã hết hàng hoặc không đủ tồn kho!");
                }
            }

            // Calculate totals
            $tongTien = $cartItems->sum(function($item) {
                return ($item->product->promo_price ?? $item->product->gia) * $item->so_luong;
            });

            // Create Order
            $order = Order::create([
                'ma_don_hang'            => Order::generateOrderCode(),
                'user_id'                => $user->id,
                'ten_nguoi_nhan'         => $validated['recipient_name'],
                'sdt_nguoi_nhan'         => $validated['phone'],
                'dia_chi_giao_hang'      => $validated['dia_chi_giao_hang'],
                'tong_tien'              => $tongTien,
                'phi_van_chuyen'         => 0,
                'giam_gia'               => 0,
                'thanh_tien'             => $tongTien,
                'trang_thai'             => 'pending',
                'phuong_thuc_thanh_toan' => $validated['phuong_thuc_thanh_toan'],
                'trang_thai_thanh_toan'  => 'unpaid',
                'ghi_chu'                => $validated['ghi_chu'] ?? null,
            ]);

            // Create Order Items
            foreach ($cartItems as $item) {
                $product = $item->product;
                $finalPrice = $product->promo_price ?? $product->gia;

                OrderItem::create([
                    'order_id'     => $order->id,
                    'product_id'   => $product->id,
                    'ten_san_pham' => $product->ten_sp,
                    'gia'          => $finalPrice,
                    'so_luong'     => $item->so_luong,
                    'thanh_tien'   => $finalPrice * $item->so_luong,
                ]);
            }

            // Clear Cart
            $user->carts()->delete();

            return $order;
        });
    }
}
