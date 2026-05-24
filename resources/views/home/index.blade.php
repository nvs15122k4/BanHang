@extends('layouts.app')

@section('title', 'Sàn Tím Vi En - Thời trang Việt phong cách')
@section('meta_description', 'Sàn Tím Vi En mang đến thời trang Việt hiện đại, chất lượng, giúp bạn tự tin thể hiện phong cách riêng qua những bộ sưu tập tinh tế mỗi ngày.')
@section('canonical', 'https://santimvien.vn/')
@section('og_title', 'Sàn Tím Vi En - Thời trang Việt phong cách')
@section('og_description', 'Sàn Tím Vi En mang đến thời trang Việt hiện đại, chất lượng, giúp bạn tự tin thể hiện phong cách riêng qua những bộ sưu tập tinh tế mỗi ngày.')

@push('styles')
    @vite(['public/css/views/home.css'])
@endpush

@section('content')

<!-- ── HERO ── -->
<section class="hero-section">
    <div class="hero-bg" id="heroSlider">
        <!-- Bạn có thể thêm 2-3 ảnh tùy ý tại đây -->
        <div class="hero-bg-item active uix-920b296261"></div>
        <div class="hero-bg-item uix-2452cd6dde"></div>
        <div class="hero-bg-item uix-e9a5c72b17"></div>
    </div>
    <div class="container">
        <div class="hero-content">
            <div class="hero-eyebrow">Bộ sưu tập Hè 2026</div>
            <h1 class="hero-title">Phong<br>Cách<br>Việt</h1>
            <p class="hero-subtitle">Thời trang chất lượng cao — thuần Việt, hiện đại, đậm cá tính</p>
            <a href="{{ route('products.index') }}" class="btn-st-dark px-5 py-3 display-inline-block">XEM NGAY</a>
        </div>
        <div class="hero-dots" id="heroDots">
            <div class="hero-dot active"></div>
            <div class="hero-dot"></div>
            <div class="hero-dot"></div>
        </div>
    </div>
</section>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const bgItems = document.querySelectorAll('.hero-bg-item');
    const dots = document.querySelectorAll('#heroDots .hero-dot');
    let currentIndex = 0;
    const intervalTime = 5000; // 5 giây chuyển ảnh một lần

    function nextSlide() {
        // Remove active class from current
        bgItems[currentIndex].classList.remove('active');
        dots[currentIndex].classList.remove('active');

        // Increment index
        currentIndex = (currentIndex + 1) % bgItems.length;

        // Add active class to next
        bgItems[currentIndex].classList.add('active');
        dots[currentIndex].classList.add('active');
    }

    // Auto play slider
    setInterval(nextSlide, intervalTime);
});
</script>
@endpush

<!-- ── BENEFITS BAR ── -->
<section class="benefits-bar">
    <div class="container">
        <div class="row g-3 justify-content-center">
            <div class="col-md-3 col-6">
                <div class="benefit-item">
                    <div class="benefit-icon"><i class="fas fa-shipping-fast"></i></div>
                    <div>
                        <div class="benefit-label">Giao hàng nhanh</div>
                        <p class="benefit-desc">Toàn quốc 2-3 ngày</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="benefit-item">
                    <div class="benefit-icon"><i class="fas fa-shield-alt"></i></div>
                    <div>
                        <div class="benefit-label">Thanh toán bảo mật</div>
                        <p class="benefit-desc">Mã hoá SSL 256-bit</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="benefit-item">
                    <div class="benefit-icon"><i class="fas fa-medal"></i></div>
                    <div>
                        <div class="benefit-label">Hàng chất lượng</div>
                        <p class="benefit-desc">100% hàng chính hãng</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="benefit-item">
                    <div class="benefit-icon"><i class="fas fa-undo-alt"></i></div>
                    <div>
                        <div class="benefit-label">Đổi trả dễ dàng</div>
                        <p class="benefit-desc">Trong vòng 14 ngày</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ── DANH MỤC NỔI BẬT ── -->
