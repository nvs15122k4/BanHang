@extends('layouts.app')

@section('title', 'Chi tiết đơn hàng - ' . $order->id)

@push('styles')
<style>
    /* =========================================
       ORDER DETAILS - AVA STYLE
       ========================================= */
    .page-title {
        font-weight: 700;
        font-size: 28px;
        color: var(--text-main);
        text-transform: uppercase;
        margin-bottom: 0;
    }

    .order-section {
        border: 1px solid #EEEEEE;
        margin-bottom: 30px;
        background: #fff;
    }

    .section-header {
        background: #F6F6F6;
        padding: 15px 20px;
        border-bottom: 1px solid #EEEEEE;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 16px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .section-body {
        padding: 20px;
    }

    /* Status Track */
    .status-track {
        display: flex;
        justify-content: space-between;
        position: relative;
        margin: 40px 20px 20px;
    }
    
    .status-track::before {
        content: '';
        position: absolute;
        top: 20px;
        left: 0;
        width: 100%;
        height: 2px;
        background: #EEEEEE;
        z-index: 0;
    }
    
    .status-step {
        position: relative;
        z-index: 1;
        text-align: center;
        flex: 1;
    }
    
    .status-icon {
        width: 40px;
        height: 40px;
        background: #EEEEEE;
        color: #999999;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 10px;
        font-size: 16px;
        transition: all 0.3s;
    }
    
    .status-label {
        font-weight: 600;
        font-size: 13px;
        color: #999999;
        text-transform: uppercase;
    }
    
    .status-step.active .status-icon {
        background: var(--text-main);
        color: #FFFFFF;
    }
    
    .status-step.active .status-label {
        color: var(--text-main);
    }
    
    .status-step.active ~ .status-step .status-icon {
        background: #EEEEEE;
    }

    /* Cancelled State */
    .cancelled-state {
        text-align: center;
        padding: 40px 20px;
    }

    /* Order Items */
    .order-item {
        display: flex;
        align-items: center;
        gap: 20px;
        padding: 15px 0;
        border-bottom: 1px dashed #EEEEEE;
    }
    
    .order-item:first-child { padding-top: 0; }
    .order-item:last-child { padding-bottom: 0; border-bottom: none; }
    
    .item-img {
        width: 80px;
        height: 80px;
        object-fit: cover;
        background: #F6F6F6;
    }
    
    .item-title {
        font-weight: 600;
        color: var(--text-main);
        font-size: 15px;
        margin-bottom: 5px;
    }
    
    .item-meta {
        color: var(--text-light);
        font-size: 14px;
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
        color: var(--text-light);
    }
    
    .summary-total {
        display: flex;
        justify-content: space-between;
        margin-top: 15px;
        padding-top: 15px;
        border-top: 1px solid #EEEEEE;
        font-weight: 700;
        font-size: 20px;
        color: var(--text-main);
    }

    .info-label {
        font-size: 12px;
        color: var(--text-light);
        text-transform: uppercase;
        font-weight: 600;
        margin-bottom: 5px;
    }
    
    .info-value {
        font-weight: 500;
        margin-bottom: 15px;
    }

    .badge-paid {
        background: #27AE60;
        color: white;
        padding: 4px 12px;
        font-size: 12px;
        font-weight: 600;
    }
    
    .badge-unpaid {
        background: #999999;
        color: white;
        padding: 4px 12px;
        font-size: 12px;
        font-weight: 600;
    }
</style>
@endpush

