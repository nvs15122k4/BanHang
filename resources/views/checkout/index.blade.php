@extends('layouts.app')

@section('title', 'Thanh toán - AVA')

@push('styles')
<style>
    /* =========================================
       CHECKOUT PAGE - AVA STYLE
       ========================================= */
    .page-title {
        font-weight: 700;
        font-size: 32px;
        color: var(--text-main);
        text-align: center;
        margin: 40px 0;
        text-transform: uppercase;
    }

    .checkout-section {
        border: 1px solid #EEEEEE;
        padding: 30px;
        margin-bottom: 30px;
    }

    .section-title {
        font-weight: 700;
        font-size: 20px;
        text-transform: uppercase;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 1px solid #EEEEEE;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    /* Form Fields */
    .form-label {
        font-weight: 600;
        color: var(--text-main);
        font-size: 14px;
        text-transform: uppercase;
    }
    
    .form-control {
        border-radius: 0;
        border: 1px solid #DDDDDD;
        padding: 12px 15px;
    }

    /* Address Cards */
    .address-card {
        border: 1px solid #EEEEEE;
        padding: 20px;
        cursor: pointer;
        transition: all 0.3s;
        margin-bottom: 15px;
        position: relative;
    }
    
    .address-card:hover {
        border-color: #CCCCCC;
    }
    
    .address-card.selected {
        border-color: var(--text-main);
        background: #F9F9F9;
    }
    
    .address-radio {
        accent-color: var(--text-main);
        width: 18px;
        height: 18px;
        margin-top: 3px;
    }

    /* Payment Options */
    .payment-option {
        border: 1px solid #EEEEEE;
        padding: 20px;
        cursor: pointer;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 15px;
        transition: all 0.3s;
    }
    
    .payment-option:hover {
        border-color: #CCCCCC;
    }
    
    .payment-option.selected {
        border-color: var(--text-main);
        background: #F9F9F9;
    }
    
    .payment-icon {
        font-size: 24px;
        color: var(--text-main);
    }

    /* Order Summary */
    .summary-panel {
        background: #F6F6F6;
        padding: 30px;
        position: sticky;
        top: 40px;
    }
    
    .order-item {
        display: flex;
        gap: 15px;
        padding: 15px 0;
        border-bottom: 1px solid #EEEEEE;
    }
    
    .item-img {
        width: 60px;
        height: 60px;
        object-fit: cover;
        background: #fff;
    }
    
    .item-info {
        flex: 1;
    }
    
    .item-title {
        font-weight: 600;
        font-size: 14px;
        margin-bottom: 5px;
        color: var(--text-main);
    }
    
    .item-qty {
        font-size: 13px;
        color: var(--text-light);
    }
    
    .item-price {
        font-weight: 700;
        font-size: 14px;
        color: var(--text-main);
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 15px;
        color: var(--text-main);
    }
    
    .summary-total {
        display: flex;
        justify-content: space-between;
        margin-top: 20px;
        padding-top: 20px;
        border-top: 1px solid #DDDDDD;
        font-weight: 700;
        font-size: 24px;
    }

    .btn-place-order {
        background: var(--text-main);
        color: #fff;
        border: none;
        padding: 15px;
        font-weight: 700;
        font-size: 16px;
        text-transform: uppercase;
        width: 100%;
        margin-top: 20px;
        transition: all 0.3s;
    }
    
    .btn-place-order:hover {
        background: var(--primary);
        color: var(--text-main);
    }
</style>
@endpush

@section('content')
<div class="container py-4">
    <h1 class="page-title">Thanh toán</h1>

    @if(session('error'))
        <div class="alert alert-danger rounded-0 border-0" style="background-color: #FFEBEE; color: #C62828;">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        </div>
    @endif

    <form action="{{ route('checkout.store') }}" method="POST" id="checkoutForm">
        @csrf
        
        <div class="row">
            <div class="col-lg-7">
                
                <!-- DELIVERY INFO -->
                <div class="checkout-section">
                    <h2 class="section-title"><i class="fas fa-map-marker-alt"></i> Địa chỉ giao hàng</h2>
                    
                    @if(auth()->check() && count($addresses) > 0)
                        <div class="mb-4">
                            <p class="mb-3 fw-bold">Chọn từ địa chỉ của bạn:</p>
                            @foreach($addresses as $index => $address)
                                <label class="address-card w-100 d-flex gap-3 {{ $index === 0 ? 'selected' : '' }}" onclick="selectAddress(this)">
                                    <input type="radio" name="address_id" value="{{ $address->id }}" class="address-radio"
                                        {{ $index === 0 ? 'checked' : '' }}
                                        data-name="{{ $address->recipient_name }}"
                                        data-phone="{{ $address->phone }}"
                                        data-address="{{ $address->full_address }}"
                                        onchange="populateFromRadio(this)">
                                    <div style="flex: 1;">
                                        <div class="fw-bold mb-1">{{ $address->recipient_name }} <span class="text-muted fw-normal">| {{ $address->phone }}</span></div>
                                        <div class="text-muted" style="font-size: 14px;">{{ $address->full_address }}</div>
                                    </div>
                                    @if($address->is_default)
                                        <span class="badge bg-dark" style="height: fit-content;">Mặc định</span>
                                    @endif
                                </label>
                            @endforeach
                            
                            <label class="address-card w-100 d-flex gap-3" onclick="selectAddress(this)">
                                <input type="radio" name="address_id" value="new" class="address-radio" onchange="clearForm()">
                                <div class="fw-bold">Sử dụng địa chỉ khác</div>
                            </label>
                        </div>
                    @endif

                    <div id="addressForm">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Họ và tên <span class="text-danger">*</span></label>
                                <input type="text" name="ten_nguoi_nhan" id="ten_nguoi_nhan" class="form-control" value="{{ old('ten_nguoi_nhan', auth()->user()->name ?? '') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                                <input type="text" name="sdt_nguoi_nhan" id="sdt_nguoi_nhan" class="form-control" value="{{ old('sdt_nguoi_nhan', auth()->user()->phone ?? '') }}" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Địa chỉ chi tiết <span class="text-danger">*</span></label>
                                <input type="text" name="dia_chi_giao_hang" id="dia_chi_giao_hang" class="form-control" value="{{ old('dia_chi_giao_hang', auth()->user()->address ?? '') }}" placeholder="Số nhà, tên đường, phường/xã, quận/huyện, tỉnh/thành phố" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Ghi chú đơn hàng (Tùy chọn)</label>
                                <textarea name="ghi_chu" class="form-control" rows="3" placeholder="Ghi chú về đơn hàng của bạn, ví dụ: lưu ý đặc biệt khi giao hàng.">{{ old('ghi_chu') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="checkout-section">
                    <h2 class="section-title"><i class="fas fa-credit-card"></i> Phương thức thanh toán</h2>
                    
                    <label class="payment-option w-100 selected" onclick="selectPayment(this)">
                        <input type="radio" name="phuong_thuc_thanh_toan" value="cod" class="address-radio" checked style="display:none;">
                        <i class="fas fa-truck payment-icon"></i>
                        <div>
                            <div class="fw-bold">Thanh toán khi nhận hàng (COD)</div>
                            <div class="text-muted" style="font-size: 13px;">Thanh toán bằng tiền mặt khi nhận hàng.</div>
                        </div>
                        <i class="fas fa-check-circle ms-auto text-success" style="font-size: 20px;"></i>
                    </label>
                    
                    <label class="payment-option w-100" onclick="selectPayment(this)">
                        <input type="radio" name="phuong_thuc_thanh_toan" value="bank_transfer" class="address-radio" style="display:none;">
                        <i class="fas fa-university payment-icon"></i>
                        <div>
                            <div class="fw-bold">Chuyển khoản ngân hàng</div>
                            <div class="text-muted" style="font-size: 13px;">Thực hiện thanh toán trực tiếp vào tài khoản ngân hàng của chúng tôi.</div>
                        </div>
                        <i class="fas fa-check-circle ms-auto text-success check-icon" style="font-size: 20px; display: none;"></i>
                    </label>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="summary-panel">
                    <h2 class="section-title">Đơn hàng của bạn</h2>
                    
                    <div style="max-height: 350px; overflow-y: auto; padding-right: 10px; margin-bottom: 20px;">
                        @foreach($items as $item)
                            @php $product = $item['product']; @endphp
                            <div class="order-item">
                                @if($product->anh)
                                    <img src="{{ $product->image_path }}" class="item-img" alt="{{ $product->ten_sp }}">
                                @else
                                    <div class="item-img d-flex align-items-center justify-content-center border">
                                        <i class="fas fa-image text-muted"></i>
                                    </div>
                                @endif
                                <div class="item-info">
                                    <div class="item-title">{{ $product->ten_sp }}</div>
                                    <div class="d-flex justify-content-between">
                                        <span class="item-qty">Số lượng: {{ $item['so_luong'] }}</span>
                                        <span class="item-price">{{ number_format($item['subtotal']) }}đ</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <div class="pt-3">
                        <div class="summary-row">
                            <span>Tạm tính</span>
                            <span>{{ number_format($total) }}đ</span>
                        </div>
                        <div class="summary-row">
                            <span>Phí vận chuyển</span>
                            <span>Miễn phí</span>
                        </div>
                        
                        <div class="summary-total">
                            <span>Tổng cộng</span>
                            <span>{{ number_format($total) }}đ</span>
                        </div>
                        
                        <button type="submit" class="btn-place-order">ĐẶT HÀNG</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    function selectAddress(element) {
        document.querySelectorAll('.address-card').forEach(el => el.classList.remove('selected'));
        element.classList.add('selected');
    }
    
    function populateFromRadio(radio) {
        document.getElementById('ten_nguoi_nhan').value = radio.dataset.name || '';
        document.getElementById('sdt_nguoi_nhan').value = radio.dataset.phone || '';
        document.getElementById('dia_chi_giao_hang').value = radio.dataset.address || '';
    }
    
    function clearForm() {
        document.getElementById('ten_nguoi_nhan').value = '';
        document.getElementById('sdt_nguoi_nhan').value = '';
        document.getElementById('dia_chi_giao_hang').value = '';
    }
    
    function selectPayment(element) {
        document.querySelectorAll('.payment-option').forEach(el => {
            el.classList.remove('selected');
            const check = el.querySelector('.check-icon, .fa-check-circle');
            if(check) check.style.display = 'none';
        });
        element.classList.add('selected');
        const check = element.querySelector('.fa-check-circle');
        if(check) check.style.display = 'block';
        element.querySelector('input').checked = true;
    }

    // Auto-populate form with first (default) address on page load
    document.addEventListener('DOMContentLoaded', function() {
        const firstChecked = document.querySelector('input[name="address_id"]:checked');
        if (firstChecked && firstChecked.value !== 'new') {
            populateFromRadio(firstChecked);
        }
    });
</script>
@endpush
@endsection
