@extends('layouts.app')

@section('title', 'Thông báo của tôi - Sàn Tím Vi En')

@push('styles')
    @vite(['public/css/views/notifications.css'])
@endpush

@section('content')
<div class="container py-4 max-w-760-px-custom">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title">Thông báo</h1>
        <a href="{{ route('orders.index') }}" class="btn btn-outline-dark rounded-0 text-md-custom-extra font-bold uppercase letter-spacing-1-custom">
            <i class="fas fa-box me-2"></i>Đơn hàng của tôi
        </a>
    </div>

    <div class="border-e-custom">
        @forelse($notifications as $notif)
            @php
                $data    = $notif->data;
                $isRead  = !is_null($notif->read_at);
                $url     = $data['url'] ?? route('orders.index');
            @endphp
            <a href="{{ $url }}" class="notif-list-item {{ $isRead ? '' : 'unread' }}">
                <div class="notif-icon-wrap">
                    <i class="fas fa-box-open"></i>
                </div>
                <div class="flex-1 min-w-0-custom">
                    <div class="notif-title">{{ $data['title'] ?? 'Thông báo' }}</div>
                    <div class="notif-message">{{ $data['message'] ?? '' }}</div>
                    <div class="notif-time">
                        <i class="far fa-clock me-1"></i>{{ $notif->created_at->format('H:i, d/m/Y') }}
                        &nbsp;·&nbsp;
                        {{ $notif->created_at->diffForHumans() }}
                    </div>
                </div>
                @if(!$isRead)
                    <div class="unread-dot"></div>
                @endif
            </a>
        @empty
            <div class="empty-state">
                <i class="far fa-bell fa-4x mb-3 d-block text-gray-light-custom"></i>
                <h4 class="font-bold text-main-custom">Chưa có thông báo nào</h4>
                <p class="text-muted">Khi đơn hàng của bạn hoàn thành, thông báo sẽ xuất hiện ở đây.</p>
                <a href="{{ route('products.index') }}" class="btn btn-dark mt-2 rounded-0 font-bold uppercase letter-spacing-1-custom">
                    Mua sắm ngay
                </a>
            </div>
        @endforelse
    </div>

    @if($notifications->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $notifications->links('pagination::bootstrap-5') }}
        </div>
    @endif

</div>
@endsection