<section class="py-5 mt-5">
    <div class="container py-4">
        <div class="section-header">
            <h2 class="section-title">Danh mục nổi bật</h2>
        </div>
        <div class="row g-3">
            <div class="col-md-4">
                <div class="category-card">
                    <div class="category-img uix-0746f10362"></div>
                    <div class="category-overlay"></div>
                    <div class="category-content">
                        <h3 class="category-name">Thời trang Nữ</h3>
                        <a href="{{ route('products.index', ['loai_filter' => 'women']) }}" class="btn-shop-now">Khám phá</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="category-card">
                    <div class="category-img uix-21e6820b00"></div>
                    <div class="category-overlay"></div>
                    <div class="category-content">
                        <h3 class="category-name">Thời trang Nam</h3>
                        <a href="{{ route('products.index', ['loai_filter' => 'men']) }}" class="btn-shop-now">Khám phá</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="category-card">
                    <div class="category-img uix-77f8847808"></div>
                    <div class="category-overlay"></div>
                    <div class="category-content">
                        <h3 class="category-name">Trẻ em & Thể thao</h3>
                        <a href="{{ route('products.index') }}" class="btn-shop-now">Khám phá</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ── HÀNG KHUYẾN MÃI ── -->
<section class="py-5">
    <div class="container py-4">
        <div class="section-header">
            <h2 class="section-title">Hàng khuyến mãi</h2>
            <a href="{{ route('promotions.index') }}" class="section-link">Xem tất cả</a>
        </div>
        <div class="row">
            @foreach($promoProducts as $product)
                @php
                    $promo      = $product->promo;
                    $promoPrice = $product->promo_price;
                    $pct        = $product->gia > 0 ? round(($product->gia - $promoPrice) / $product->gia * 100) : 0;
                @endphp
                <div class="col-md-3 col-6">
                    <div class="product-card">
                        <div class="product-img-wrapper">
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
                            {{ number_format($promoPrice) }}đ
                            <span class="price-old">{{ number_format($product->gia) }}đ</span>
                        </div>
                    </div>
                </div>
            @endforeach
            @if($promoProducts->isEmpty())
                <div class="col-12 text-center text-muted my-4">Hiện tại không có chương trình khuyến mãi nào.</div>
            @endif
        </div>
    </div>
</section>

<!-- ── PROMO BANNER ── -->
<section class="promo-banner">
    <div class="container position-relative">
        <div class="promo-eyebrow">Ưu đãi đặc biệt</div>
        <div class="promo-title">BỘ SƯU TẬP<br>NĂM <span>2026</span></div>
        <div class="promo-title text-primary-custom uix-c3a0a563bb">GIẢM ĐẾN 50%</div>
        <p class="promo-desc">Chương trình khuyến mãi có thời hạn — Đừng bỏ lỡ!</p>
        <a href="{{ route('products.index') }}" class="btn-st-dark px-5 py-3 bg-white text-dark display-inline-block">MUA NGAY</a>
    </div>
</section>

<!-- ── HÀNG MỚI VỀ ── -->
<section class="py-5">
    <div class="container py-4">
        <div class="section-header">
            <h2 class="section-title">Hàng mới về</h2>
            <a href="{{ route('products.index', ['sort' => 'newest']) }}" class="section-link">Xem tất cả</a>
        </div>
        <div class="row">
            @foreach($latestProducts as $product)
                <div class="col-md-3 col-6">
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
                            {{ number_format($product->gia) }}đ
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- ── NEWSLETTER ── -->
<section class="newsletter-section">
    <div class="container">
        <h2 class="newsletter-title">Tham gia cùng chúng tôi</h2>
        <p class="tracking-wide-custom uix-3a4643dee9">Nhận ưu đãi 20% cho đơn hàng đầu tiên của bạn</p>
        <div class="newsletter-group">
            <input type="email" class="newsletter-input" placeholder="Địa chỉ email của bạn">
            <button class="btn-nl">Đăng ký</button>
        </div>
    </div>
</section>

@endsection