@section('content')
<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title">ĐƠN HÀNG #{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</h1>
        <a href="{{ route('orders.index') }}" class="btn btn-outline-dark" style="border-radius: 0;">VỀ DANH SÁCH</a>
    </div>

    <div class="row">
        <!-- Main Info -->
        <div class="col-lg-8">
            
            <!-- Order Status -->
            <div class="order-section">
                <div class="section-header">
                    <i class="fas fa-truck"></i> TRẠNG THÁI ĐƠN HÀNG
                </div>
                <div class="section-body">
                    @if($order->trang_thai === 'cancelled')
                        <div class="cancelled-state">
                            <i class="fas fa-times-circle fa-4x text-danger mb-3"></i>
                            <h4 style="font-weight: 700; color: #EB5757;">Đơn hàng đã hủy</h4>
                            <p class="text-muted">Đơn hàng này đã bị hủy và sẽ không được xử lý.</p>
                        </div>
                    @else
                        @php
                            $steps = [
                                ['key' => 'pending',   'icon' => 'clipboard-list', 'label' => 'Chờ xác nhận'],
                                ['key' => 'confirmed', 'icon' => 'box',            'label' => 'Đang chuẩn bị hàng'],
                                ['key' => 'shipping',  'icon' => 'shipping-fast',  'label' => 'Đang giao hàng'],
                                ['key' => 'delivered', 'icon' => 'user-check',     'label' => 'Chờ xác nhận'],
                                ['key' => 'completed', 'icon' => 'box-open',       'label' => 'Hoàn thành'],
                            ];

                            // disputing vẫn nằm ở bước delivered trên track
                            $trackKey   = $order->trang_thai === 'disputing' ? 'delivered' : $order->trang_thai;
                            $stepKeys   = array_column($steps, 'key');
                            $currentIdx = array_search($trackKey, $stepKeys) ?? 0;
                        @endphp

                        <div class="status-track">
                            @foreach($steps as $i => $step)
                                <div class="status-step {{ $i <= $currentIdx ? 'active' : '' }}">
                                    <div class="status-icon"><i class="fas fa-{{ $step['icon'] }}"></i></div>
                                    <div class="status-label">{{ $step['label'] }}</div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Banner trạng thái đặc biệt --}}
                        @if($order->trang_thai === 'disputing')
                            <div class="text-center mt-3 py-3" style="background:#FFF5F5; border:1px solid #FFCCCC;">
                                <i class="fas fa-exclamation-circle fa-2x mb-2" style="color:#EB5757;"></i>
                                <p class="mb-0 fw-bold" style="color:#EB5757;">Đang xử lý khiếu nại — chúng tôi sẽ liên hệ bạn sớm nhất</p>
                            </div>
                        @endif

                        {{-- Nút hành động của user khi đơn đang giao --}}
                        @php $userNext = \App\Models\Order::userNextStatuses($order->trang_thai); @endphp
                        @if(count($userNext) > 0)
                            <div class="mt-4 p-3" style="background:#F9F9F9; border:1px solid #EEE;">
                                <p class="fw-bold mb-3" style="font-size:14px;">Bạn đã nhận được hàng chưa?</p>
                                <div class="d-flex gap-2 flex-wrap">
                                    {{-- Đã nhận --}}
                                    <form method="POST" action="{{ route('orders.updateStatus', $order) }}" style="display:inline;">
                                        @csrf
                                        <input type="hidden" name="trang_thai" value="completed">
                                        <button type="submit" class="btn btn-success"
                                            style="border-radius:0; font-weight:700; text-transform:uppercase; font-size:13px; letter-spacing:1px;"
                                            onclick="return confirm('Xác nhận bạn đã nhận được hàng?')">
                                            <i class="fas fa-check-circle me-2"></i>Đã nhận được hàng
                                        </button>
                                    </form>
                                    {{-- Chưa nhận / Khiếu nại --}}
                                    <form method="POST" action="{{ route('orders.updateStatus', $order) }}" style="display:inline;">
                                        @csrf
                                        <input type="hidden" name="trang_thai" value="disputing">
                                        <button type="submit" class="btn btn-outline-danger"
                                            style="border-radius:0; font-weight:700; text-transform:uppercase; font-size:13px; letter-spacing:1px;"
                                            onclick="return confirm('Xác nhận bạn chưa nhận được hàng và muốn khiếu nại?')">
                                            <i class="fas fa-exclamation-circle me-2"></i>Chưa nhận được hàng
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
            </div>

            <!-- Ordered Items -->
            <div class="order-section">
                <div class="section-header">
                    <i class="fas fa-box"></i> SẢN PHẨM ĐÃ ĐẶT
                </div>
                <div class="section-body">
                    @foreach($order->orderItems as $detail)
                        <div class="order-item">
                            @if($detail->product && $detail->product->anh)
                                <img src="{{ $detail->product->image_path }}" class="item-img" alt="Product">
                            @else
                                <div class="item-img d-flex align-items-center justify-content-center border">
                                    <i class="fas fa-image text-muted"></i>
                                </div>
                            @endif
                            
                            <div style="flex:1;">
                                <div class="item-title">
                                    @if($detail->product)
                                        <a href="{{ route('products.show', $detail->product->id) }}" style="color:inherit; text-decoration:none;">
                                            {{ $detail->product->ten_sp }}
                                        </a>
                                    @else
                                        Sản phẩm đã xóa
                                    @endif
                                </div>
                                <div class="item-meta">{{ number_format($detail->gia) }}đ × {{ $detail->so_luong }}</div>

                                {{-- Nút đánh giá — chỉ hiện khi đơn hoàn thành và còn sản phẩm --}}
                                @if($order->trang_thai === 'completed' && $detail->product)
                                    @php
                                        $reviewed = \App\Models\Review::where('user_id', auth()->id())
                                            ->where('product_id', $detail->product->id)
                                            ->first();
                                    @endphp
                                    @if($reviewed)
                                        <span class="mt-2 d-inline-flex align-items-center gap-1" style="font-size:12px; color:#27AE60; font-weight:600;">
                                            <i class="fas fa-check-circle"></i> Đã đánh giá
                                            @if($reviewed->trang_thai === 'pending')
                                                <span style="color:#F5A623;">(Đang chờ duyệt)</span>
                                            @endif
                                        </span>
                                    @else
                                        <a href="{{ route('products.show', $detail->product->id) }}#review-pane"
                                           class="btn mt-2"
                                           style="background:var(--primary); color:#fff; border:none; padding:6px 16px; font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:1px; border-radius:0; text-decoration:none; display:inline-block;">
                                            <i class="fas fa-star me-1"></i>Đánh giá
                                        </a>
                                    @endif
                                @endif
                            </div>
                            
                            <div class="fw-bold" style="font-size: 16px;">
                                {{ number_format($detail->thanh_tien) }}đ
                            </div>
                        </div>
                    @endforeach
                    
                    <div class="mt-4 pt-3 border-top">
                        <div class="summary-row">
                            <span>Tạm tính:</span>
                            <span class="fw-bold" style="color: var(--text-main);">{{ number_format($order->tong_tien) }}đ</span>
                        </div>
                        <div class="summary-row">
                            <span>Vận chuyển:</span>
                            <span class="fw-bold" style="color: #27AE60;">Miễn phí</span>
                        </div>
                        <div class="summary-total">
                            <span>Tổng tiền:</span>
                            <span>{{ number_format($order->tong_tien) }}đ</span>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            
            <!-- Delivery Details -->
            <div class="order-section">
                <div class="section-header">
                    <i class="fas fa-map-marker-alt"></i> THÔNG TIN GIAO HÀNG
                </div>
                <div class="section-body">
                    <div class="info-label">Người nhận</div>
                    <div class="info-value">{{ $order->ten_nguoi_nhan }}</div>
                    
                    <div class="info-label">Số điện thoại</div>
                    <div class="info-value">{{ $order->sdt_nguoi_nhan }}</div>
                    
                    <div class="info-label">Địa chỉ</div>
                    <div class="info-value">{{ $order->dia_chi_giao_hang }}</div>
                    
                    @if($order->ghi_chu)
                        <div class="info-label">Ghi chú</div>
                        <div class="info-value">{{ $order->ghi_chu }}</div>
                    @endif
                </div>
            </div>

            <!-- Payment Details -->
            <div class="order-section">
                <div class="section-header">
                    <i class="fas fa-credit-card"></i> THÔNG TIN THANH TOÁN
                </div>
                <div class="section-body">
                    <div class="info-label">Phương thức</div>
                    <div class="info-value">
                        @if($order->phuong_thuc_thanh_toan === 'vietqr')
                            Chuyển khoản VietQR
                        @else
                            Chuyển khoản ngân hàng
                        @endif
                    </div>
                    
                    <div class="info-label">Trạng thái thanh toán</div>
                    <div>
                        @if($order->trang_thai_thanh_toan === 'paid')
                            <span class="badge-paid">ĐÃ THANH TOÁN</span>
                        @else
                            <span class="badge-unpaid">CHƯA THANH TOÁN</span>
                        @endif
                    </div>

                    @if($order->phuong_thuc_thanh_toan === 'vietqr' && $order->trang_thai_thanh_toan === 'unpaid')
                        <div class="mt-4 pt-4 border-top text-center">
                            <div class="info-label mb-3 text-center">Quét mã QR để thanh toán</div>
                            <img src="{{ $order->vietqr_url }}" alt="VietQR Code" class="img-fluid border p-2 bg-white mb-2" style="max-width: 250px; border-radius: 8px;">
                            <p class="text-muted small mt-2">Sử dụng App ngân hàng của bạn để quét mã này.<br>Nội dung chuyển khoản: <strong>{{ $order->ma_don_hang }}</strong></p>
                        </div>
                    @endif
                </div>
            </div>
            
        </div>
    </div>
</div>
@endsection
