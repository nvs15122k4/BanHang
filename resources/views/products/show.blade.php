@extends('layouts.app')

@section('title', $product->ten_sp . ' - Sàn Tím Vi En')

@push('styles')
<style>
    /* =========================================
       PRODUCT DETAIL - SÀN TÍM VI EN
       ========================================= */
    :root { --primary: #7C3AED; }
    .breadcrumb-ava {
        font-size: 13px;
        color: var(--text-light);
        margin: 20px 0 40px;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    
    .breadcrumb-ava a {
        color: var(--text-main);
        text-decoration: none;
        font-weight: 600;
    }

    .product-detail-container {
        margin-bottom: 100px;
    }

    /* Gallery */
    .detail-img-wrap {
        background: #F9F9F9;
        height: 700px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 40px;
        position: relative;
        overflow: hidden;
    }
    
    .detail-img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
        transition: transform 0.5s ease;
    }

    .detail-img:hover {
        transform: scale(1.05);
    }

    /* Info */
    .detail-info {
        padding-left: 60px;
    }
    
    @media (max-width: 992px) {
        .detail-info { padding-left: 0; margin-top: 40px; }
        .detail-img-wrap { height: 500px; }
    }

    .detail-category {
        font-size: 12px;
        color: var(--text-light);
        text-transform: uppercase;
        letter-spacing: 2px;
        margin-bottom: 15px;
        display: block;
    }

    .detail-title {
        font-weight: 700;
        font-size: 42px;
        color: var(--text-main);
        line-height: 1.1;
        margin-bottom: 25px;
        text-transform: uppercase;
    }

    .detail-price {
        font-weight: 700;
        font-size: 32px;
        color: var(--text-main);
        margin-bottom: 40px;
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .detail-price .price-old {
        font-size: 20px;
        color: #AAA;
        text-decoration: line-through;
        font-weight: 400;
    }

    .detail-desc {
        color: var(--text-light);
        font-size: 16px;
        line-height: 1.8;
        margin-bottom: 50px;
        border-top: 1px solid #EEE;
        padding-top: 30px;
    }

    /* Actions */
    .action-row {
        display: flex;
        gap: 20px;
        margin-bottom: 40px;
    }

    .qty-control {
        display: flex;
        align-items: center;
        border: 1px solid #000;
        height: 55px;
    }
    
    .qty-btn {
        background: transparent;
        border: none;
        color: #000;
        width: 50px;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        cursor: pointer;
    }
    
    .qty-input {
        background: transparent;
        border: none;
        color: #000;
        text-align: center;
        width: 60px;
        font-weight: 700;
        font-size: 16px;
        outline: none;
    }

    .btn-add-cart {
        background: var(--primary);
        color: #fff;
        border: none;
        height: 55px;
        padding: 0 60px;
        font-weight: 700;
        font-size: 14px;
        text-transform: uppercase;
        letter-spacing: 2px;
        flex-grow: 1;
        transition: all 0.3s;
    }
    
    .btn-add-cart:hover {
        background: #6D28D9;
    }

    /* Delivery Options */
    .delivery-options {
        border: 1px solid #EEE;
        padding: 20px;
        margin-bottom: 30px;
    }
    .delivery-title { font-size:13px; font-weight:700; text-transform:uppercase; letter-spacing:1px; margin-bottom:15px; }
    .delivery-item { display:flex; align-items:flex-start; gap:10px; margin-bottom:10px; font-size:13px; color:#666; }
    .delivery-item i { color:var(--primary); margin-top:2px; flex-shrink:0; }

    .btn-add-cart:disabled {
        background: #CCC;
        cursor: not-allowed;
    }

    /* Meta */
    .detail-meta {
        border-top: 1px solid #EEE;
        padding-top: 30px;
        font-size: 14px;
        color: var(--text-light);
    }

    .meta-item {
        margin-bottom: 10px;
        display: flex;
        gap: 10px;
    }

    .meta-label {
        font-weight: 700;
        color: var(--text-main);
        min-width: 100px;
        text-transform: uppercase;
        font-size: 12px;
    }

    /* Tabs */
    .tabs-ava {
        margin-top: 100px;
        border-bottom: 1px solid #EEE;
    }
    
    .tabs-ava .nav-link {
        color: #AAA;
        font-weight: 700;
        font-size: 14px;
        padding: 20px 40px;
        border: none;
        border-bottom: 2px solid transparent;
        background: transparent;
        text-transform: uppercase;
        letter-spacing: 2px;
    }
    
    .tabs-ava .nav-link.active {
        color: #000;
        border-bottom: 2px solid #000;
    }
    
    .tab-content-ava {
        padding: 60px 0;
        color: var(--text-main);
        line-height: 1.8;
    }

    /* Related Products */
    .section-title {
        font-weight: 700;
        font-size: 24px;
        text-transform: uppercase;
        letter-spacing: 2px;
        margin-bottom: 40px;
        text-align: center;
    }

    .product-card {
        border: none;
        margin-bottom: 60px;
        transition: all 0.3s ease;
        position: relative;
    }
    
    .product-img-wrapper {
        position: relative;
        aspect-ratio: 3/4;
        background: #F4F4F4;
        overflow: hidden;
        margin-bottom: 15px;
    }
    
    .product-img {
        position: absolute;
        top: 0; left: 0; width: 100%; height: 100%;
        object-fit: cover;
        transition: transform 1.2s cubic-bezier(0.165, 0.84, 0.44, 1);
    }

    .product-card:hover .product-img {
        transform: scale(1.05);
    }

    .product-actions {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        transform: translateY(100%);
        transition: transform 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
        z-index: 4;
        display: flex;
    }

    .product-card:hover .product-actions {
        transform: translateY(0);
    }

    .btn-quick-add {
        background: rgba(255, 255, 255, 0.9);
        color: #000;
        border: none;
        padding: 15px;
        width: 100%;
        font-weight: 700;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 2px;
        backdrop-filter: blur(5px);
        transition: all 0.3s;
    }

    .btn-quick-add:hover {
        background: #000;
        color: #fff;
    }

    .product-info {
        padding: 0;
        text-align: left;
    }

    .product-category {
        font-size: 10px;
        color: #888;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        margin-bottom: 5px;
        display: block;
    }

    .product-title {
        font-weight: 600;
        font-size: 15px;
        margin-bottom: 5px;
        color: #000;
        text-decoration: none;
        display: block;
        line-height: 1.4;
    }

    .product-price {
        font-weight: 700;
        font-size: 15px;
        color: #000;
    }

    /* Reviews */
    .review-item {
        border-bottom: 1px solid #EEE;
        padding: 30px 0;
    }

    .review-header {
        display: flex;
        justify-content: space-between;
        margin-bottom: 15px;
    }

    .review-user {
        font-weight: 700;
        text-transform: uppercase;
        font-size: 14px;
    }

    .review-date {
        font-size: 12px;
        color: #AAA;
    }

    .review-rating {
        color: #000;
        margin-bottom: 10px;
    }

    .review-content {
        color: var(--text-light);
    }
</style>
@endpush

@section('content')
<div class="container py-4">
    <div class="breadcrumb-ava">
        <a href="{{ route('home') }}">Trang chủ</a> &nbsp;/&nbsp; 
        <a href="{{ route('products.index') }}">Cửa hàng</a> &nbsp;/&nbsp; 
        {{ $product->ten_sp }}
    </div>

    <div class="product-detail-container">
        <div class="row">
            <div class="col-lg-6">
                <div class="detail-img-wrap">
                    @if($product->so_luong <= 0)
                        <div class="product-badge" style="position:absolute; top:20px; left:20px; background: #E74C3C; color: white; padding:6px 15px; font-weight:700; z-index:1; text-transform:uppercase;">Hết hàng</div>
                    @endif
                    
                    @if($product->anh)
                        <img src="{{ $product->image_path }}" alt="{{ $product->ten_sp }}" class="detail-img" id="mainImage">
                    @else
                        <i class="fas fa-image fa-6x text-muted"></i>
                    @endif
                </div>
            </div>
            
            <div class="col-lg-6">
                <div class="detail-info">
                    <span class="detail-category">{{ $product->loai }}</span>
                    <h1 class="detail-title">{{ $product->ten_sp }}</h1>
                    
                    <div class="detail-price">
                        @if($product->gia_goc > $product->gia)
                            <span class="price-old">{{ number_format($product->gia_goc) }}đ</span>
                        @endif
                        {{ number_format($product->gia) }}đ
                    </div>
                    
                    <div class="detail-desc">
                        {!! nl2br(e($product->mo_ta ?: 'Chưa có mô tả cho sản phẩm này.')) !!}
                    </div>
                    
                    @if($product->so_luong > 0)
                        <form action="{{ route('cart.add') }}" method="POST">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <div class="action-row">
                                <div class="qty-control">
                                    <button type="button" class="qty-btn" onclick="decrementQty()">-</button>
                                    <input type="number" name="so_luong" id="qtyInput" class="qty-input" value="1" min="1" max="{{ $product->so_luong }}">
                                    <button type="button" class="qty-btn" onclick="incrementQty()">+</button>
                                </div>
                                <button type="submit" class="btn-add-cart">Thêm vào giỏ hàng</button>
                            </div>
                        </form>
                    @else
                        <button class="btn-add-cart mb-4" disabled>SẢN PHẨM TẠM HẾT HÀNG</button>
                    @endif

                    {{-- Delivery Options (theo wireframe) --}}
                    <div class="delivery-options mt-4">
                        <div class="delivery-title">Tuỳ chọn giao hàng</div>
                        <div class="delivery-item"><i class="fas fa-check-circle"></i> 100% hàng chính hãng Sàn Tím Vi En</div>
                        <div class="delivery-item"><i class="fas fa-check-circle"></i> Có thể thanh toán khi nhận hàng (COD)</div>
                        <div class="delivery-item"><i class="fas fa-check-circle"></i> Đổi trả dễ dàng trong 14 ngày</div>
                        <div class="delivery-item"><i class="fas fa-check-circle"></i> Giao hàng toàn quốc 2-3 ngày</div>
                    </div>
                    
                    <div class="detail-meta">
                        <div class="meta-item">
                            <span class="meta-label">Trạng thái:</span>
                            <span class="{{ $product->so_luong > 0 ? 'text-success' : 'text-danger' }} font-weight-bold">
                                {{ $product->so_luong > 0 ? 'Còn hàng (' . $product->so_luong . ')' : 'Hết hàng' }}
                            </span>
                        </div>
                        <div class="meta-item">
                            <span class="meta-label">Danh mục:</span>
                            <span>{{ ucfirst($product->loai) }}</span>
                        </div>
                        <div class="meta-item">
                            <span class="meta-label">Mã SP:</span>
                            <span>#{{ str_pad($product->id, 6, '0', STR_PAD_LEFT) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- TABS -->
    <ul class="nav nav-tabs tabs-ava" id="productTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="desc-tab" data-bs-toggle="tab" data-bs-target="#desc-pane" type="button" role="tab">Mô tả chi tiết</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="review-tab" data-bs-toggle="tab" data-bs-target="#review-pane" type="button" role="tab">Đánh giá ({{ count($reviews) }})</button>
        </li>
    </ul>
    
    <div class="tab-content tab-content-ava">
        <div class="tab-pane fade show active" id="desc-pane" role="tabpanel">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <p>{!! nl2br(e($product->mo_ta ?: 'Sản phẩm đang được cập nhật thông tin chi tiết.')) !!}</p>
                </div>
            </div>
        </div>
        
        <div class="tab-pane fade" id="review-pane" role="tabpanel">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    @auth
                        @if(!$userReview)
                            <div class="mb-5 pb-5 border-bottom">
                                <h4 class="mb-4 text-uppercase font-weight-bold" style="font-size: 18px; letter-spacing: 1px;">Viết đánh giá của bạn</h4>
                                <form action="{{ route('reviews.store') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                    <div class="mb-3">
                                        <label class="form-label text-uppercase small font-weight-bold">Xếp hạng</label>
                                        <select name="rating" class="form-select" style="border-radius:0;">
                                            <option value="5">5 Sao - Rất tốt</option>
                                            <option value="4">4 Sao - Tốt</option>
                                            <option value="3">3 Sao - Trung bình</option>
                                            <option value="2">2 Sao - Kém</option>
                                            <option value="1">1 Sao - Rất kém</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label text-uppercase small font-weight-bold">Bình luận</label>
                                        <textarea name="comment" class="form-control" rows="4" style="border-radius:0;" placeholder="Chia sẻ trải nghiệm của bạn về sản phẩm này..."></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-dark" style="border-radius:0; padding:12px 30px; font-weight:700;">GỬI ĐÁNH GIÁ</button>
                                </form>
                            </div>
                        @else
                            <div class="alert alert-info" style="border-radius:0;">
                                Bạn đã đánh giá sản phẩm này. Cảm ơn bạn!
                            </div>
                        @endif
                    @else
                        <div class="alert alert-light border text-center py-4" style="border-radius:0;">
                            Vui lòng <a href="{{ route('login') }}" class="font-weight-bold text-dark">đăng nhập</a> để viết đánh giá.
                        </div>
                    @endauth

                    <div class="reviews-list">
                        @forelse($reviews as $review)
                            <div class="review-item">
                                <div class="review-header">
                                    <span class="review-user">{{ $review->user->name }}</span>
                                    <span class="review-date">{{ $review->created_at->format('d/m/Y') }}</span>
                                </div>
                                <div class="review-rating">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fa{{ $i <= $review->rating ? 's' : 'r' }} fa-star"></i>
                                    @endfor
                                </div>
                                <div class="review-content">
                                    {{ $review->comment }}
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4 text-muted">
                                Chưa có đánh giá nào cho sản phẩm này.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- RELATED PRODUCTS -->
    @if(count($relatedProducts) > 0)
        <div class="mt-5 pt-5">
            <h2 class="section-title">Sản phẩm tương tự</h2>
            {{-- Similar Products section theo wireframe --}}
            <div class="row">
                @foreach($relatedProducts as $rp)
                    <div class="col-md-3 col-6">
                        <div class="product-card">
                            <div class="product-img-wrapper">
                                <a href="{{ route('products.show', $rp->id) }}">
                                    @if($rp->anh)
                                        <img src="{{ $rp->image_path }}" alt="{{ $rp->ten_sp }}" class="product-img">
                                    @else
                                        <div class="product-img d-flex align-items-center justify-content-center bg-light">
                                            <i class="fas fa-image fa-2x text-muted"></i>
                                        </div>
                                    @endif
                                </a>

                                <div class="product-actions">
                                    @if($rp->so_luong > 0)
                                        <form action="{{ route('cart.add') }}" method="POST" class="flex-grow-1 add-to-cart-form">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $rp->id }}">
                                            <input type="hidden" name="so_luong" value="1">
                                            <button type="submit" class="btn-quick-add">Thêm nhanh +</button>
                                        </form>
                                    @else
                                        <button class="btn-quick-add" disabled>Hết hàng</button>
                                    @endif
                                </div>
                            </div>
                            <div class="product-info">
                                <span class="product-category">{{ $rp->loai }}</span>
                                <a href="{{ route('products.show', $rp->id) }}" class="product-title">{{ $rp->ten_sp }}</a>
                                <div class="product-price">
                                    {{ number_format($rp->gia) }}đ
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>

<script>
    function incrementQty() {
        var input = document.getElementById('qtyInput');
        var max = parseInt(input.getAttribute('max'));
        var val = parseInt(input.value);
        if (val < max) input.value = val + 1;
    }

    function decrementQty() {
        var input = document.getElementById('qtyInput');
        var val = parseInt(input.value);
        if (val > 1) input.value = val - 1;
    }
</script>
@endsection
