@extends('layouts.app')
@section('title', 'Khuyến Mãi Hot — Sàn Tím')

@push('styles')
    @vite(['resources/css/views/product_index.css'])
@endpush

@section('content')



<div class="container py-2 pb-5">
    <div class="breadcrumb-st">
        <a href="{{ route('home') }}">Trang chủ</a> &nbsp;/&nbsp; Khuyến mãi
    </div>

    <div class="row mt-3">
        <!-- SIDEBAR FILTERS -->
        <aside class="col-lg-3 d-none d-lg-block">
            <div class="sidebar-filter">
                <h1 class="uix-b69612cfda">Lọc Khuyến Mãi</h1>

                <form method="GET" action="{{ route('promotions.index') }}" id="filterForm">
                    <!-- Danh mục -->
                    <div class="filter-section">
                        <div class="filter-title">Danh mục <i class="fas fa-chevron-down uix-00313dcbd7"></i></div>
                        <ul class="filter-list">
                            <li class="filter-item">
                                <a href="{{ route('promotions.index', request()->except('loai')) }}"
                                   class="filter-link {{ !request('loai') ? 'active' : '' }}">Tất cả</a>
                            </li>
                            @foreach($categories as $cat)
                                <li class="filter-item">
                                    <a href="{{ route('promotions.index', array_merge(request()->all(), ['loai' => $cat])) }}"
                                       class="filter-link {{ request('loai') == $cat ? 'active' : '' }}">
                                        {{ ucfirst($cat) }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <!-- Mức giảm -->
                    <div class="filter-section">
                        <div class="filter-title">Mức giảm tối thiểu <i class="fas fa-chevron-down uix-00313dcbd7"></i></div>
                        <ul class="filter-list">
                            <li class="filter-item">
                                <a href="{{ route('promotions.index', request()->except('min_discount')) }}"
                                   class="filter-link {{ !request('min_discount') ? 'active' : '' }}">Bất kỳ</a>
                            </li>
                            @foreach([10, 20, 30, 50] as $discount)
                                <li class="filter-item">
                                    <a href="{{ route('promotions.index', array_merge(request()->all(), ['min_discount' => $discount])) }}"
                                       class="filter-link {{ request('min_discount') == $discount ? 'active' : '' }}">
                                        Từ {{ $discount }}% trở lên
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    
                    @if(request()->hasAny(['loai','min_discount','sort']))
                        <a href="{{ route('promotions.index') }}" class="btn-st-dark w-100 py-2-custom text-xs-custom text-center mt-3 d-block">XÓA BỘ LỌC</a>
                    @endif
                </form>
            </div>
        </aside>

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
                                    <a href="{{ route('products.show', $product->id) }}">
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

                                        @if($product->anh)
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
