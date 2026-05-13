@extends('layouts.app')

@section('title', 'Sàn Tím Vi En — Phong cách Việt, Sống đẹp mỗi ngày')

@push('styles')
<style>
    /* ── HERO ── */
    .hero-section {
        position: relative;
        background: #F5F3FF;
        min-height: 780px;
        display: flex;
        align-items: center;
        overflow: hidden;
    }
    .hero-bg {
        position: absolute; top:0; left:0; width:100%; height:100%;
        z-index: 0;
    }
    .hero-bg-item {
        position: absolute; top:0; left:0; width:100%; height:100%;
        background-size: cover; background-position: center;
        opacity: 0;
        transition: opacity 1.5s ease-in-out;
        z-index: 0;
    }
    .hero-bg-item.active {
        opacity: 0.3; /* Độ mờ nhẹ cho phong cách minimalist */
    }
    .hero-content { position: relative; z-index: 1; max-width: 680px; }
    .hero-eyebrow {
        font-size: 13px; font-weight: 700; letter-spacing: 4px;
        color: var(--primary); text-transform: uppercase; margin-bottom: 20px;
    }
    .hero-title {
        font-weight: 800; font-size: 80px; line-height: 1.05;
        color: #000; margin-bottom: 25px;
        text-transform: uppercase; letter-spacing: -2px;
    }
    .hero-subtitle {
        font-size: 18px; color: #666; margin-bottom: 45px; line-height: 1.6;
    }
    .hero-dots { display: flex; gap: 8px; margin-top: 40px; }
    .hero-dot { width: 8px; height: 8px; border-radius: 50%; background: #DDD; }
    .hero-dot.active { background: var(--primary); width: 24px; border-radius: 4px; }
    @media (max-width: 768px) {
        .hero-title { font-size: 52px; }
        .hero-section { min-height: 560px; }
    }

    /* ── BENEFITS BAR ── */
    .benefits-bar {
        background: #FFF; border-top: 1px solid #EEE; border-bottom: 1px solid #EEE;
        padding: 30px 0;
    }
    .benefit-item { display: flex; align-items: center; gap: 14px; }
    .benefit-icon { font-size: 26px; color: var(--primary); flex-shrink: 0; }
    .benefit-label { font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
    .benefit-desc { font-size: 12px; color: #888; margin: 0; }

    /* ── SECTION HEADERS ── */
    .section-header { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 50px; }
    .section-title {
        font-weight: 800; font-size: 28px; color: #000;
        text-transform: uppercase; letter-spacing: 1px; margin: 0;
    }
    .section-link {
        color: #000; font-weight: 700; font-size: 12px;
        text-transform: uppercase; letter-spacing: 1px;
        border-bottom: 2px solid #000; padding-bottom: 3px;
    }
    .section-link:hover { color: var(--primary); border-bottom-color: var(--primary); }

    /* ── CATEGORY CARDS ── */
    .category-card {
        height: 480px; position: relative; overflow: hidden;
        display: flex; align-items: center; justify-content: center; cursor: pointer;
    }
    .category-img {
        position: absolute; top:0; left:0; width:100%; height:100%;
        background-size: cover; background-position: center;
        transition: transform 1.2s ease; z-index: 0;
    }
    .category-card:hover .category-img { transform: scale(1.08); }
    .category-overlay {
        position: absolute; top:0; left:0; width:100%; height:100%;
        background: linear-gradient(to top, rgba(0,0,0,0.55) 0%, rgba(0,0,0,0.05) 60%);
        z-index: 1; transition: background 0.3s;
    }
    .category-card:hover .category-overlay { background: linear-gradient(to top, rgba(0,0,0,0.65) 0%, rgba(0,0,0,0.1) 60%); }
    .category-content { position: relative; z-index: 2; text-align: center; padding: 0 20px; }
    .category-name {
        color: #FFF; font-weight: 800; font-size: 22px;
        text-transform: uppercase; letter-spacing: 3px; margin-bottom: 18px;
    }
    .btn-shop-now {
        background: #FFF; color: #000; padding: 11px 28px;
        font-weight: 700; font-size: 11px; text-transform: uppercase;
        letter-spacing: 2px; transition: all 0.3s; display: inline-block;
    }
    .btn-shop-now:hover { background: var(--primary); color: #FFF; }

    /* ── PRODUCT CARD ── */
    .product-card { border: none; margin-bottom: 50px; position: relative; }
    .product-img-wrapper {
        position: relative; aspect-ratio: 3/4;
        background: #F5F5F5; overflow: hidden; margin-bottom: 14px;
    }
    .product-img {
        position: absolute; top:0; left:0; width:100%; height:100%;
        object-fit: cover; transition: transform 1.2s cubic-bezier(.165,.84,.44,1);
    }
    .product-card:hover .product-img { transform: scale(1.06); }
    .product-badge {
        position: absolute; top:12px; left:12px; z-index: 3;
        font-size: 10px; font-weight: 700; text-transform: uppercase;
        letter-spacing: 1px; padding: 5px 10px;
        background: var(--primary); color: #FFF;
    }
    .badge-out { background: #E53E3E; }
    .product-actions {
        position: absolute; bottom:0; left:0; width:100%;
        transform: translateY(100%);
        transition: transform 0.4s cubic-bezier(.165,.84,.44,1);
        z-index: 4;
    }
    .product-card:hover .product-actions { transform: translateY(0); }
    .btn-quick-add {
        background: rgba(255,255,255,0.92); color: #000; border: none;
        padding: 14px; width: 100%; font-weight: 700; font-size: 11px;
        text-transform: uppercase; letter-spacing: 2px;
        backdrop-filter: blur(6px); transition: all 0.3s; cursor: pointer;
    }
    .btn-quick-add:hover { background: var(--primary); color: #FFF; }
    .btn-quick-add:disabled { background: rgba(0,0,0,0.5); color: #FFF; cursor: not-allowed; }
    .product-category {
        font-size: 10px; color: #999; text-transform: uppercase;
        letter-spacing: 1.5px; margin-bottom: 5px; display: block;
    }
    .product-title {
        font-weight: 600; font-size: 14px; margin-bottom: 6px;
        color: #000; display: block; line-height: 1.4;
    }
    .product-title:hover { color: var(--primary); }
    .product-price {
        font-weight: 700; font-size: 14px; color: #000;
        display: flex; gap: 8px; align-items: center;
    }
    .price-old { text-decoration: line-through; color: #BBB; font-size: 12px; font-weight: 400; }

    /* ── PROMO BANNER ── */
    .promo-banner {
        background: #1A1A1A;
        padding: 70px 0;
        text-align: center;
        position: relative;
        overflow: hidden;
    }
    .promo-banner::before {
        content: '';
        position: absolute; top: -80px; right: -80px;
        width: 300px; height: 300px; border-radius: 50%;
        background: rgba(124,58,237,0.15);
    }
    .promo-eyebrow { font-size: 12px; letter-spacing: 4px; color: var(--primary); font-weight: 700; text-transform: uppercase; margin-bottom: 10px; }
    .promo-title { font-size: 48px; font-weight: 800; color: #FFF; text-transform: uppercase; letter-spacing: -1px; line-height: 1.1; }
    .promo-title span { color: var(--primary); }
    .promo-desc { color: rgba(255,255,255,0.6); font-size: 15px; margin: 15px 0 30px; }

    /* ── NEWSLETTER ── */
    .newsletter-section { background: #000; color: #FFF; padding: 100px 0; text-align: center; }
    .newsletter-title { font-size: 38px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 15px; }
    .newsletter-group {
        max-width: 550px; margin: 35px auto 0;
        display: flex; border-bottom: 1px solid rgba(255,255,255,0.3);
    }
    .newsletter-input {
        background: transparent; border: none; color: #FFF; flex:1;
        padding: 14px 0; font-size: 15px; outline: none;
    }
    .newsletter-input::placeholder { color: rgba(255,255,255,0.4); }
    .btn-nl { background: transparent; color: #FFF; border: none; font-weight: 700; text-transform: uppercase; letter-spacing: 2px; padding: 0 20px; cursor: pointer; }
</style>
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
            <a href="{{ route('products.index') }}" class="btn-st-dark px-5 py-3" style="display:inline-block;">XEM NGAY</a>
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
                                    <form action="{{ route('cart.add') }}" method="POST" style="width:100%;">
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
        <div class="promo-title" style="font-size:36px; color:var(--primary);">GIẢM ĐẾN 50%</div>
        <p class="promo-desc">Chương trình khuyến mãi có thời hạn — Đừng bỏ lỡ!</p>
        <a href="{{ route('products.index') }}" class="btn-st-dark px-5 py-3 bg-white text-dark" style="display:inline-block;">MUA NGAY</a>
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
                                    <form action="{{ route('cart.add') }}" method="POST" style="width:100%;">
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
        <p style="font-size:16px; color:rgba(255,255,255,0.5); letter-spacing:1px;">Nhận ưu đãi 20% cho đơn hàng đầu tiên của bạn</p>
        <div class="newsletter-group">
            <input type="email" class="newsletter-input" placeholder="Địa chỉ email của bạn">
            <button class="btn-nl">Đăng ký</button>
        </div>
    </div>
</section>

@endsection
