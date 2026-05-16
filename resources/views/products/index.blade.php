@extends('layouts.app')

@section('title', 'Sản phẩm - Sàn Tím Vi En')

@push('styles')
    @vite(['resources/css/views/product_index.css'])
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
                <h1 style="font-weight:800; font-size:22px; text-transform:uppercase; letter-spacing:1px; margin-bottom:30px;">Cửa hàng</h1>

                <form method="GET" action="{{ route('products.index') }}" id="filterForm">
                    <!-- Tìm kiếm -->
                    <div class="filter-section">
                        <div class="filter-title">Tìm kiếm</div>
                        <div class="position-relative">
                            <input type="text" name="search" class="form-control rounded-0" placeholder="Tên sản phẩm..."
                                   value="{{ request('search') }}">
                            <button type="submit" style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;">
                                <i class="fas fa-search text-muted" style="font-size:13px;"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Danh mục -->
                    <div class="filter-section">
                        <div class="filter-title">Danh mục <i class="fas fa-chevron-down" style="font-size:9px;"></i></div>
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
                        <div class="filter-title">Khoảng giá <i class="fas fa-chevron-down" style="font-size:9px;"></i></div>
                        <div class="d-flex gap-2 align-items-center mb-3">
                            <input type="number" name="min_price" class="form-control form-control-sm rounded-0"
                                   placeholder="Từ" value="{{ request('min_price') }}">
                            <span style="color:#CCC;">—</span>
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
                    <span class="text-muted d-none d-md-block" style="font-size:11px; font-weight:700; letter-spacing:1px;">SẮP XẾP:</span>
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
                                    @elseif($product->created_at > now()->subDays(7))
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
                                <div class="product-actions">
                                    @if($product->so_luong > 0)
                                        <form action="{{ route('cart.add') }}" method="POST" class="add-to-cart-form" style="width:100%;">
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
        <div class="modal-content" style="border-radius:0; border:none;">
            <div class="modal-header" style="background:var(--text-main); color:#FFF; border-radius:0;">
                <h5 class="modal-title" style="font-weight:700; text-transform:uppercase;">Thêm sản phẩm mới</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label">Tên sản phẩm</label>
                            <input type="text" name="ten_sp" class="form-control" style="border-radius:0;" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Giá (vnđ)</label>
                            <input type="number" name="gia" class="form-control" min="0" style="border-radius:0;" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Số lượng kho</label>
                            <input type="number" name="so_luong" class="form-control" min="0" style="border-radius:0;" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Danh mục</label>
                            <select name="loai" class="form-select" style="border-radius:0;">
                                @foreach($loaiList as $loai)
                                    <option value="{{ $loai }}">{{ ucfirst($loai) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Mô tả</label>
                            <textarea name="mo_ta" class="form-control" rows="4" style="border-radius:0;"></textarea>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Hình ảnh</label>
                            <input type="file" name="anh_file" class="form-control" accept="image/*" style="border-radius:0;">
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
