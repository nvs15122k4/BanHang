@extends('layouts.app')
@section('title', 'Danh sách yêu thích - Sàn Tím')

@push('styles')
    @vite(['public/css/views/product_index.css'])
@endpush

@section('content')
<div class="container py-2 pb-5">
    <div class="breadcrumb-st">
        <a href="{{ route('home') }}">Trang chủ</a> &nbsp;/&nbsp; <a class="uix-1e32cae737" href="{{ route('profile.index') }}">Tài khoản</a> &nbsp;/&nbsp; <span class="uix-5a8b9549de">Yêu thích</span>
    </div>

    <div class="d-flex align-items-center mb-4 mt-3">
        <h4 class="fw-bold mb-0"><i class="fas fa-heart text-danger me-2"></i>Danh sách yêu thích</h4>
    </div>

    @if($wishlists->isEmpty())
        <div class="text-center py-5 uix-e84bfd5299">
            <i class="far fa-heart fa-3x text-muted mb-3 d-block"></i>
            <h5 class="text-muted">Bạn chưa yêu thích sản phẩm nào.</h5>
            <a href="{{ route('products.index') }}" class="btn-st-dark mt-3 d-inline-block px-4 py-2 uix-66bcd2106f">Khám phá sản phẩm</a>
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
                                    <div class="product-badge uix-6263644e65">
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
                                    <form action="{{ route('cart.add') }}" method="POST" class="w-full"
                                          data-requires-size="{{ count($product->sizes ?? []) > 0 ? '1' : '0' }}"
                                          data-product-name="{{ $product->ten_sp }}"
                                          data-size-options='@json($product->sizes ?? [])'>
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
