@extends('layouts.app')

@section('title', 'Sàn Tím Vi En — Phong cách Việt, Sống đẹp mỗi ngày')

@push('styles')
    @vite(['resources/css/views/home.css'])
@endpush

@section('content')

<!-- ── HERO ── -->
<section class="hero-section">
    <div class="hero-bg" id="heroSlider">
        <!-- Bạn có thể thêm 2-3 ảnh tùy ý tại đây -->
        <div class="hero-bg-item active" style="background-image: url('https://res.cloudinary.com/dxvml3sji/image/upload/q_auto/f_auto/v1778634216/wug2aeesprt4ghjksljq.png')"></div>
        <div class="hero-bg-item" style="background-image: url('https://res.cloudinary.com/dxvml3sji/image/upload/q_auto/f_auto/v1778634214/hzbqlxybgcc2ftcbvvep.jpg')"></div>
        <div class="hero-bg-item" style="background-image: url('https://res.cloudinary.com/dxvml3sji/image/upload/q_auto/f_auto/v1778634215/dpyubaok2bjzmzayhqm8.jpg')"></div>
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
                    <div class="category-img" style="background-image:url('https://res.cloudinary.com/dxvml3sji/image/upload/q_auto/f_auto/v1778634213/moetvw8jk5czsz9um7xh.jpg')"></div>
                    <div class="category-overlay"></div>
                    <div class="category-content">
                        <h3 class="category-name">Thời trang Nữ</h3>
                        <a href="{{ route('products.index', ['loai_filter' => 'women']) }}" class="btn-shop-now">Khám phá</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="category-card">
                    <div class="category-img" style="background-image:url('https://res.cloudinary.com/dxvml3sji/image/upload/q_auto/f_auto/v1778634215/s6e4glwbkp5umuyfsvny.jpg')"></div>
                    <div class="category-overlay"></div>
                    <div class="category-content">
                        <h3 class="category-name">Thời trang Nam</h3>
                        <a href="{{ route('products.index', ['loai_filter' => 'men']) }}" class="btn-shop-now">Khám phá</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="category-card">
                    <div class="category-img" style="background-image:url('https://res.cloudinary.com/dxvml3sji/image/upload/q_auto/f_auto/v1778634213/tz3nx2t8vq1yrjejw7cv.jpg')"></div>
                    <div class="category-overlay"></div>
                    <div class="category-content">
                        <h3 class="category-name">Trẻ em & Thể thao</h3>
                        <a href="{{ route('products.index', ['loai_filter' => 'kids']) }}" class="btn-shop-now">Khám phá</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ── SẢN PHẨM MỚI NHẤT ── -->
<section class="py-5">
    <div class="container py-4">
        <div class="section-header">
            <h2 class="section-title">Hàng mới về</h2>
            <a href="{{ route('products.index') }}" class="section-link">Xem tất cả</a>
        </div>
        <div class="row">
            @foreach($products->take(8) as $product)
                <div class="col-md-3 col-6">
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
                                    <form action="{{ route('cart.add') }}" method="POST" class="w-full">
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
            @endforeach
        </div>
    </div>
</section>

<!-- ── PROMO BANNER ── -->
<section class="promo-banner">
    <div class="container position-relative">
        <div class="promo-eyebrow">Ưu đãi đặc biệt</div>
        <div class="promo-title">BỘ SƯU TẬP<br>NĂM <span>2026</span></div>
        <div class="promo-title text-primary-custom" style="font-size:36px;">GIẢM ĐẾN 50%</div>
        <p class="promo-desc">Chương trình khuyến mãi có thời hạn — Đừng bỏ lỡ!</p>
        <a href="{{ route('products.index') }}" class="btn-st-dark px-5 py-3 bg-white text-dark display-inline-block">MUA NGAY</a>
    </div>
</section>

<!-- ── SẢN PHẨM NỔI BẬT ── -->
<section class="py-5">
    <div class="container py-4">
        <div class="section-header">
            <h2 class="section-title">Sản phẩm nổi bật</h2>
            <a href="{{ route('products.index') }}" class="section-link">Xem tất cả</a>
        </div>
        <div class="row">
            @foreach($products->take(4) as $product)
                <div class="col-md-3 col-6">
                    <div class="product-card">
                        <div class="product-img-wrapper">
                            <a href="{{ route('products.show', $product->id) }}">
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
                                    <form action="{{ route('cart.add') }}" method="POST" class="w-full">
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
            @endforeach
        </div>
    </div>
</section>

<!-- ── NEWSLETTER ── -->
<section class="newsletter-section">
    <div class="container">
        <h2 class="newsletter-title">Tham gia cùng chúng tôi</h2>
        <p class="tracking-wide-custom" style="font-size:16px; color:rgba(255,255,255,0.5);">Nhận ưu đãi 20% cho đơn hàng đầu tiên của bạn</p>
        <div class="newsletter-group">
            <input type="email" class="newsletter-input" placeholder="Địa chỉ email của bạn">
            <button class="btn-nl">Đăng ký</button>
        </div>
    </div>
</section>

@endsection
