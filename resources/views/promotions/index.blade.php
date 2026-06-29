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

    <div class="row" id="products-ajax-container">
        <!-- CATEGORY FILTER GRID -->
        <style>
        .category-filter-wrapper {
            overflow-x: auto;
            padding-bottom: 15px;
            margin: 0 -10px;
            padding-left: 10px;
            padding-right: 10px;
            margin-bottom: 10px;
        }
        .category-filter-wrapper::-webkit-scrollbar {
            height: 4px;
        }
        .category-filter-wrapper::-webkit-scrollbar-thumb {
            background: #e0e0e0;
            border-radius: 4px;
        }
        .category-filter-wrapper::-webkit-scrollbar-track {
            background: transparent;
        }
        .category-filter-grid {
            display: grid;
            grid-template-rows: repeat(2, 1fr);
            grid-auto-flow: column;
            gap: 12px;
        }
        .category-filter-item {
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: flex-start;
            width: 170px;
            height: 52px;
            background: #ffffff;
            border-radius: 12px;
            text-decoration: none;
            color: #4a4a4a;
            transition: all 0.25s ease;
            border: 1px solid #eaeaea;
            box-shadow: 0 2px 6px rgba(0,0,0,0.02);
            padding: 0 15px;
            gap: 10px;
        }
        .category-filter-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.06);
            border-color: #dcdcdc;
            color: var(--primary-color, #920b29);
        }
        .category-filter-icon {
            font-size: 20px;
            color: var(--primary-color, #920b29);
            transition: transform 0.25s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 24px;
        }
        .category-filter-item:hover .category-filter-icon {
            transform: scale(1.1);
        }
        .category-filter-name {
            font-size: 13px;
            line-height: 1.2;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            font-weight: 500;
        }
        .category-filter-active {
            border: 1.5px solid var(--primary-color, #920b29);
            background: #fffafa; /* very light red/pink tint */
            color: var(--primary-color, #920b29);
            box-shadow: 0 4px 10px rgba(146, 11, 41, 0.08);
        }
        </style>
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="mb-0" style="font-size: 1.25rem;">Lọc theo danh mục</h4>
                @if($selectedCategoryModel)
                    <a href="{{ route('promotions.index') }}" class="ajax-category-link text-decoration-none text-muted" style="font-size: 14px; font-weight: 500;">
                        <i class="fas fa-arrow-left me-1"></i> Xem tất cả
                    </a>
                @endif
            </div>
            <div class="category-filter-wrapper">
                <div class="category-filter-grid">
                    @if($selectedCategoryModel)
                        @php
                            $contextCategory = $selectedCategoryModel->parent_id ? $selectedCategoryModel->parent : $selectedCategoryModel;
                        @endphp
                        
                        <a href="{{ route('promotions.index', ['loai_filter' => $contextCategory->slug]) }}" class="ajax-category-link category-filter-item {{ $selectedCategoryModel->id === $contextCategory->id ? 'category-filter-active' : '' }}">
                            <div class="category-filter-icon"><i class="{{ $contextCategory->icon ?? 'fas fa-tag' }}"></i></div>
                            <div class="category-filter-name">Tất cả {{ $contextCategory->name }}</div>
                        </a>
                        
                        @foreach($contextCategory->children as $child)
                            <a href="{{ route('promotions.index', ['loai_filter' => $child->slug]) }}" class="ajax-category-link category-filter-item {{ $selectedCategoryModel->id === $child->id ? 'category-filter-active' : '' }}">
                                <div class="category-filter-icon"><i class="{{ $child->icon ?? 'fas fa-tag' }}"></i></div>
                                <div class="category-filter-name">{{ $child->name }}</div>
                            </a>
                        @endforeach
                    @else
                        @foreach($categories as $cat)
                            <a href="{{ route('promotions.index', ['loai_filter' => $cat->slug]) }}" class="ajax-category-link category-filter-item">
                                <div class="category-filter-icon"><i class="{{ $cat->icon ?? 'fas fa-tag' }}"></i></div>
                                <div class="category-filter-name">{{ $cat->name }}</div>
                            </a>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>

        <!-- MAIN CONTENT -->
        <main class="col-lg-12">
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
                        <div class="col-lg-3 col-md-4 col-6">
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
