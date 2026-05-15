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

        /* Star Rating Interactive */
        .star-rating-interactive { display: flex; gap: 6px; }
        .star-btn {
            background: none; border: none; padding: 0;
            font-size: 28px; color: #DDD; cursor: pointer;
            transition: color 0.15s, transform 0.15s;
            line-height: 1;
        }
        .star-btn.active { color: var(--primary); }
        .star-btn:hover  { transform: scale(1.15); }
        .star-label { font-size: 13px; color: var(--text-light); min-height: 18px; }

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
                    {{-- Notification Bell --}}
                    <div class="dropdown" id="notifDropdownWrap">
                        <a href="#" class="position-relative" data-bs-toggle="dropdown" data-bs-auto-close="outside" style="color:var(--text-main);" id="notifToggle">
                            <i class="fas fa-bell" style="font-size:18px;"></i>
                            <span class="cart-badge d-none" id="notif-badge">0</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end shadow border-0 p-0" id="notifDropdown" style="border-radius:0; min-width:340px;">
                            <div style="max-height:460px; display:flex; flex-direction:column;">
                                {{-- Header --}}
                                <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom flex-shrink-0">
                                    <span style="font-weight:700; font-size:13px; text-transform:uppercase; letter-spacing:1px;">Thông báo</span>
                                    <a href="#" id="markAllRead" style="font-size:12px; color:#888; display:none;">Đánh dấu đã đọc</a>
                                </div>
                                {{-- List --}}
                                <div id="notifList" style="overflow-y:auto; flex:1;">
                                    <div class="text-center py-4 text-muted" id="notifEmpty" style="font-size:13px;">
                                        <i class="far fa-bell fa-2x mb-2 d-block" style="color:#ddd;"></i>
                                        Không có thông báo
                                    </div>
                                </div>
                                {{-- Footer --}}
                                <a href="{{ route('notifications.index') }}"
                                   class="d-block text-center py-2 border-top flex-shrink-0"
                                   style="font-size:12px; font-weight:700; color:var(--primary); text-transform:uppercase; letter-spacing:1px; text-decoration:none; background:#FAFAFA;">
                                    Xem tất cả thông báo
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- User Menu --}}
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
        <div class="toast align-items-center text-bg-success border-0" role="alert">
            <div class="d-flex">
                <div class="toast-body">{{ session('success') }}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    @endif
    @if(session('error'))
        <div class="toast align-items-center text-bg-danger border-0" role="alert">
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
    const CSRF = document.querySelector('meta[name="csrf-token"]').content;

    /* ── TOAST ── */
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

    // Auto-hide session toasts
    document.querySelectorAll('.toast.show').forEach(function(el) {
        const t = new bootstrap.Toast(el, { delay: 4000, autohide: true });
        t.show();
        el.addEventListener('hidden.bs.toast', () => el.remove());
    });

    /* ── AJAX ADD TO CART ── */
    document.addEventListener('submit', function(e) {
        const form = e.target;
        if (form.action && form.action.includes('cart/add')) {
            e.preventDefault();
            const formData = new FormData(form);
            const btn = form.querySelector('button[type="submit"]');
            const orig = btn ? btn.innerHTML : '';
            if (btn) { btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>'; }
            fetch(form.action, {
                method: 'POST', body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': CSRF }
            })
            .then(r => r.json())
            .then(data => {
                showToast(data.message || (data.success ? 'Đã thêm vào giỏ!' : 'Có lỗi!'), data.success ? 'success' : 'danger');
                if (data.success) {
                    const badge = document.getElementById('cart-badge-count');
                    if (badge) { badge.innerText = data.cart_count; badge.classList.remove('d-none'); }
                }
            })
            .catch(() => showToast('Không thể kết nối máy chủ!', 'danger'))
            .finally(() => { if (btn) { btn.disabled = false; btn.innerHTML = orig; } });
        }
    });

    @auth
    /* ── NOTIFICATION SYSTEM ── */
    const NOTIF_ICONS = {
        'confirmed':  'check-circle',
        'shipping':   'shipping-fast',
        'completed':  'box-open',
        'cancelled':  'times-circle',
        'pending':    'clock',
        'paid':       'check-circle',
        'unpaid':     'times-circle',
    };
    const NOTIF_COLORS = {
        'confirmed': '#27AE60',
        'shipping':  '#7C3AED',
        'completed': '#7C3AED',
        'cancelled': '#E53E3E',
        'pending':   '#F5A623',
        'paid':      '#27AE60',
        'unpaid':    '#888',
    };

    let lastNotifCount = -1;

    function buildNotifItem(n) {
        const data    = n.data || {};
        const isRead  = !!n.read_at;
        const status  = data.status || data.type || '';
        const icon    = NOTIF_ICONS[status] || 'bell';
        const color   = NOTIF_COLORS[status] || '#7C3AED';
        const bg      = isRead ? '#fff' : '#FFF9F0';
        const border  = isRead ? '' : 'border-left:3px solid #7C3AED;';

        return `<a href="${data.url || '#'}"
                   class="d-flex align-items-start gap-2 px-3 py-2 border-bottom notif-item text-decoration-none"
                   style="background:${bg}; ${border}"
                   data-id="${n.id}">
            <div class="flex-shrink-0 mt-1">
                <i class="fas fa-${icon}" style="color:${color}; font-size:16px;"></i>
            </div>
            <div class="flex-grow-1">
                <div style="font-weight:${isRead ? '500' : '700'}; font-size:13px; color:#1A1A1A;">${data.title || 'Thông báo'}</div>
                <div style="font-size:12px; color:${isRead ? '#999' : '#555'}; line-height:1.4;">${data.message || ''}</div>
                <div style="font-size:11px; color:#BBB; margin-top:3px;">${n.time || ''}</div>
            </div>
            ${!isRead ? '<div style="width:8px;height:8px;background:#7C3AED;border-radius:50%;flex-shrink:0;margin-top:6px;"></div>' : ''}
        </a>`;
    }

    function renderNotifications(notifications, unreadCount) {
        const list  = document.getElementById('notifList');
        const badge = document.getElementById('notif-badge');
        const markAllBtn = document.getElementById('markAllRead');
        const empty = document.getElementById('notifEmpty');

        // Badge
        if (badge) {
            if (unreadCount > 0) {
                badge.textContent = unreadCount > 9 ? '9+' : unreadCount;
                badge.classList.remove('d-none');
            } else {
                badge.classList.add('d-none');
            }
        }

        // Mark all button
        if (markAllBtn) {
            markAllBtn.style.display = unreadCount > 0 ? 'inline' : 'none';
        }

        if (!notifications || notifications.length === 0) {
            list.innerHTML = `<div class="text-center py-4 text-muted" style="font-size:13px;">
                <i class="far fa-bell fa-2x mb-2 d-block" style="color:#ddd;"></i>Không có thông báo
            </div>`;
            return;
        }

        list.innerHTML = notifications.map(buildNotifItem).join('');

        // Bind click → mark as read
        list.querySelectorAll('.notif-item').forEach(el => {
            el.addEventListener('click', function(e) {
                const id = this.dataset.id;
                if (!id) return;
                fetch(`/notifications/${id}/read`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest' }
                });
                // Visually mark as read immediately
                this.style.background = '#fff';
                this.style.borderLeft = '';
                const dot = this.querySelector('[style*="border-radius:50%"]');
                if (dot) dot.remove();
                const title = this.querySelector('[style*="font-weight"]');
                if (title) title.style.fontWeight = '500';
            });
        });
    }

    async function fetchNotifications() {
        try {
            const res  = await fetch('/notifications/fetch', {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            });
            if (!res.ok) return;
            const json = await res.json();

            // Show toast for new notifications
            if (lastNotifCount >= 0 && json.unread_count > lastNotifCount) {
                const newest = json.notifications.find(n => !n.read_at);
                if (newest) {
                    showToast((newest.data.title || 'Thông báo mới') + ': ' + (newest.data.message || ''), 'success');
                }
            }
            lastNotifCount = json.unread_count;

            renderNotifications(json.notifications, json.unread_count);
        } catch {}
    }

    // Initial fetch + poll every 30s
    fetchNotifications();
    setInterval(fetchNotifications, 30000);

    // Mark all read
    document.getElementById('markAllRead')?.addEventListener('click', function(e) {
        e.preventDefault();
        fetch('/notifications/read-all', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest' }
        }).then(() => {
            lastNotifCount = 0;
            fetchNotifications();
        });
    });

    // Refresh on dropdown open
    document.getElementById('notifToggle')?.addEventListener('click', function() {
        fetchNotifications();
    });
    @endauth
});
</script>
    @auth
    <!-- Global Review Modal (Dual Mode: Add & View) -->
    <div class="modal fade" id="globalReviewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content" style="border-radius:20px; border:none; box-shadow:0 25px 50px -12px rgba(0,0,0,0.5);">
                <div class="modal-body p-0">
                    <div class="row g-0">
                        <!-- Left: Product Info -->
                        <div class="col-md-5 d-none d-md-block" style="background:#F9F9F9; border-radius:20px; solid #EEE;">
                            <div class="p-4 text-center h-100 d-flex flex-column justify-content-center">
                                <img id="reviewModalProductImg" src="" class="img-fluid mb-3 mx-auto" style="max-height:200px; object-fit:contain;" alt="Product">
                                <h5 id="reviewModalProductName" style="font-weight:700; margin-bottom:10px; color:#111;"></h5>
                                <div id="reviewModalProductPrice" style="color:#666; font-size:16px;"></div>
                            </div>
                        </div>
                        
                        <!-- Right: Review Form / View -->
                        <div class="col-md-7">
                            <div class="p-4 p-md-5">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h4 id="reviewModalTitle" style="font-weight:900; letter-spacing:1px; margin:0;">Đánh giá sản phẩm</h4>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>

                                <!-- Form for Adding Review -->
                                <form id="addReviewForm" action="{{ route('reviews.store') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="product_id" id="reviewModalProductId">
                                    
                                    <div class="mb-4 text-center">
                                        <label class="d-block mb-2 text-muted small fw-bold">Đánh giá chất lượng</label>
                                        <div class="star-rating-input d-flex justify-content-center gap-2">
                                            @for($i=5; $i>=1; $i--)
                                                <input type="radio" name="rating" id="star{{ $i }}" value="{{ $i }}" {{ $i==5?'checked':'' }}>
                                                <label for="star{{ $i }}"><i class="fas fa-star"></i></label>
                                            @endfor
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        <textarea name="comment" class="form-control" rows="4" placeholder="Chia sẻ cảm nhận của bạn về sản phẩm..." style="border-radius:20px; border:1px solid #DDD; padding:15px; font-size:14px;"></textarea>
                                    </div>

                                    <div class="mb-4" style="border-radius:20px;">
                                        <label class="d-block mb-2 text-muted small fw-bold text-uppercase" " >Hình ảnh & Video</label>
                                        <div class="row g-2">
                                            <div class="col-6">
                                                <div class="upload-btn-wrapper w-100">
                                                    <button class="btn btn-outline-secondary w-100 py-2" type="button" style="font-size:12px; border-style:dashed;">
                                                        <i class="fas fa-camera me-2"></i>Thêm ảnh (Max 10)
                                                    </button>
                                                    <input type="file" name="images[]" multiple accept="image/*" id="reviewImagesInput">
                                                </div>
                                            </div>
                                            <div class="col-6" style="border-radius:20px;">
                                                <div class="upload-btn-wrapper w-100">
                                                    <button class="btn btn-outline-secondary w-100 py-2" type="button" style="font-size:12px; border-style:dashed;">
                                                        <i class="fas fa-video me-2"></i>Thêm Video
                                                    </button>
                                                    <input type="file" name="video" accept="video/*" id="reviewVideoInput">
                                                </div>
                                            </div>
                                        </div>
                                        <div id="mediaPreviews" class="mt-3 d-flex flex-wrap gap-2"></div>
                                    </div>

                                    <button type="submit" class="btn btn-dark w-100 py-3" style="border-radius:20px; font-weight:700; text-transform:uppercase; letter-spacing:1px;">Gửi đánh giá</button>
                                </form>

                                <!-- View Review Details -->
                                <div id="viewReviewContent" class="d-none">
                                    <div class="mb-4 text-center">
                                        <div id="viewRatingStars" class="star-rating fs-4" style="color: #7C3AED;"></div>
                                        <div id="viewReviewDate" class="text-muted small mt-1"></div>
                                    </div>
                                    
                                    <div id="viewReviewComment" class="mb-4 p-3 border-start border-4 border-dark italic" style="background:#F9F9F9; font-style:italic; color:#444;"></div>

                                    <div id="viewReviewMedia" class="mb-4">
                                        <div id="viewReviewImages" class="d-flex flex-wrap gap-2 mb-3"></div>
                                        <div id="viewReviewVideo"></div>
                                    </div>

                                    <div id="reviewActionsView" class="d-none">
                                        <form id="deleteReviewForm" action="" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa đánh giá này?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger w-100 py-3" style="border-radius:20px; font-weight:700; text-transform:uppercase; letter-spacing:1px; font-size:13px;">
                                                <i class="fas fa-trash-alt me-2"></i>XÓA ĐÁNH GIÁ
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .star-rating-input { display: flex; flex-direction: row-reverse; }
        .star-rating-input input { display: none; }
        .star-rating-input label { font-size: 30px; color: #DDD; cursor: pointer; transition: color 0.2s; }
        .star-rating-input input:checked ~ label,
        .star-rating-input label:hover,
        .star-rating-input label:hover ~ label { color: #7C3AED; }
        
        .upload-btn-wrapper { position: relative; overflow: hidden; display: inline-block; }
        .upload-btn-wrapper input[type=file] { font-size: 100px; position: absolute; left: 0; top: 0; opacity: 0; cursor: pointer; }
        
        .preview-item { width: 60px; height: 60px; object-fit: cover; border: 1px solid #EEE; position: relative; }
        .preview-remove { position: absolute; top: -5px; right: -5px; background: red; color: white; border-radius: 50%; width: 15px; height: 15px; font-size: 10px; display: flex; align-items: center; justify-content: center; cursor: pointer; }
    </style>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('globalReviewModal');
        if (!modal) return;

        const addForm = document.getElementById('addReviewForm');
        const viewContent = document.getElementById('viewReviewContent');
        const reviewActionsView = document.getElementById('reviewActionsView');
        const deleteForm = document.getElementById('deleteReviewForm');

        modal.addEventListener('show.bs.modal', function(event) {
            const btn = event.relatedTarget;
            const productId = btn.getAttribute('data-product-id');
            const productName = btn.getAttribute('data-product-name');
            const productImg = btn.getAttribute('data-product-image');
            const productPrice = btn.getAttribute('data-product-price');
            const reviewData = btn.getAttribute('data-review');

            // Set static data
            document.getElementById('reviewModalProductId').value = productId;
            document.getElementById('reviewModalProductName').textContent = productName;
            document.getElementById('reviewModalProductImg').src = productImg;
            document.getElementById('reviewModalProductPrice').textContent = productPrice;

            if (reviewData) {
                // VIEW MODE
                const review = JSON.parse(reviewData);
                document.getElementById('reviewModalTitle').textContent = 'ĐÁNH GIÁ CỦA BẠN';
                addForm.classList.add('d-none');
                viewContent.classList.remove('d-none');
                
                // Set rating
                let stars = '';
                for(let i=1; i<=5; i++) stars += `<i class="${i <= review.rating ? 'fas' : 'far'} fa-star"></i> `;
                document.getElementById('viewRatingStars').innerHTML = stars;
                document.getElementById('viewReviewDate').textContent = 'Đã đánh giá vào ' + new Date(review.created_at).toLocaleDateString('vi-VN');
                document.getElementById('viewReviewComment').textContent = review.comment || 'Không có nhận xét';

                // Media
                const imgContainer = document.getElementById('viewReviewImages');
                imgContainer.innerHTML = '';
                if (review.images && review.images.length > 0) {
                    review.images.forEach(img => {
                        imgContainer.innerHTML += `<img src="/storage/${img}" class="img-thumbnail" style="width:100px; height:100px; object-fit:cover; cursor:pointer;" onclick="window.open(this.src)">`;
                    });
                }

                const videoContainer = document.getElementById('viewReviewVideo');
                videoContainer.innerHTML = '';
                if (review.video) {
                    videoContainer.innerHTML = `
                        <video controls class="w-100" style="max-height:300px; background:#000;">
                            <source src="/storage/${review.video}" type="video/mp4">
                        </video>`;
                }

                // Delete Button
                reviewActionsView.classList.remove('d-none');
                deleteForm.action = `/reviews/${review.id}`;

            } else {
                // ADD MODE
                document.getElementById('reviewModalTitle').textContent = 'ĐÁNH GIÁ SẢN PHẨM';
                addForm.classList.remove('d-none');
                viewContent.classList.add('d-none');
                reviewActionsView.classList.add('d-none');
                addForm.reset();
                document.getElementById('mediaPreviews').innerHTML = '';
            }
        });

        // Media Preview Logic
        document.getElementById('reviewImagesInput').addEventListener('change', function(e) {
            const container = document.getElementById('mediaPreviews');
            Array.from(e.target.files).forEach(file => {
                const reader = new FileReader();
                reader.onload = function(ex) {
                    const div = document.createElement('div');
                    div.className = 'position-relative';
                    div.innerHTML = `<img src="${ex.target.result}" class="preview-item">`;
                    container.appendChild(div);
                };
                reader.readAsDataURL(file);
            });
        });
        
        document.getElementById('reviewVideoInput').addEventListener('change', function(e) {
            if(e.target.files[0]) {
                const container = document.getElementById('mediaPreviews');
                const div = document.createElement('div');
                div.className = 'position-relative d-flex align-items-center justify-content-center border';
                div.style = 'width:60px; height:60px; background:#EEE;';
                div.innerHTML = `<i class="fas fa-video text-muted"></i>`;
                container.appendChild(div);
                
                // Simple validation for video length (optional but good)
                const video = document.createElement('video');
                video.preload = 'metadata';
                video.onloadedmetadata = function() {
                    window.URL.revokeObjectURL(video.src);
                    if (video.duration > 30) {
                        alert("Video không được dài quá 30 giây!");
                        e.target.value = '';
                        div.remove();
                    }
                }
                video.src = URL.createObjectURL(e.target.files[0]);
            }
        });
    });
    </script>
    @endauth

    @stack('scripts')
</body>
</html>
