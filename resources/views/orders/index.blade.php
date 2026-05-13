@extends('layouts.app')

@section('title', 'Đơn hàng của tôi - AVA')

@push('styles')
<style>
    /* =========================================
       ORDERS PAGE - AVA STYLE
       ========================================= */
    .page-title {
        font-weight: 700;
        font-size: 32px;
        color: var(--text-main);
        text-align: center;
        margin: 40px 0;
        text-transform: uppercase;
    }

    .filter-tabs {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        margin-bottom: 30px;
        border-bottom: 2px solid #EEEEEE;
        padding-bottom: 10px;
    }
    
    .filter-tab {
        color: var(--text-light);
        padding: 10px 20px;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 14px;
        transition: all 0.3s;
        border-bottom: 2px solid transparent;
        margin-bottom: -12px;
    }
    
    .filter-tab:hover {
        color: var(--text-main);
    }
    
    .filter-tab.active {
        color: var(--text-main);
        border-bottom: 2px solid var(--text-main);
    }

    .order-card {
        border: 1px solid #EEEEEE;
        margin-bottom: 30px;
        background: #fff;
    }

    .order-header {
        background: #F6F6F6;
        padding: 15px 20px;
        border-bottom: 1px solid #EEEEEE;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
    }
    
    .order-id {
        font-weight: 700;
        font-size: 16px;
    }
    
    .order-date {
        color: var(--text-light);
        font-size: 14px;
    }

    .order-body {
        padding: 20px;
    }
    
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
    }
    
    .item-qty {
        color: var(--text-light);
        font-size: 14px;
    }

    .order-footer {
        padding: 15px 20px;
        border-top: 1px solid #EEEEEE;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
    }
    
    .order-total-label {
        color: var(--text-light);
        font-size: 14px;
    }
    
    .order-total-val {
        font-weight: 700;
        font-size: 18px;
        color: var(--text-main);
    }

    /* Badges */
    .status-badge {
        padding: 5px 15px;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        border: 1px solid transparent;
    }
    
    .status-pending { background: transparent; color: #F5A623; border-color: #F5A623; }
    .status-confirmed { background: transparent; color: #4A90E2; border-color: #4A90E2; }
    .status-shipping { background: transparent; color: #9B51E0; border-color: #9B51E0; }
    .status-completed { background: transparent; color: #27AE60; border-color: #27AE60; }
    .status-cancelled { background: transparent; color: #EB5757; border-color: #EB5757; }
    
    .empty-state {
        padding: 80px 20px;
        text-align: center;
        border: 1px solid #EEEEEE;
    }
</style>
@endpush

@section('content')
<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title mb-0" style="margin:0;">Đơn hàng của tôi</h1>
        <a href="{{ route('profile.index') }}" class="btn btn-outline-dark" style="border-radius: 0;">VỀ TRANG CÁ NHÂN</a>
    </div>

    <!-- Tabs Lọc -->
    <div class="filter-tabs">
        @php
            $statuses = [
                ''          => 'Tất cả đơn',
                'pending'   => 'Chờ xử lý',
                'confirmed' => 'Đã xác nhận',
                'shipping'  => 'Đang giao',
                'completed' => 'Đã hoàn thành',
                'cancelled' => 'Đã hủy',
            ];
            $currentStatus = request('status', '');
        @endphp
        @foreach($statuses as $val => $label)
            <a href="{{ route('orders.index', ['status' => $val]) }}" 
               class="filter-tab {{ $currentStatus === $val ? 'active' : '' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    <!-- Danh sách đơn hàng -->
    <div class="order-list">
        @forelse($orders as $order)
            <div class="order-card">
                <div class="order-header">
                    <div>
                        <span class="order-id">Đơn hàng #{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</span>
                        <span class="order-date ms-3">{{ $order->created_at->format('d/m/Y, H:i') }}</span>
                    </div>
                    <div>
                        @php
                            $statusClass = 'pending';
                            $statusText = 'Chờ xử lý';
                            switch($order->trang_thai) {
                                case 'confirmed': $statusClass = 'confirmed'; $statusText = 'Đã xác nhận'; break;
                                case 'shipping':  $statusClass = 'shipping'; $statusText = 'Đang giao'; break;
                                case 'completed': $statusClass = 'completed'; $statusText = 'Đã hoàn thành'; break;
                                case 'cancelled': $statusClass = 'cancelled'; $statusText = 'Đã hủy'; break;
                            }
                        @endphp
                        <span class="status-badge status-{{ $statusClass }}">{{ $statusText }}</span>
                    </div>
                </div>

                <div class="order-body">
                    @foreach($order->orderItems->take(2) as $detail)
                        <div class="order-item">
                            @if($detail->product && $detail->product->anh)
                                <img src="{{ $detail->product->image_path }}" class="item-img" alt="Product">
                            @else
                                <div class="item-img d-flex align-items-center justify-content-center border">
                                    <i class="fas fa-image text-muted"></i>
                                </div>
                            @endif
                            <div style="flex:1;">
                                <div class="item-title">{{ $detail->product ? $detail->product->ten_sp : 'Sản phẩm đã xóa' }}</div>
                                <div class="item-qty">Số lượng: {{ $detail->so_luong }}</div>
                            </div>
                            <div class="fw-bold">{{ number_format($detail->gia) }}đ</div>
                        </div>
                    @endforeach
                    
                    @if($order->orderItems->count() > 2)
                        <div class="text-center mt-3">
                            <span class="text-muted" style="font-size: 13px;">Và {{ $order->orderItems->count() - 2 }} sản phẩm khác...</span>
                        </div>
                    @endif
                </div>

                <div class="order-footer">
                    <div>
                        <span class="order-total-label">Tổng tiền:</span>
                        <span class="order-total-val ms-2">{{ number_format($order->tong_tien) }}đ</span>
                    </div>
                    <a href="{{ route('orders.show', $order) }}" class="btn btn-ava-dark" style="padding: 10px 20px;">XEM CHI TIẾT</a>
                </div>
            </div>
        @empty
            <div class="empty-state">
                <i class="fas fa-box-open fa-4x text-muted mb-4"></i>
                <h3 style="font-weight: 700;">Không tìm thấy đơn hàng</h3>
                <p class="text-muted mb-4">Bạn chưa có đơn hàng nào với trạng thái này.</p>
                <a href="{{ route('products.index') }}" class="btn btn-ava-dark px-4 py-2">BẮT ĐẦU MUA SẮM</a>
            </div>
        @endforelse
    </div>

    @if($orders->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $orders->appends(request()->query())->links('pagination::bootstrap-5') }}
        </div>
    @endif

</div>
@endsection
