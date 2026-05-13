<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sàn Tím Vi En - Phong cách Việt')</title>
    <meta name="description" content="Sàn Tím Vi En - Shop thời trang Việt chất lượng cao. Phong cách Việt — Sống đẹp mỗi ngày.">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        /* =========================================
           SÀN TÍM VI EN — DESIGN SYSTEM
           ========================================= */
        :root {
            --primary: #7C3AED;       /* Tím Vi En */
            --primary-light: #EDE9FE;
            --text-main: #1A1A1A;
            --text-light: #888888;
            --bg-main: #FFFFFF;
            --bg-gray: #F7F7F7;
            --border: #EEEEEE;
        }

        * { box-sizing: border-box; }

        body {
            background-color: var(--bg-main);
            color: var(--text-main);
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        a { text-decoration: none; color: inherit; }
        a:hover { color: var(--text-light); }

        /* ── TOP BAR ── */
        .top-bar {
            background: var(--text-main);
            color: #FFF;
            padding: 8px 0;
            font-size: 12px;
            font-weight: 500;
            letter-spacing: 0.5px;
        }
        .top-bar a { color: rgba(255,255,255,0.8); }
        .top-bar a:hover { color: #FFF; }

        /* ── NAVBAR ── */
        .san-tim-navbar {
            background: var(--bg-main);
            padding: 18px 0;
            border-bottom: 1px solid var(--border);
            position: sticky;
            top: 0;
            z-index: 1030;
        }

        .brand-logo {
            font-weight: 800;
            font-size: 22px;
            color: #7C3AED;
            letter-spacing: 1px;
            line-height: 1;
        }
        .brand-logo span { color: var(--primary); }

        .nav-menu {
            display: flex;
            gap: 28px;
            list-style: none;
            margin: 0;
            padding: 0;
        }
        .nav-menu li a {
            font-weight: 500;
            font-size: 14px;
            color: var(--text-main);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: color 0.2s;
        }
        .nav-menu li a:hover { color: var(--primary); }
        .nav-menu li a.sale-link { color: #E53E3E; font-weight: 700; }

        /* ── SEARCH BAR ── */
        .search-bar {
            background: var(--bg-gray);
            border-radius: 30px;
            padding: 9px 18px;
            display: flex;
            align-items: center;
            width: 260px;
            border: 1px solid transparent;
            transition: border-color 0.2s;
        }
        .search-bar:focus-within { border-color: var(--primary); }
        .search-bar input {
            border: none;
            background: transparent;
            width: 100%;
            outline: none;
            font-size: 13px;
        }
        .search-bar button { background: none; border: none; padding: 0; color: var(--text-light); }

        /* ── ACTION ICONS ── */
        .action-icons { display: flex; gap: 18px; align-items: center; }
        .action-icons i { font-size: 18px; color: var(--text-main); }

        /* ── CART BADGE ── */
        .cart-badge {
            position: absolute;
            top: -8px; right: -8px;
            background: var(--primary);
            color: #FFF;
            font-size: 10px;
            font-weight: 700;
            width: 18px; height: 18px;
            display: flex; align-items: center; justify-content: center;
            border-radius: 50%;
        }

        /* ── BUTTONS ── */
        .btn-st {
            background: var(--bg-main);
            border: 1px solid var(--text-main);
            color: var(--text-main);
            padding: 12px 30px;
            font-weight: 700;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-radius: 0;
            transition: all 0.3s ease;
        }
        .btn-st:hover { background: var(--text-main); color: var(--bg-main); }

        .btn-st-dark {
            background: var(--text-main);
            border: 1px solid var(--text-main);
            color: var(--bg-main);
            padding: 12px 30px;
            font-weight: 700;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-radius: 0;
            transition: all 0.3s ease;
        }
        .btn-st-dark:hover { background: var(--bg-main); color: var(--text-main); }

        /* ── CARDS ── */
        .st-card { border: none; border-radius: 0; box-shadow: none; background: var(--bg-main); }

        /* ── FORMS ── */
        .form-control:focus { border-color: var(--primary); box-shadow: 0 0 0 3px var(--primary-light); }

        /* ── FOOTER ── */
        .st-footer {
            background: var(--text-main);
            color: rgba(255,255,255,0.85);
            padding: 60px 0 30px;
            margin-top: auto;
        }
        .footer-brand { font-weight: 800; font-size: 20px; color: #FFF; letter-spacing: 1px; }
        .footer-brand span { color: var(--primary); }
        .footer-title {
            font-weight: 700;
            font-size: 13px;
            margin-bottom: 18px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #FFF;
        }
        .footer-links { list-style: none; padding: 0; margin: 0; }
        .footer-links li { margin-bottom: 10px; }
        .footer-links li a {
            color: rgba(255,255,255,0.6);
            font-size: 14px;
            transition: color 0.2s;
        }
        .footer-links li a:hover { color: #FFF; }
        .footer-divider { border-color: rgba(255,255,255,0.1); margin: 30px 0; }
        .footer-bottom { font-size: 12px; color: rgba(255,255,255,0.4); }

        .social-btn {
            width: 36px; height: 36px;
            border: 1px solid rgba(255,255,255,0.2);
            display: inline-flex; align-items: center; justify-content: center;
            color: rgba(255,255,255,0.6);
            font-size: 14px;
            transition: all 0.2s;
        }
        .social-btn:hover { background: var(--primary); border-color: var(--primary); color: #FFF; }

        /* ── NEWSLETTER INLINE (footer) ── */
        .newsletter-inline { display: flex; gap: 0; max-width: 300px; }
        .newsletter-inline input {
            border: 1px solid rgba(255,255,255,0.2);
            background: rgba(255,255,255,0.05);
            color: #FFF;
            padding: 10px 15px;
            font-size: 13px;
            outline: none;
            flex: 1;
        }
        .newsletter-inline input::placeholder { color: rgba(255,255,255,0.4); }
        .newsletter-inline button {
            background: var(--primary);
            border: none;
            color: #FFF;
            padding: 10px 16px;
            font-weight: 700;
            font-size: 12px;
            text-transform: uppercase;
            cursor: pointer;
            transition: background 0.2s;
        }
        .newsletter-inline button:hover { background: #6D28D9; }
    </style>
    @stack('styles')
</head>
<body>

<!-- ── TOP BAR ── -->
<div class="top-bar d-none d-md-block">
    <div class="container d-flex justify-content-between align-items-center">
        <div class="d-flex gap-4">
            <span><i class="fas fa-phone me-1"></i> 1800 2345 (Miễn phí)</span>
            <span><i class="fas fa-envelope me-1"></i> hotro@santimvien.vn</span>
        </div>
        <div class="d-flex gap-4">
            <a href="{{ route('pages.about') }}">Về chúng tôi</a>
            <a href="{{ route('pages.blog') }}">Blog</a>
            <a href="{{ route('pages.contact') }}">Liên hệ</a>
            {{-- Download app — ẩn cho đến khi có app --}}
            {{-- <a href="#">Tải ứng dụng</a> --}}
        </div>
    </div>
</div>

<!-- ── MAIN NAVBAR ── -->
<header class="san-tim-navbar">
    <div class="container d-flex justify-content-between align-items-center">
        <!-- Logo -->
        <a href="{{ route('home') }}" class="brand-logo">
            SÀN TÍM
        </a>

        <!-- Desktop Menu -->
        <ul class="nav-menu d-none d-lg-flex">
            <li><a href="{{ route('home') }}">Trang chủ</a></li>
            <li><a href="{{ route('products.index', ['loai_filter' => 'men']) }}">Nam</a></li>
            <li><a href="{{ route('products.index', ['loai_filter' => 'women']) }}">Nữ</a></li>
            <li><a href="{{ route('products.index', ['loai_filter' => 'kids']) }}">Trẻ em</a></li>
            <li><a href="{{ route('pages.blog') }}">Blog</a></li>
            <li><a href="{{ route('pages.about') }}">Về chúng tôi</a></li>
            <li><a href="{{ route('products.index') }}" class="sale-link">KHUYẾN MÃI</a></li>
        </ul>

        <!-- Mobile Toggle -->
        <button class="btn d-lg-none p-0" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileMenu" style="font-size:20px; color:var(--text-main);">
            <i class="fas fa-bars"></i>
        </button>

        <!-- Right Actions -->
        <div class="d-flex align-items-center gap-3">
            <div class="search-bar d-none d-md-flex">
                <form action="{{ route('products.index') }}" method="GET" class="d-flex w-100 align-items-center">
                    <input type="text" name="search" placeholder="Tìm sản phẩm..." value="{{ request('search') }}">
                    <button type="submit"><i class="fas fa-search" style="font-size:13px;"></i></button>
                </form>
            </div>

            <div class="action-icons">
                @auth
                    <div class="dropdown">
                        <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown" style="color:var(--text-main); text-decoration:none;">
                            <i class="far fa-user"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0" style="border-radius:0; min-width:200px;">
                            @if(auth()->user()->role === 'admin')
                                <li><a class="dropdown-item py-2 fw-bold" href="{{ route('admin.dashboard') }}"><i class="fas fa-cog me-2"></i>Quản trị</a></li>
                                <li><hr class="dropdown-divider"></li>
                            @endif
                            <li><a class="dropdown-item py-2" href="{{ route('profile.index') }}"><i class="far fa-user me-2"></i>Tài khoản</a></li>
                            <li><a class="dropdown-item py-2" href="{{ route('orders.index') }}"><i class="fas fa-box me-2"></i>Đơn mua</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger py-2"><i class="fas fa-sign-out-alt me-2"></i>Đăng xuất</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @else
                    <a href="{{ route('login') }}" style="color:var(--text-main);"><i class="far fa-user"></i></a>
                @endauth

                <a href="{{ route('cart.index') }}" class="position-relative" style="color:var(--text-main);">
                    <i class="fas fa-shopping-bag"></i>
                    @php $cartCount = \App\Http\Controllers\CartController::cartCount(); @endphp
                    @if($cartCount > 0)
                        <span id="cart-badge-count" class="cart-badge">{{ $cartCount }}</span>
                    @else
                        <span id="cart-badge-count" class="cart-badge d-none">0</span>
                    @endif
                </a>
            </div>
        </div>
    </div>
</header>

<!-- ── MOBILE MENU ── -->
<div class="offcanvas offcanvas-start" tabindex="-1" id="mobileMenu" style="width:280px;">
    <div class="offcanvas-header border-bottom">
        <span class="brand-logo">SÀN <span>TÍM</span></span>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body p-0">
        <div class="list-group list-group-flush">
            <a href="{{ route('home') }}" class="list-group-item list-group-item-action py-3 border-0 fw-600">TRANG CHỦ</a>
            <a href="{{ route('products.index', ['loai_filter' => 'men']) }}" class="list-group-item list-group-item-action py-3 border-0">NAM</a>
            <a href="{{ route('products.index', ['loai_filter' => 'women']) }}" class="list-group-item list-group-item-action py-3 border-0">NỮ</a>
            <a href="{{ route('products.index', ['loai_filter' => 'kids']) }}" class="list-group-item list-group-item-action py-3 border-0">TRẺ EM</a>
            <a href="{{ route('pages.blog') }}" class="list-group-item list-group-item-action py-3 border-0">BLOG</a>
            <a href="{{ route('pages.about') }}" class="list-group-item list-group-item-action py-3 border-0">VỀ CHÚNG TÔI</a>
            <a href="{{ route('products.index') }}" class="list-group-item list-group-item-action py-3 border-0 text-danger fw-bold">KHUYẾN MÃI</a>
        </div>
        <div class="p-3 mt-3">
            <form action="{{ route('products.index') }}" method="GET" class="d-flex align-items-center bg-light p-2 rounded-pill">
                <input type="text" name="search" placeholder="Tìm kiếm..." style="border:none;background:transparent;width:100%;outline:none;padding-left:10px;font-size:14px;">
                <button type="submit" style="background:none;border:none;"><i class="fas fa-search text-muted"></i></button>
            </form>
        </div>
    </div>
</div>

<!-- ── MAIN CONTENT ── -->
<main>
    @yield('content')
</main>

<!-- ── FOOTER ── -->
<footer class="st-footer">
    <div class="container">
        <div class="row g-4">
            <!-- Brand -->
            <div class="col-lg-3 col-md-6">
                <div class="footer-brand mb-3">SÀN <span>TÍM</span> VI EN</div>
                <p style="font-size:13px; color:rgba(255,255,255,0.5); line-height:1.7;">
                    Phong cách Việt — Sống đẹp mỗi ngày.<br>
                    Lorem Ipsum, 235 Simply, Quận 1, TP.HCM
                </p>
                <p style="font-size:13px; color:rgba(255,255,255,0.5);">
                    santimvien@gmail.com<br>
                    +84 (28) 3822-4242
                </p>
                <div class="d-flex gap-2 mt-3">
                    <a href="#" class="social-btn"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="social-btn"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="social-btn"><i class="fab fa-tiktok"></i></a>
                    <a href="#" class="social-btn"><i class="fab fa-youtube"></i></a>
                </div>
            </div>

            <!-- Thông tin -->
            <div class="col-lg-2 col-md-3 col-6">
                <div class="footer-title">Thông tin</div>
                <ul class="footer-links">
                    <li><a href="{{ route('products.index', ['loai_filter' => 'men']) }}">Nam</a></li>
                    <li><a href="{{ route('products.index', ['loai_filter' => 'women']) }}">Nữ</a></li>
                    <li><a href="{{ route('products.index', ['loai_filter' => 'kids']) }}">Trẻ em</a></li>
                    <li><a href="{{ route('products.index') }}">Hàng mới về</a></li>
                </ul>
            </div>

            <!-- Khám phá -->
            <div class="col-lg-2 col-md-3 col-6">
                <div class="footer-title">Khám phá</div>
                <ul class="footer-links">
                    <li><a href="{{ route('pages.blog') }}">Blog</a></li>
                    <li><a href="{{ route('cart.index') }}">Giỏ hàng</a></li>
                    <li><a href="{{ route('pages.about') }}">Về chúng tôi</a></li>
                    {{-- Đánh giá — ẩn khi chưa có trang riêng --}}
                    {{-- <li><a href="#">Đánh giá</a></li> --}}
                </ul>
            </div>

            <!-- Liên hệ -->
            <div class="col-lg-2 col-md-6 col-6">
                <div class="footer-title">Liên hệ</div>
                <ul class="footer-links">
                    <li><a href="#">FAQ</a></li>
                    <li><a href="{{ route('orders.index') }}">Theo dõi đơn hàng</a></li>
                    <li><a href="#">Vận chuyển</a></li>
                    <li><a href="#">Đổi trả</a></li>
                </ul>
            </div>

            <!-- Hỗ trợ + Newsletter -->
            <div class="col-lg-3 col-md-6 col-6">
                <div class="footer-title">Đăng ký nhận tin</div>
                <p style="font-size:13px; color:rgba(255,255,255,0.5); margin-bottom:15px;">Nhận ưu đãi & cập nhật mới nhất</p>
                <div class="newsletter-inline">
                    <input type="email" placeholder="Email của bạn">
                    <button type="button">GỬI</button>
                </div>
                <div class="mt-4">
                    <div class="footer-title">Hỗ trợ</div>
                    <ul class="footer-links">
                        <li><a href="#">Trung tâm hỗ trợ</a></li>
                        <li><a href="{{ route('pages.contact') }}">Liên hệ</a></li>
                        <li><a href="#">Tuyển dụng</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <hr class="footer-divider">
        <div class="d-flex flex-wrap justify-content-between align-items-center footer-bottom">
            <span>© 2026 Sàn Tím Vi En. All Rights Reserved.</span>
            <div class="d-flex gap-3 mt-2 mt-md-0">
                <a href="#" style="color:rgba(255,255,255,0.4);">Chính sách bảo mật</a>
                <a href="#" style="color:rgba(255,255,255,0.4);">Điều khoản & Điều kiện</a>
            </div>
        </div>
    </div>
</footer>

<!-- ── TOAST NOTIFICATIONS ── -->
<div id="toast-container" class="position-fixed bottom-0 end-0 p-3" style="z-index:1060;">
    @if(session('success'))
        <div class="toast align-items-center text-bg-success border-0 show" role="alert">
            <div class="d-flex">
                <div class="toast-body">{{ session('success') }}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    @endif
    @if(session('error'))
        <div class="toast align-items-center text-bg-danger border-0 show" role="alert">
            <div class="d-flex">
                <div class="toast-body">{{ session('error') }}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    @endif
</div>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-hide toasts
    setTimeout(function() {
        document.querySelectorAll('.toast.show').forEach(function(t) {
            new bootstrap.Toast(t).hide();
        });
    }, 4000);

    // AJAX Add to Cart
    document.addEventListener('submit', function(e) {
        const form = e.target;
        if (form.action && form.action.includes('cart/add')) {
            e.preventDefault();
            const formData = new FormData(form);
            const btn = form.querySelector('button[type="submit"]');
            const orig = btn ? btn.innerHTML : '';
            if (btn) { btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>'; }

            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(r => r.json())
            .then(data => {
                showToast(data.message || (data.success ? 'Đã thêm vào giỏ!' : 'Có lỗi xảy ra!'), data.success ? 'success' : 'danger');
                if (data.success) {
                    const badge = document.getElementById('cart-badge-count');
                    if (badge) { badge.innerText = data.cart_count; badge.classList.remove('d-none'); }
                }
            })
            .catch(() => showToast('Không thể kết nối máy chủ!', 'danger'))
            .finally(() => { if (btn) { btn.disabled = false; btn.innerHTML = orig; } });
        }
    });

    window.showToast = function(message, type = 'success') {
        const container = document.getElementById('toast-container');
        const el = document.createElement('div');
        el.className = `toast align-items-center text-bg-${type} border-0`;
        el.setAttribute('role', 'alert');
        el.innerHTML = `<div class="d-flex"><div class="toast-body">${message}</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div>`;
        container.appendChild(el);
        const t = new bootstrap.Toast(el, { delay: 4000 });
        t.show();
        el.addEventListener('hidden.bs.toast', () => el.remove());
    };
});
</script>
@stack('scripts')
</body>
</html>
