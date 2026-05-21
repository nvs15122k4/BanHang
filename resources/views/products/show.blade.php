@extends('layouts.app')

@section('title', $product->ten_sp . ' - Sàn Tím Vi En')

@push('styles')
@vite(['public/css/views/product_show.css'])
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

                {{-- Size Selection --}}
                @if($product->sizes && count($product->sizes) > 0)
                <div class="size-selection mb-3">
                    <label class="form-label fw-semibold">Chọn kích cỡ:</label>
                    <div class="d-flex flex-wrap gap-2" id="sizeOptions">
                        @foreach($product->sizes as $size)
                        <button type="button" class="btn btn-outline-primary size-btn" data-size="{{ $size }}" onclick="selectSize('{{ $size }}')">
                            {{ $size }}
                        </button>
                        @endforeach
                    </div>
                    <input type="hidden" name="selected_size" id="selectedSize" value="">
                    <small class="text-danger d-none" id="sizeError">Vui lòng chọn kích cỡ</small>
                </div>
                @endif

                @if($product->so_luong > 0)
                <form action="{{ route('cart.add') }}" method="POST" id="addToCartForm"
                      data-requires-size="{{ count($product->sizes ?? []) > 0 ? '1' : '0' }}"
                      data-product-name="{{ $product->ten_sp }}"
                      data-size-options='@json($product->sizes ?? [])'>
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="size" id="cartSize" value="">
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

                {{-- Size Recommendation --}}
                @if($sizeRecommendation)
                <div class="size-recommendation-card mt-4 p-3 border rounded uix-14d428d219">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i class="fas fa-info-circle text-primary"></i>
                        <strong>Gợi ý kích cỡ dựa vào thông tin cơ thể của bạn</strong>
                    </div>
                    <p class="mb-2 text-muted uix-df67104f3b">
                        Dựa vào chiều cao {{ $sizeRecommendation['height'] }}cm, cân nặng {{ $sizeRecommendation['weight'] }}kg (BMI: {{ $sizeRecommendation['bmi'] }}):
                    </p>
                    <div class="size-recommendation-badges">
                        @foreach($sizeRecommendation['all_sizes'] as $index => $rec)
                        <span class="badge {{ $index === 0 ? 'bg-primary' : 'bg-light text-dark' }} me-2 mb-2 uix-41fe5bbc0b">
                            {{ $rec['size'] }}
                            @if($index === 0)
                            <i class="fas fa-star ms-1"></i> Được gợi ý
                            @endif
                        </span>
                        @endforeach
                    </div>
                    <p class="mb-0 text-muted small mt-2">
                        Để cập nhật hoặc thay đổi gợi ý này, <a href="{{ route('profile.index') }}">cập nhật thông tin cơ thể</a>.
                    </p>
                </div>
                @elseif(auth()->check())
                <div class="size-recommendation-card mt-4 p-3 border rounded uix-cbcf40315a">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i class="fas fa-info-circle text-warning"></i>
                        <strong>Nhận gợi ý kích cỡ phù hợp</strong>
                    </div>
                    <p class="mb-0 text-muted uix-df67104f3b">
                        Hãy cập nhật chiều cao và cân nặng trong <a href="{{ route('profile.index') }}">thông tin cá nhân</a> để nhận gợi ý kích cỡ sản phẩm phù hợp nhất.
                    </p>
                </div>
                @else
                <div class="size-recommendation-card mt-4 p-3 border rounded uix-c114d30d77">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i class="fas fa-info-circle text-info"></i>
                        <strong>Cần gợi ý kích cỡ?</strong>
                    </div>
                    <p class="mb-0 text-muted uix-df67104f3b">
                        <a href="{{ route('login') }}">Đăng nhập</a> và cập nhật thông tin cơ thể của bạn để nhận gợi ý kích cỡ sản phẩm phù hợp.
                    </p>
                </div>
                @endif

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
                                    @if($i <=round($product->average_rating))
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
                                <span class="small uix-516adaa881">{{ $star }} ★</span>
                                <div class="flex-grow-1 uix-b0771944f3">
                                    <div style="width:{{ $pct }}%;background:var(--primary);height:8px;border-radius:4px;transition:width .5s;"></div>
                                </div>
                                <span class="small text-muted uix-acf8e3fb4b">{{ $cnt }}</span>
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
                        <i class="fas fa-pen me-2 uix-90010faf15"></i>Viết đánh giá của bạn
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
                        <i class="far fa-star fa-3x mb-3 d-block uix-30261166ec"></i>
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
                        <form action="{{ route('cart.add') }}" method="POST" class="flex-grow-1 add-to-cart-form"
                              data-requires-size="{{ count($rp->sizes ?? []) > 0 ? '1' : '0' }}"
                              data-product-name="{{ $rp->ten_sp }}"
                              data-size-options='@json($rp->sizes ?? [])'>
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

    function selectSize(size) {
        document.getElementById('selectedSize').value = size;
        document.getElementById('cartSize').value = size;
        document.querySelectorAll('.size-btn').forEach(btn => {
            btn.classList.remove('active');
        });
        const activeBtn = document.querySelector(`.size-btn[data-size="${CSS.escape(size)}"]`);
        if (activeBtn) activeBtn.classList.add('active');
        document.getElementById('sizeError').classList.add('d-none');
    }

    document.addEventListener('DOMContentLoaded', function() {
        const starLabels = ['', 'Tệ', 'Không tốt', 'Bình thường', 'Tốt', 'Rất tốt'];
        const starBtns = document.querySelectorAll('.star-btn');
        const ratingInput = document.getElementById('ratingInput');
        const starLabel = document.getElementById('starLabel');

        if (!starBtns.length) return;

        let currentRating = parseInt(ratingInput ? ratingInput.value : 5);

        function setStars(val) {
            starBtns.forEach(function(btn) {
                btn.classList.toggle('active', parseInt(btn.dataset.value) <= val);
            });
            if (starLabel) starLabel.textContent = starLabels[val] || '';
            if (ratingInput) ratingInput.value = val;
            currentRating = val;
        }

        setStars(currentRating);

        starBtns.forEach(function(btn) {
            btn.addEventListener('mouseenter', function() {
                setStars(parseInt(this.dataset.value));
            });
            btn.addEventListener('click', function() {
                currentRating = parseInt(this.dataset.value);
                setStars(currentRating);
            });
        });

        document.getElementById('starRating') && document.getElementById('starRating').addEventListener('mouseleave', function() {
            setStars(currentRating);
        });
    });
</script>
@endsection
