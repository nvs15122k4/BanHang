@extends('layouts.app')

@section('title', 'Thông báo của tôi - Sàn Tím Vi En')

@push('styles')
<style>
    .page-title {
        font-weight: 700;
        font-size: 28px;
        color: var(--text-main);
        text-transform: uppercase;
        margin-bottom: 0;
    }

    .notif-list-item {
        display: flex;
        align-items: flex-start;
        gap: 16px;
        padding: 20px 24px;
        border-bottom: 1px solid #EEEEEE;
        background: #fff;
        transition: background 0.2s;
        text-decoration: none;
        color: inherit;
    }

    .notif-list-item:hover {
        background: #FAFAFA;
        color: inherit;
    }

    .notif-list-item.unread {
        background: #FFF9F0;
        border-left: 3px solid var(--primary);
    }

    .notif-list-item.unread:hover {
        background: #FFF3E0;
    }

    .notif-icon-wrap {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        background: var(--primary-light);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .notif-icon-wrap i {
        color: var(--primary);
        font-size: 18px;
    }

    .notif-title {
        font-weight: 700;
        font-size: 14px;
        color: var(--text-main);
        margin-bottom: 4px;
    }

    .notif-message {
        font-size: 13px;
        color: #555;
        line-height: 1.5;
        margin-bottom: 6px;
    }

    .notif-time {
        font-size: 11px;
        color: #AAA;
    }

    .unread-dot {
        width: 8px;
        height: 8px;
        background: var(--primary);
        border-radius: 50%;
        flex-shrink: 0;
        margin-top: 6px;
    }

    .empty-state {
        padding: 80px 20px;
        text-align: center;
    }
</style>
@endpush

@section('content')
<div class="container py-4" style="max-width: 760px;">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title">Thông báo</h1>
        <a href="{{ route('orders.index') }}" class="btn btn-outline-dark" style="border-radius:0; font-size:13px; font-weight:700; text-transform:uppercase; letter-spacing:1px;">
            <i class="fas fa-box me-2"></i>Đơn hàng của tôi
        </a>
    </div>

    <div style="border: 1px solid #EEEEEE;">
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
                <div style="flex:1; min-width:0;">
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
                <i class="far fa-bell fa-4x mb-3 d-block" style="color:#DDD;"></i>
                <h4 style="font-weight:700; color:var(--text-main);">Chưa có thông báo nào</h4>
                <p class="text-muted">Khi đơn hàng của bạn hoàn thành, thông báo sẽ xuất hiện ở đây.</p>
                <a href="{{ route('products.index') }}" class="btn btn-dark mt-2" style="border-radius:0; font-weight:700; text-transform:uppercase; letter-spacing:1px;">
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
