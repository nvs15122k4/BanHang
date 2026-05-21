@extends('layouts.app')

@section('title', 'Sản phẩm - Sàn Tím Vi En')

@push('styles')
    @vite(['public/css/views/product_index.css'])
@endpush

@section('content')
<div class="container py-4">
    <div class="breadcrumb-st">
        <a href="{{ route('home') }}">Trang chủ</a> &nbsp;/&nbsp; Tất cả sản phẩm
    </div>

    <div class="row">
        <!-- SIDEBAR FILTERS -->
        <aside class="col-lg-3 d-none d-lg-block">
            <div class="sidebar-filter">
                <h1 class="uix-30b515ec19">Cửa hàng</h1>

                <form method="GET" action="{{ route('products.index') }}" id="filterForm">
                    <!-- Tìm kiếm -->
                    <div class="filter-section">
                        <div class="filter-title">Tìm kiếm</div>
                        <div class="position-relative">
                            <input type="text" name="search" class="form-control rounded-0" placeholder="Tên sản phẩm..."
                                   value="{{ request('search') }}">
                            <button class="uix-8493cc9cf4" type="submit">
                                <i class="fas fa-search text-muted uix-9e6595fb01"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Danh mục -->
                    <div class="filter-section">
                        <div class="filter-title">Danh mục <i class="fas fa-chevron-down uix-00313dcbd7"></i></div>
                        <ul class="filter-list">
                            <li class="filter-item">
                                <a href="{{ route('products.index', request()->except('loai_filter')) }}"
                                   class="filter-link {{ !request('loai_filter') ? 'active' : '' }}">Tất cả</a>
                            </li>
                            @foreach($loaiList as $loai)
                                <li class="filter-item">
                                    <a href="{{ route('products.index', array_merge(request()->all(), ['loai_filter' => $loai])) }}"
                                       class="filter-link {{ request('loai_filter') == $loai ? 'active' : '' }}">
                                        {{ ucfirst($loai) }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                        <input type="hidden" name="loai_filter" value="{{ request('loai_filter') }}">
                    </div>

                    <!-- Khoảng giá -->
                    <div class="filter-section">
                        <div class="filter-title">Khoảng giá <i class="fas fa-chevron-down uix-00313dcbd7"></i></div>
                        <div class="d-flex gap-2 align-items-center mb-3">
                            <input type="number" name="min_price" class="form-control form-control-sm rounded-0"
                                   placeholder="Từ" value="{{ request('min_price') }}">
                            <span class="uix-8bd34921dd">—</span>
                            <input type="number" name="max_price" class="form-control form-control-sm rounded-0"
                                   placeholder="Đến" value="{{ request('max_price') }}">
                        </div>
                        <button type="submit" class="btn-st-dark w-100 py-2-custom text-xs-custom">ÁP DỤNG</button>
                    </div>
                </form>
            </div>
        </aside>

        <!-- MAIN CONTENT -->
        <main class="col-lg-9">
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
                                <a href="{{ route('products.show', $product->id) }}">
                                    @if($product->so_luong <= 0)
                                        <div class="product-badge badge-out">Hết hàng</div>
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
