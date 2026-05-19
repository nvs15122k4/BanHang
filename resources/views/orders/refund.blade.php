@extends('layouts.app')

@section('title', 'Thông tin hoàn tiền - ' . $order->id)

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-dark text-white py-3">
                    <h5 class="mb-0 text-uppercase fw-bold uix-2ecb5783f5">Cung cấp thông tin hoàn tiền</h5>
                </div>
                <div class="card-body p-4">
                    <p class="text-muted mb-4">Vui lòng cung cấp chính xác thông tin tài khoản ngân hàng để chúng tôi thực hiện hoàn tiền cho đơn hàng <strong>#{{ $order->ma_don_hang }}</strong>.</p>

                    <form action="{{ route('orders.submitRefund', $order) }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold text-uppercase small uix-5db9ed9f68">Số tiền hoàn trả</label>
                            <input type="text" class="form-control rounded-0 bg-light fw-bold" value="{{ number_format($order->thanh_tien) }}đ" readonly>
                            <div class="form-text">Số tiền này tương ứng với tổng giá trị đơn hàng đã thanh toán.</div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold text-uppercase small uix-5db9ed9f68">Tên ngân hàng</label>
                            <input type="text" name="refund_bank_name" class="form-control rounded-0 @error('refund_bank_name') is-invalid @enderror" value="{{ old('refund_bank_name', $order->refund_bank_name) }}" placeholder="Ví dụ: Vietcombank, Techcombank..." required>
                            @error('refund_bank_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-uppercase small uix-5db9ed9f68">Số tài khoản</label>
                            <input type="text" name="refund_account_number" class="form-control rounded-0 @error('refund_account_number') is-invalid @enderror" value="{{ old('refund_account_number', $order->refund_account_number) }}" required>
                            @error('refund_account_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-uppercase small uix-5db9ed9f68">Tên chủ tài khoản</label>
                            <input type="text" name="refund_account_name" class="form-control rounded-0 @error('refund_account_name') is-invalid @enderror" value="{{ old('refund_account_name', $order->refund_account_name) }}" placeholder="NGUYEN VAN A" required>
                            @error('refund_account_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold text-uppercase small uix-5db9ed9f68">Nội dung bổ sung (nếu có)</label>
                            <textarea name="refund_user_note" class="form-control rounded-0" rows="3">{{ old('refund_user_note', $order->refund_user_note) }}</textarea>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-dark py-3 fw-bold text-uppercase uix-a7a4d766bd">
                                <i class="fas fa-paper-plane me-2"></i>Gửi thông tin
                            </button>
                            <a href="{{ route('orders.show', $order) }}" class="btn btn-outline-secondary py-3 fw-bold text-uppercase uix-a7a4d766bd">Quay lại</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
