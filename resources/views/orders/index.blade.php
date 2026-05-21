@extends('layouts.app')

@section('title', 'Đơn hàng của tôi - AVA')

@push('styles')
    @vite(['public/css/views/order_index.css'])
@endpush

@section('content')
<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title m-0">Đơn hàng của tôi</h1>
        <a href="{{ route('profile.index') }}" class="btn btn-outline-dark rounded-0">VỀ TRANG CÁ NHÂN</a>
    </div>

    <!-- Tabs Lọc -->
    <div class="filter-tabs">
        @php
            $statuses = [
                ''          => 'Tất cả đơn',
                'pending'   => 'Chờ duyệt đơn',
                'confirmed' => 'Đã duyệt đơn',
                'shipping'  => 'Đang giao hàng',
                'disputing' => 'Đang xử lý KN',
                'completed' => 'Hoàn thành',
                'cancelled' => 'Đã hủy',
                'reviewed'  => 'Đánh giá sản phẩm',
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

    <!-- Danh sách đơn hàng / Đánh giá -->
    <div class="order-list">
        @if($currentStatus === 'reviewed')
            {{-- VIEW REVIEWS LIST --}}
            @forelse($reviews as $review)
                <div class="order-card p-3">
                    <div class="d-flex align-items-center gap-3">
                        @if($review->product)
                            <img src="{{ $review->product->image_path }}" class="item-img w-60-px-custom h-60-px-custom" alt="Product">
                            <div class="uix-7623f05545">
                                <div class="item-title mb-1">{{ $review->product->ten_sp }}</div>
                                <div class="star-rating text-yellow-custom text-xs-custom">
                                    @for($i=1; $i<=5; $i++)
                                        <i class="{{ $i <= $review->rating ? 'fas' : 'far' }} fa-star"></i>
                                    @endfor
                                    <span class="ms-2 text-muted small">{{ $review->created_at->format('d/m/Y') }}</span>
                                </div>
                                <div class="mt-2 text-muted small italic">"{{ $review->comment ?? 'Không có nhận xét' }}"</div>
                            </div>
                                <button type="button" 
                                class="btn btn-outline-dark btn-sm rounded-0 font-bold text-xs-custom-extra"
                                data-bs-toggle="modal"
                                data-bs-target="#globalReviewModal"
                                data-product-id="{{ $review->product->id }}"
                                data-product-name="{{ $review->product->ten_sp }}"
                                data-product-image="{{ $review->product->image_path }}"
                                data-product-price="{{ number_format($review->product->gia) }}đ"
                                data-review="{{ $review->toJson() }}">
                                XEM CHI TIẾT
                            </button>
                        @else
                            <div class="text-muted">Sản phẩm đã bị xóa</div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="empty-state">
                    <i class="fas fa-comment-slash fa-4x text-muted mb-4"></i>
                    <h3 class="uix-42acb60ee2">Chưa có đánh giá nào</h3>
                    <p class="text-muted mb-4">Bạn chưa đánh giá bất kỳ sản phẩm nào.</p>
                </div>
            @endforelse
        @else
            {{-- VIEW ORDERS LIST --}}
            @forelse($orders as $order)
                <div class="order-card">
                    <div class="order-header">
                        <div>
                            <span class="order-id">Đơn hàng #{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</span>
                            <span class="order-date ms-3">{{ $order->created_at->format('d/m/Y, H:i') }}</span>
                        </div>
                        <div>
                            @php
                                // User thấy nhãn theo userStatusLabels
                                $userLabels = \App\Models\Order::userStatusLabels();
                                $statusStyleMap = [
                                    'pending'   => 'pending',
                                    'confirmed' => 'confirmed',
                                    'shipping'  => 'shipping',
                                    'disputing' => 'cancelled',
                                    'completed' => 'completed',
                                    'cancelled' => 'cancelled',
                                ];
                                $styleClass = $statusStyleMap[$order->trang_thai] ?? 'pending';
                                $displayText = $userLabels[$order->trang_thai] ?? $order->trang_thai;
                            @endphp
                            <span class="status-badge status-{{ $styleClass }}">{{ $displayText }}</span>
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
                                <div class="uix-7623f05545">
                                    <div class="item-title">{{ $detail->product ? $detail->product->ten_sp : 'Sản phẩm đã xóa' }}</div>
                                    <div class="item-qty">Số lượng: {{ $detail->so_luong }}</div>

                                    {{-- Nút đánh giá nhanh --}}
                                    @if($order->trang_thai === 'completed' && $detail->product)
                                        @php
                                            $reviewed = \App\Models\Review::where('user_id', auth()->id())
                                                ->where('product_id', $detail->product->id)
                                                ->first();
                                        @endphp
                                        @if(!$reviewed)
                                            <button type="button" 
                                                class="btn mt-1 p-0 border-0 text-primary-custom text-xs-custom font-bold uppercase letter-spacing-05-custom bg-none-custom"
                                                data-bs-toggle="modal"
                                                data-bs-target="#globalReviewModal"
                                                data-product-id="{{ $detail->product->id }}"
                                                data-product-name="{{ $detail->product->ten_sp }}"
                                                data-product-image="{{ $detail->product->image_path }}"
                                                data-product-price="{{ number_format($detail->gia) }}đ">
                                                <i class="fas fa-star me-1"></i>Đánh giá ngay
                                            </button>
                                        @else
                                            <button type="button" 
                                                class="btn mt-1 p-0 border-0 text-success-custom text-xs-custom font-bold uppercase letter-spacing-05-custom bg-none-custom"
                                                data-bs-toggle="modal"
                                                data-bs-target="#globalReviewModal"
                                                data-product-id="{{ $detail->product->id }}"
                                                data-product-name="{{ $detail->product->ten_sp }}"
                                                data-product-image="{{ $detail->product->image_path }}"
                                                data-product-price="{{ number_format($detail->gia) }}đ"
                                                data-review="{{ $reviewed->toJson() }}">
                                                <i class="fas fa-check-circle"></i> Xem đánh giá
                                            </button>
                                        @endif
                                    @endif
                                </div>
                                <div class="fw-bold">{{ number_format($detail->gia) }}đ</div>
                            </div>
                        @endforeach
                        
                        @if($order->orderItems->count() > 2)
                            <div class="text-center mt-3">
                                <span class="text-muted text-xs-custom">Và {{ $order->orderItems->count() - 2 }} sản phẩm khác...</span>
                            </div>
                        @endif
                    </div>

                    <div class="order-footer">
                        <div>
                            <span class="order-total-label">Tổng tiền:</span>
                            <span class="order-total-val ms-2">{{ number_format($order->tong_tien) }}đ</span>
                        </div>
                        <a href="{{ route('orders.show', $order) }}" class="btn btn-ava-dark px-20-px-custom py-10-px-custom">XEM CHI TIẾT</a>
                    </div>
                </div>
            @empty
                <div class="empty-state">
                    <i class="fas fa-box-open fa-4x text-muted mb-4"></i>
                    <h3 class="uix-42acb60ee2">Không tìm thấy đơn hàng</h3>
                    <p class="text-muted mb-4">Bạn chưa có đơn hàng nào với trạng thái này.</p>
                    <a href="{{ route('products.index') }}" class="btn btn-ava-dark px-4 py-2">BẮT ĐẦU MUA SẮM</a>
                </div>
            @endforelse
        @endif
    </div>

    @if(($currentStatus === 'reviewed' ? $reviews : $orders)->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ ($currentStatus === 'reviewed' ? $reviews : $orders)->appends(request()->query())->links('pagination::bootstrap-5') }}
        </div>
    @endif

</div>
@endsection
