@extends('layouts.app')

@section('title', $product->ten_sp . ' - Sàn Tím Vi En')

@push('styles')
    @vite(['resources/css/views/product_show.css'])
    <style>
    .wishlist-btn-float {
        position: absolute;
        top: 10px;
        right: 10px;
        background: rgba(255,255,255,0.9);
        border: none;
        border-radius: 50%;
        width: 40px;
        height: 40px;
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
    .wishlist-btn-float i {
        font-size: 18px;
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
                        <div class="product-badge out-of-stock-badge-custom">Hết hàng</div>
                    @endif

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
                        <div class="delivery-item"><i class="fas fa-check-circle"></i> Thanh toán tiện lợi qua VietQR</div>
                        <div class="delivery-item"><i class="fas fa-check-circle"></i> Đổi trả dễ dàng trong 14 ngày</div>
                        <div class="delivery-item"><i class="fas fa-check-circle"></i> Giao hàng toàn quốc 2-3 ngày</div>
                    </div>
                    
                    <div class="detail-meta">
                        <div class="meta-item">
                            <span class="meta-label">Trạng thái:</span>
                            <span class="{{ $product->so_luong > 0 ? 'text-success' : 'text-danger' }} font-bold">
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

                    {{-- Hiển thị rating tổng quan --}}
                    @if(count($reviews) > 0)
                    <div class="review-summary mb-5">
                        <div class="row align-items-center">
                            <div class="col-auto text-center">
                                <div class="avg-rating-big">{{ number_format($product->average_rating, 1) }}</div>
                                <div class="avg-stars">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= round($product->average_rating))
                                            <i class="fas fa-star"></i>
                                        @else
                                            <i class="far fa-star"></i>
                                        @endif
                                    @endfor
                                </div>
                                <div class="avg-count">{{ $product->total_reviews }} đánh giá</div>
                            </div>
                            <div class="col">
                                @for($star = 5; $star >= 1; $star--)
                                    @php $cnt = $reviews->where('rating', $star)->count(); $pct = $product->total_reviews > 0 ? ($cnt / $product->total_reviews) * 100 : 0; @endphp
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <span class="small" style="min-width:40px;">{{ $star }} ★</span>
                                        <div class="flex-grow-1" style="background:#eee;height:8px;border-radius:4px;">
                                            <div style="width:{{ $pct }}%;background:var(--primary);height:8px;border-radius:4px;transition:width .5s;"></div>
                                        </div>
                                        <span class="small text-muted" style="min-width:20px;">{{ $cnt }}</span>
                                    </div>
                                @endfor
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Form đánh giá --}}
                    @auth
                        @if($userReview)
                            {{-- Đã đánh giá rồi --}}
                            <div class="review-notice review-notice--done mb-5">
                                <i class="fas fa-check-circle"></i>
                                <div>
                                    <strong>Bạn đã gửi đánh giá cho sản phẩm này.</strong>
                                    <p class="mb-0 small">
                                        @if($userReview->trang_thai === 'approved')
                                            Đánh giá của bạn đã được hiển thị công khai.
                                        @elseif($userReview->trang_thai === 'rejected')
                                            Đánh giá của bạn đã bị từ chối.
                                        @else
                                            Đánh giá đang chờ duyệt, sẽ hiển thị sau khi được phê duyệt.
                                        @endif
                                    </p>
                                </div>
                            </div>
                        @elseif($canReview)
                            {{-- Đã mua + đã thanh toán -> hiển thị form --}}
                            <div class="review-form-wrap mb-5 pb-5 border-bottom">
                                <h4 class="mb-4 uppercase font-bold text-md-custom letter-spacing-1-custom">
                                    <i class="fas fa-pen me-2" style="color:var(--primary);"></i>Viết đánh giá của bạn
                                </h4>
                                <form action="{{ route('reviews.store') }}" method="POST" id="reviewForm">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                    <input type="hidden" name="rating" id="ratingInput" value="5">

                                    {{-- Star Rating Interactive --}}
                                    <div class="mb-4">
                                        <label class="form-label text-uppercase small fw-bold d-block mb-2">Xếp hạng</label>
                                        <div class="star-rating-interactive" id="starRating">
                                            @for($s = 1; $s <= 5; $s++)
                                                <button type="button" class="star-btn {{ $s <= 5 ? 'active' : '' }}" data-value="{{ $s }}">
                                                    <i class="fas fa-star"></i>
                                                </button>
                                            @endfor
                                        </div>
                                        <div class="star-label mt-1" id="starLabel">Rất tốt</div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label text-uppercase small fw-bold">Nhận xét</label>
                                        <textarea name="comment" class="form-control rounded-0" rows="4"
                                            placeholder="Chia sẻ trải nghiệm của bạn về sản phẩm này..."></textarea>
                                    </div>
                                    <button type="submit" class="btn-submit-review">
                                        <i class="fas fa-paper-plane me-2"></i>GỬI ĐÁNH GIÁ
                                    </button>
                                </form>
                            </div>
                        @elseif($hasPurchased && !$paymentPaid)
                            {{-- Đã mua nhưng đơn hàng chưa hoàn thành --}}
                            <div class="review-notice review-notice--pending mb-5">
                                <i class="fas fa-clock"></i>
                                <div>
                                    <strong>Đơn hàng chưa hoàn thành</strong>
                                    <p class="mb-0 small">Bạn đã đặt mua sản phẩm này. Sau khi đơn hàng được chuyển sang trạng thái <strong>Hoàn thành</strong>, bạn sẽ nhận được thông báo và có thể đánh giá sản phẩm.</p>
                                </div>
                            </div>
                        @else
                            {{-- Chưa mua sản phẩm --}}
                            <div class="review-notice review-notice--locked mb-5">
                                <i class="fas fa-lock"></i>
                                <div>
                                    <strong>Chỉ khách hàng đã mua mới được đánh giá</strong>
                                    <p class="mb-0 small">Bạn cần mua và thanh toán thành công sản phẩm này để có thể gửi đánh giá.</p>
                                </div>
                            </div>
                        @endif
                    @else
                        <div class="review-notice review-notice--locked mb-5">
                            <i class="fas fa-user"></i>
                            <div>
                                <strong>Vui lòng đăng nhập để đánh giá</strong>
                                <p class="mb-0 small">
                                    <a href="{{ route('login') }}" class="text-dark fw-bold">Đăng nhập</a> để viết đánh giá cho sản phẩm này.
                                </p>
                            </div>
                        </div>
                    @endauth

                    {{-- Danh sách đánh giá --}}
                    <div class="reviews-list">
                        @forelse($reviews as $review)
                            <div class="review-item">
                                <div class="review-header">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="review-avatar">{{ mb_strtoupper(mb_substr($review->user->name, 0, 1)) }}</div>
                                        <div>
                                            <div class="review-user">{{ $review->user->name }}</div>
                                            <div class="review-date">{{ $review->created_at->format('d/m/Y') }}</div>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="review-rating">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="fa{{ $i <= $review->rating ? 's' : 'r' }} fa-star"></i>
                                            @endfor
                                        </div>
                                        
                                        {{-- Nút xóa cho Admin hoặc Chính chủ --}}
                                        @auth
                                            @if(auth()->user()->role === 'admin' || auth()->id() === $review->user_id)
                                                <form action="{{ route('reviews.destroy', $review) }}" method="POST" data-item-name="Đánh giá của bạn" onsubmit="return confirmForm(this, 'Đánh giá này sẽ bị xóa vĩnh viễn và không thể khôi phục.', 'XÓA ĐÁNH GIÁ')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-link text-danger p-0 no-underline-custom text-xs-custom" title="Xóa đánh giá">
                                                        <i class="fas fa-trash-alt me-1"></i>Xóa
                                                    </button>
                                                </form>
                                            @endif
                                        @endauth
                                    </div>
                                </div>
                                <div class="review-content">{{ $review->comment ?: '(Không có nhận xét)' }}</div>
                            </div>
                        @empty
                            <div class="text-center py-5 text-muted">
                                <i class="far fa-star fa-3x mb-3 d-block" style="color:#ddd;"></i>
                                Chưa có đánh giá nào được duyệt cho sản phẩm này.
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

                                {{-- Wishlist Toggle --}}
                                @auth
                                    <form action="{{ route('wishlist.toggle', $rp->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="wishlist-btn-float {{ auth()->user()->hasInWishlist($rp->id) ? 'active' : '' }}" title="{{ auth()->user()->hasInWishlist($rp->id) ? 'Bỏ yêu thích' : 'Yêu thích' }}">
                                            <i class="fas fa-heart"></i>
                                        </button>
                                    </form>
                                @else
                                    <a href="{{ route('login') }}" class="wishlist-btn-float" title="Đăng nhập để yêu thích">
                                        <i class="fas fa-heart"></i>
                                    </a>
                                @endauth

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

    // Star Rating Interactive
    document.addEventListener('DOMContentLoaded', function () {
        const starLabels = ['', 'Tệ', 'Không tốt', 'Bình thường', 'Tốt', 'Rất tốt'];
        const starBtns = document.querySelectorAll('.star-btn');
        const ratingInput = document.getElementById('ratingInput');
        const starLabel = document.getElementById('starLabel');

        if (!starBtns.length) return;

        let currentRating = parseInt(ratingInput ? ratingInput.value : 5);

        function setStars(val) {
            starBtns.forEach(function (btn) {
                btn.classList.toggle('active', parseInt(btn.dataset.value) <= val);
            });
            if (starLabel) starLabel.textContent = starLabels[val] || '';
            if (ratingInput) ratingInput.value = val;
            currentRating = val;
        }

        // Init
        setStars(currentRating);

        starBtns.forEach(function (btn) {
            btn.addEventListener('mouseenter', function () {
                setStars(parseInt(this.dataset.value));
            });
            btn.addEventListener('click', function () {
                currentRating = parseInt(this.dataset.value);
                setStars(currentRating);
            });
        });

        document.getElementById('starRating') && document.getElementById('starRating').addEventListener('mouseleave', function () {
            setStars(currentRating);
        });
    });
</script>
@endsection
