@extends('layouts.app')

@php
    $category = $category ?? null;
    $productFilterKeys = ['search', 'loai_filter', 'min_price', 'max_price', 'sort', 'trang_thai_filter', 'per_page'];
    $hasProductFilters = collect($productFilterKeys)->contains(fn ($key) => request()->filled($key));
    $selectedCategory = $category?->slug ?? request('loai_filter', '');
    $productsCanonical = $category
        ? 'https://santimvien.vn/danh-muc/' . $category->slug
        : 'https://santimvien.vn/products';

    if (! $hasProductFilters && request()->integer('page') > 1) {
        $productsCanonical .= '?page=' . request()->integer('page');
    }

    $pageTitle = $category
        ? $category->name . ' - Sàn Tím Vi En'
        : 'Sản phẩm thời trang nam nữ - Sàn Tím Vi En';
    $pageDescription = $category && filled($category->description)
        ? \Illuminate\Support\Str::limit(strip_tags($category->description), 155)
        : ($category
            ? 'Khám phá sản phẩm ' . $category->name . ' tại Sàn Tím Vi En với lựa chọn thời trang chất lượng.'
            : 'Khám phá sản phẩm thời trang nam nữ hiện đại, chất lượng từ Sàn Tím Vi En với nhiều lựa chọn phù hợp phong cách Việt.');
    $categoryBreadcrumbSchema = $category ? [
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => [
            [
                '@type' => 'ListItem',
                'position' => 1,
                'name' => 'Trang chủ',
                'item' => 'https://santimvien.vn/',
            ],
            [
                '@type' => 'ListItem',
                'position' => 2,
                'name' => $category->name,
                'item' => 'https://santimvien.vn/danh-muc/' . $category->slug,
            ],
        ],
    ] : null;
@endphp

@section('title', $pageTitle)
@section('meta_description', $pageDescription)
@section('canonical', $productsCanonical)
@section('robots', $hasProductFilters ? 'noindex, follow' : 'index, follow')
@section('og_title', $pageTitle)
@section('og_description', $pageDescription)

@push('styles')
    @vite(['public/css/views/product_index.css'])
@endpush

@section('content')
@if($categoryBreadcrumbSchema)
<script type="application/ld+json">{!! json_encode($categoryBreadcrumbSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}</script>
@endif
<div class="container py-4">
    <div class="breadcrumb-st">
        <a href="{{ route('home') }}">Trang chủ</a> &nbsp;/&nbsp;
        @if($category)
            Danh mục &nbsp;/&nbsp; {{ $category->name }}
        @else
            Tất cả sản phẩm
        @endif
    </div>

    <div class="row">
        <x-catalog-filter
            :action="route('products.index')"
            :categories="$loaiList"
            :title="$category ? $category->name : 'Cửa hàng'"
            :selected-category="$selectedCategory"
            :has-filters="$hasProductFilters"
            :clear-url="route('products.index')"
        />

        <!-- MAIN CONTENT -->
        <main class="col-lg-9">
            @if($category && filled($category->description))
                <p class="mb-4 text-muted">{{ $category->description }}</p>
            @endif
            <div class="result-bar">
                <div class="result-count">
                    Hiển thị {{ $products->count() }} / {{ $products->total() }} sản phẩm
                </div>
                <div class="d-flex align-items-center gap-3">
                    <span class="text-muted d-none d-md-block uix-3aa7552479">SẮP XẾP:</span>
                    <select class="sort-select" onchange="window.location.href=this.value">
                        <option value="{{ request()->fullUrlWithQuery(['sort' => 'newest']) }}" {{ request('sort') == 'newest' ? 'selected' : '' }}>Mới nhất</option>
                        <option value="{{ request()->fullUrlWithQuery(['sort' => 'price_asc']) }}" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Giá tăng dần</option>
                        <option value="{{ request()->fullUrlWithQuery(['sort' => 'price_desc']) }}" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Giá giảm dần</option>
                    </select>
                </div>
            </div>

            <div class="row">
                @forelse($products as $product)
                    <div class="col-md-4 col-6">
                        <div class="product-card">
                            <div class="product-img-wrapper">
                                <a href="{{ route('products.show', ['product' => $product->slug]) }}">
                                    @if($product->so_luong <= 0)
                                        <div class="product-badge badge-out">Hết hàng</div>
                                    @elseif($product->is_new)
                                        <div class="product-badge">Mới</div>
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
                                        <button type="submit" class="wishlist-btn-float {{ auth()->user()->hasInWishlist($product->id) ? 'active' : '' }}" title="{{ auth()->user()->hasInWishlist($product->id) ? 'Bỏ yêu thích' : 'Yêu thích' }}">
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
                                        <form action="{{ route('cart.add') }}" method="POST" class="add-to-cart-form uix-cad980f4b7"
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
                                {{ number_format($product->gia) }}đ
                                @if($product->gia_goc > $product->gia)
                                    <span class="price-old">{{ number_format($product->gia_goc) }}đ</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <i class="fas fa-search fa-3x text-muted mb-3 d-block"></i>
                        <h4 class="text-muted">Không tìm thấy sản phẩm nào phù hợp.</h4>
                        <a href="{{ route('products.index') }}" class="btn-st mt-3 d-inline-block-custom">Xóa bộ lọc</a>
                    </div>
                @endforelse
            </div>

            @if($products->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $products->appends(request()->query())->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </main>
    </div>
</div>

<!-- Admin: Thêm sản phẩm (Modal) -->
@if(auth()->check() && auth()->user()->role === 'admin')
<div class="modal fade" id="createProductModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content uix-7f4ecc53cd">
            <div class="modal-header uix-9d08eeeff3">
                <h5 class="modal-title uix-8d366359a1">Thêm sản phẩm mới</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label">Tên sản phẩm</label>
                            <input type="text" name="ten_sp" class="form-control uix-4a2477fd2a" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Giá (vnđ)</label>
                            <input type="number" name="gia" class="form-control uix-4a2477fd2a" min="0" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Số lượng kho</label>
                            <input type="number" name="so_luong" class="form-control uix-4a2477fd2a" min="0" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Danh mục</label>
                            <select name="loai" class="form-select uix-4a2477fd2a">
                                @foreach($loaiList as $loai)
                                    <option value="{{ $loai }}">{{ ucfirst($loai) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Mô tả</label>
                            <textarea name="mo_ta" class="form-control uix-4a2477fd2a" rows="4"></textarea>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Hình ảnh</label>
                            <input type="file" name="anh_file" class="form-control uix-4a2477fd2a" accept="image/*">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-st" data-bs-dismiss="modal">HỦY BỎ</button>
                    <button type="submit" class="btn-st-dark">LƯU SẢN PHẨM</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection
