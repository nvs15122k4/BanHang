@extends('layouts.app')
@section('title', 'Danh sách yêu thích - Sàn Tím')

@push('styles')
    @vite(['resources/css/views/product_index.css'])
    <style>
    .wishlist-btn-float {
        position: absolute;
        top: 10px;
        right: 10px;
        background: rgba(255,255,255,0.9);
        border: none;
        border-radius: 50%;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #9ca3af;
        transition: all 0.2s;
        z-index: 10;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    .wishlist-btn-float:hover, .wishlist-btn-float.active {
        color: #e11d48;
        background: #fff;
    }
    </style>
@endpush

@section('content')
<div class="container py-2 pb-5">
    <div class="breadcrumb-st">
        <a href="{{ route('home') }}">Trang chủ</a> &nbsp;/&nbsp; <a href="{{ route('profile.index') }}" style="color:#666; text-decoration:none;">Tài khoản</a> &nbsp;/&nbsp; <span style="font-weight:600; color:#111;">Yêu thích</span>
    </div>

    <div class="d-flex align-items-center mb-4 mt-3">
        <h4 class="fw-bold mb-0"><i class="fas fa-heart text-danger me-2"></i>Danh sách yêu thích</h4>
    </div>

    @if($wishlists->isEmpty())
        <div class="text-center py-5" style="background:#f9fafb; border-radius:16px;">
            <i class="far fa-heart fa-3x text-muted mb-3 d-block"></i>
            <h5 class="text-muted">Bạn chưa yêu thích sản phẩm nào.</h5>
            <a href="{{ route('products.index') }}" class="btn-st-dark mt-3 d-inline-block px-4 py-2" style="border-radius:24px; text-decoration:none;">Khám phá sản phẩm</a>
        </div>
    @else
        <div class="row">
            @foreach($wishlists as $wishlist)
                @php
                    $product    = $wishlist->product;
                    if (!$product) continue;
                    $promo      = $product->getActivePromotion();
                    $promoPrice = $promo ? ($promo->loai_km === 'percent' ? $product->gia * (1 - $promo->gia_tri/100) : max(0, $product->gia - $promo->gia_tri)) : $product->gia;
                    $pct        = $product->gia > 0 ? round(($product->gia - $promoPrice) / $product->gia * 100) : 0;
                @endphp
                <div class="col-md-3 col-6 mb-4">
                    <div class="product-card">
                        <div class="product-img-wrapper position-relative">
                            <a href="{{ route('products.show', $product->id) }}">
                                @if($product->so_luong <= 0)
                                    <div class="product-badge badge-out">Hết hàng</div>
                                @elseif($promo)
                                    <div class="product-badge" style="background:#e11d48; color:#fff; border:none;">
                                        @if($promo->tag)
                                            {{ $promo->tag }}
                                        @else
                                            -{{ $pct }}%
                                        @endif
                                    </div>
                                @elseif($product->is_new)
                                    <div class="product-badge">Mới</div>
                                @endif

                                @if($product->anh)
                                    <img src="{{ $product->image_path }}" alt="{{ $product->ten_sp }}" class="product-img">
                                @else
                                    <div class="product-img d-flex align-items-center justify-content-center bg-light">
                                        <i class="fas fa-image fa-3x text-muted"></i>
                                    </div>
                                @endif
                            </a>
                            
                            {{-- Wishlist Toggle --}}
                            <form action="{{ route('wishlist.toggle', $product->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="wishlist-btn-float active" title="Bỏ yêu thích">
                                    <i class="fas fa-heart"></i>
                                </button>
                            </form>

                            <div class="product-actions">
                                @if($product->so_luong > 0)
                                    <form action="{{ route('cart.add') }}" method="POST" class="w-full">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                                        <input type="hidden" name="so_luong" value="1">
                                        <button type="submit" class="btn-quick-add">Thêm vào giỏ</button>
                                    </form>
                                @else
                                    <button class="btn-quick-add" disabled>Tạm hết hàng</button>
                                @endif
                            </div>
                        </div>
                        <span class="product-category">{{ $product->loai }}</span>
                        <a href="{{ route('products.show', $product->id) }}" class="product-title">{{ $product->ten_sp }}</a>
                        <div class="product-price">
                            {{ number_format($promoPrice, 0, ',', '.') }}đ
                            @if($promo)
                                <span class="price-old">{{ number_format($product->gia, 0, ',', '.') }}đ</span>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if($wishlists->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $wishlists->links('pagination::bootstrap-5') }}
            </div>
        @endif
    @endif
</div>
@endsection
