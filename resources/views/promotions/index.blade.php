@extends('layouts.app')
@php
    $promotionFilterKeys = ['search', 'loai_filter', 'loai', 'min_price', 'max_price', 'min_discount', 'sort'];
    $hasPromotionFilters = collect($promotionFilterKeys)->contains(fn ($key) => request()->filled($key));
@endphp
@section('title', 'Khuyến mãi thời trang mới nhất - Sàn Tím Vi En')
@section('meta_description', 'Cập nhật các chương trình khuyến mãi thời trang mới nhất từ Sàn Tím Vi En và lựa chọn sản phẩm phong cách với mức giá hấp dẫn.')
@section('canonical', 'https://santimvien.vn/khuyen-mai')
@section('robots', $hasPromotionFilters ? 'noindex, follow' : 'index, follow')
@section('og_title', 'Khuyến mãi thời trang mới nhất - Sàn Tím Vi En')
@section('og_description', 'Cập nhật các chương trình khuyến mãi thời trang mới nhất từ Sàn Tím Vi En và lựa chọn sản phẩm phong cách với mức giá hấp dẫn.')

@push('styles')
    @vite(['public/css/views/product_index.css'])
@endpush

@section('content')



<div class="container py-2 pb-5">
    <div class="breadcrumb-st">
        <a href="{{ route('home') }}">Trang chủ</a> &nbsp;/&nbsp; Khuyến mãi
    </div>

    <div class="row mt-3">
        <x-catalog-filter
            :action="route('promotions.index')"
            :categories="$categories"
            title="Khuyến mãi"
            :selected-category="request('loai_filter', request('loai', ''))"
            :show-discount="true"
            :has-filters="$hasPromotionFilters"
            :clear-url="route('promotions.index')"
        />

        <!-- MAIN CONTENT -->
        <main class="col-lg-9">
            <div class="result-bar">
                <div class="result-count">
                    Hiển thị {{ $paginated->count() }} / {{ $paginated->total() }} sản phẩm
                </div>
                <div class="d-flex align-items-center gap-3">
                    <span class="text-muted d-none d-md-block uix-3aa7552479">SẮP XẾP:</span>
                    <select class="sort-select" onchange="window.location.href=this.value">
                        <option value="{{ request()->fullUrlWithQuery(['sort' => 'discount_desc']) }}" {{ request('sort', 'discount_desc') == 'discount_desc' ? 'selected' : '' }}>Giảm nhiều nhất</option>
                        <option value="{{ request()->fullUrlWithQuery(['sort' => 'price_asc']) }}" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Giá thấp → cao</option>
                        <option value="{{ request()->fullUrlWithQuery(['sort' => 'price_desc']) }}" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Giá cao → thấp</option>
                        <option value="{{ request()->fullUrlWithQuery(['sort' => 'newest']) }}" {{ request('sort') == 'newest' ? 'selected' : '' }}>Mới nhất</option>
                    </select>
                </div>
            </div>

            @if($paginated->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-tag fa-3x text-muted mb-3 d-block"></i>
                    <h4 class="text-muted">Không tìm thấy sản phẩm khuyến mãi phù hợp.</h4>
                    <a href="{{ route('promotions.index') }}" class="btn-st mt-3 d-inline-block-custom">Xem tất cả khuyến mãi</a>
                </div>
            @else
                <div class="row">
                    @foreach($paginated as $product)
                        @php
                            $promo      = $product->promo;
                            $promoPrice = $product->promo_price;
                            $pct        = $product->gia > 0 ? round(($product->gia - $promoPrice) / $product->gia * 100) : 0;
                            $isWished   = auth()->check() ? auth()->user()->hasInWishlist($product->id) : false;
                        @endphp
                        <div class="col-md-4 col-6">
                            <div class="product-card">
                                <div class="product-img-wrapper position-relative">
                                    <a href="{{ route('products.show', ['product' => $product->slug]) }}">
                                        @if($product->so_luong <= 0)
                                            <div class="product-badge badge-out">Hết hàng</div>
                                        @else
                                            <div class="product-badge uix-6263644e65">
                                                @if($promo && $promo->tag)
                                                    {{ $promo->tag }}
                                                @else
                                                    -{{ $pct }}%
                                                @endif
                                            </div>
                                        @endif

                                        @if($product->anh || $product->productImages->isNotEmpty())
                                            <img src="{{ $product->image_path }}" alt="{{ $product->ten_sp }}" class="product-img">
                                        @else
                                            <div class="product-img d-flex align-items-center justify-content-center bg-light">
                                                <i class="fas fa-image fa-3x text-muted"></i>
                                            </div>
                                        @endif
                                    </a>
                                    
                                    {{-- Wishlist Toggle --}}
                                    @auth
                                        <form action="{{ route('wishlist.toggle', $product->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="wishlist-btn-float {{ $isWished ? 'active' : '' }}" title="{{ $isWished ? 'Bỏ yêu thích' : 'Yêu thích' }}">
                                                <i class="fas fa-heart"></i>
                                            </button>
                                        </form>
                                    @else
                                        <a href="{{ route('login') }}" class="wishlist-btn-float" title="Đăng nhập để yêu thích">
                                            <i class="fas fa-heart"></i>
                                        </a>
                                    @endauth

                                    <div class="product-actions">
                                        @if($product->so_luong > 0)
                                            <form action="{{ route('cart.add') }}" method="POST" class="w-full"
                                                  data-requires-size="{{ count($product->variant_options) > 0 ? '1' : '0' }}"
                                                  data-product-name="{{ $product->ten_sp }}"
                                                  data-size-options="{{ json_encode($product->variant_options, JSON_UNESCAPED_UNICODE) }}">
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
                                <a href="{{ route('products.show', ['product' => $product->slug]) }}" class="product-title">{{ $product->ten_sp }}</a>
                                <div class="product-price">
                                    {{ number_format($promoPrice, 0, ',', '.') }}đ
                                    <span class="price-old">{{ number_format($product->gia, 0, ',', '.') }}đ</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($paginated->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $paginated->withQueryString()->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            @endif
        </main>
    </div>
</div>
@endsection
