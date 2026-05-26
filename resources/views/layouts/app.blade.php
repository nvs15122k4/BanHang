<!DOCTYPE html>
<html lang="vi">
<head>
    @php
        $defaultSeoTitle = 'Sàn Tím Vi En - Thời trang Việt phong cách';
        $defaultSeoDescription = 'Sàn Tím Vi En mang đến thời trang Việt hiện đại, chất lượng, giúp bạn tự tin thể hiện phong cách riêng qua những bộ sưu tập tinh tế mỗi ngày.';
        $defaultSeoCanonical = 'https://santimvien.vn' . (request()->path() === '/' ? '/' : '/' . request()->path());
        $defaultSeoImage = 'https://res.cloudinary.com/dxvml3sji/image/upload/q_auto/f_auto/v1779381084/title.png';
    @endphp
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', $defaultSeoTitle)</title>
    <meta name="description" content="@yield('meta_description', $defaultSeoDescription)">
    <meta name="robots" content="@yield('robots', 'index, follow')">
    <link rel="canonical" href="@yield('canonical', $defaultSeoCanonical)">

    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:title" content="@yield('og_title', $defaultSeoTitle)">
    <meta property="og:description" content="@yield('og_description', $defaultSeoDescription)">
    <meta property="og:url" content="@yield('canonical', $defaultSeoCanonical)">
    <meta property="og:image" content="@yield('og_image', $defaultSeoImage)">
    <meta name="twitter:card" content="summary_large_image">
    @stack('head')
    <link rel="icon" type="image/png" href="https://res.cloudinary.com/dxvml3sji/image/upload/q_auto/f_auto/v1779381084/title.png">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['public/css/app.css', 'public/js/app.js'])
    @stack('styles')
    @vite(['public/css/extracted-inline.css'])
</head>
<body>

<!-- ── TOP BAR ── -->
<div class="top-bar d-none d-md-block">
    <div class="container d-flex justify-content-between align-items-center">
        <div class="d-flex gap-4">
            <a href="{{ route('support.purchase-guide') }}"><i class="fas fa-shopping-bag me-1"></i> Hướng dẫn mua hàng</a>
            <a href="{{ route('guides.size') }}"><i class="fas fa-ruler me-1"></i> Hướng dẫn chọn size</a>
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
            SÀN <span>TÍM</span> VI EN
        </a>
        <!-- Desktop Menu -->
        <ul class="nav-menu d-none d-lg-flex">
            <li><a href="{{ route('home') }}">Trang chủ</a></li>
            <li><a href="{{ route('products.index') }}" class="products-link">Sản phẩm</a></li>
            <li><a href="{{ route('promotions.index') }}" class="sale-link">Khuyến mãi</a></li>
            <li><a href="{{ route('pages.blog') }}">Blog</a></li>
            <li><a href="{{ route('guides.size') }}">Chọn size</a></li>
            <li><a href="{{ route('pages.about') }}">Về chúng tôi</a></li>
        </ul>

        <!-- Mobile Toggle -->
        <button class="btn d-lg-none p-0 uix-dafa9f4e6e" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileMenu">
            <i class="fas fa-bars"></i>
        </button>

        <!-- Right Actions -->
        <div class="d-flex align-items-center gap-3">
            <div class="search-bar d-none d-md-flex">
                <form action="{{ route('products.index') }}" method="GET" class="d-flex w-100 align-items-center">
                    <input type="text" name="search" placeholder="Tìm sản phẩm..." value="{{ request('search') }}">
                    <button type="submit"><i class="fas fa-search uix-9e6595fb01"></i></button>
                </form>
            </div>

            <div class="action-icons">
                @auth
                    {{-- Notification Bell --}}
                    <div class="dropdown" id="notifDropdownWrap">
                        <a href="#" class="position-relative uix-9cd4420129" data-bs-toggle="dropdown" data-bs-auto-close="outside" id="notifToggle">
                            <i class="fas fa-bell uix-4f1925a8a6"></i>
                            <span class="cart-badge d-none" id="notif-badge">0</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end shadow border-0 p-0 uix-c9d6241e98" id="notifDropdown">
                            <div class="uix-daf1f03179">
                                {{-- Header --}}
                                <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom flex-shrink-0">
                                    <span class="uix-95ce444e5f">Thông báo</span>
                                    <button class="mark-all-read-button" type="button" id="markAllRead" title="Đánh dấu tất cả đã đọc" aria-label="Đánh dấu tất cả đã đọc">
                                        <i class="fas fa-check-double"></i>
                                        <span>Đánh dấu tất cả</span>
                                    </button>
                                </div>
                                {{-- List --}}
                                <div class="uix-441b1b7425" id="notifList">
                                    <div class="text-center py-4 text-muted uix-9e6595fb01" id="notifEmpty">
                                        <i class="far fa-bell fa-2x mb-2 d-block uix-30261166ec"></i>
                                        Không có thông báo
                                    </div>
                                </div>
                                {{-- Footer --}}
                                <a href="{{ route('notifications.index') }}"
                                   class="d-block text-center py-2 border-top flex-shrink-0 uix-6c2fda0f21"
                                  >
                                    Xem tất cả thông báo
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- Wishlist --}}
                    <a href="{{ route('wishlist.index') }}" class="position-relative mx-2 uix-9cd4420129" title="Yêu thích">
                        <i class="fas fa-heart uix-4f1925a8a6"></i>
                        @php $wishlistCount = auth()->user()->wishlists()->count(); @endphp
                        @if($wishlistCount > 0)
                            <span class="cart-badge">{{ $wishlistCount }}</span>
                        @else
                            <span class="cart-badge d-none">0</span>
                        @endif
                    </a>

                    <a href="{{ route('cart.index') }}" class="position-relative uix-9cd4420129">
                        <i class="fas fa-shopping-bag"></i>
                        @php $cartCount = \App\Http\Controllers\CartController::cartCount(); @endphp
                        @if($cartCount > 0)
                            <span id="cart-badge-count" class="cart-badge">{{ $cartCount }}</span>
                        @else
                            <span id="cart-badge-count" class="cart-badge d-none">0</span>
                        @endif
                    </a>

                    {{-- User Menu --}}
                    <div class="dropdown">
                        @php
                            $navbarAvatar = auth()->user()->avatar ?: \App\Models\User::DEFAULT_AVATAR_URL;
                        @endphp
                        <a href="#" class="dropdown-toggle uix-becf5e5a9c navbar-avatar-toggle" data-bs-toggle="dropdown">
                            <img src="{{ $navbarAvatar }}" alt="{{ auth()->user()->name }}" class="navbar-user-avatar" loading="lazy" decoding="async" onerror="this.onerror=null; this.src='{{ \App\Models\User::DEFAULT_AVATAR_URL }}';" style="width: 40px; height: 40px; min-width: 40px; max-width: 40px; min-height: 40px; max-height: 40px; aspect-ratio: 1 / 1; object-fit: cover; object-position: center; border-radius: 50%;">
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 uix-920e6abfc3">
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
                    <a class="uix-9cd4420129" href="{{ route('login') }}"><i class="far fa-user"></i></a>
                @endauth
            </div>
        </div>
    </div>
</header>

<!-- ── MOBILE MENU ── -->
<div class="offcanvas offcanvas-start uix-eed5c25912" tabindex="-1" id="mobileMenu">
    <div class="offcanvas-header border-bottom">
        <span class="brand-logo">SÀN <span>TÍM</span> VI EN</span>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body p-0">
        <div class="list-group list-group-flush">
            <a href="{{ route('home') }}" class="list-group-item list-group-item-action py-3 border-0 fw-600">TRANG CHỦ</a>
            <a href="{{ route('products.index') }}" class="list-group-item list-group-item-action py-3 border-0">SẢN PHẨM</a>
            <a href="{{ route('promotions.index') }}" class="list-group-item list-group-item-action py-3 border-0 text-danger fw-bold">KHUYẾN MÃI</a>
            <a href="{{ route('categories.show', ['category' => 'men']) }}" class="list-group-item list-group-item-action py-3 border-0">NAM</a>
            <a href="{{ route('categories.show', ['category' => 'women']) }}" class="list-group-item list-group-item-action py-3 border-0">NỮ</a>
            <a href="{{ route('pages.blog') }}" class="list-group-item list-group-item-action py-3 border-0">BLOG</a>
            <a href="{{ route('guides.size') }}" class="list-group-item list-group-item-action py-3 border-0">HƯỚNG DẪN CHỌN SIZE</a>
            <a href="{{ route('support.faq') }}" class="list-group-item list-group-item-action py-3 border-0">HỖ TRỢ</a>
            <a href="{{ route('pages.about') }}" class="list-group-item list-group-item-action py-3 border-0">VỀ CHÚNG TÔI</a>
        </div>
        <div class="p-3 mt-3">
            <form action="{{ route('products.index') }}" method="GET" class="d-flex align-items-center bg-light p-2 rounded-pill">
                <input class="uix-187b00e9e7" type="text" name="search" placeholder="Tìm kiếm...">
                <button class="uix-6c6053b117" type="submit"><i class="fas fa-search text-muted"></i></button>
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
            <div class="col-lg-4 col-md-12">
                <div class="footer-brand mb-3">SÀN <span>TÍM</span> VI EN</div>
                <p class="footer-copy">
                    Không gian mua sắm thời trang trực tuyến với sản phẩm, bài viết phối đồ
                    và hướng dẫn chọn size ngay trên website.
                </p>
                <a class="footer-support-link" href="{{ route('support.purchase-guide') }}">
                    <i class="fas fa-arrow-right me-2"></i>Xem hướng dẫn mua hàng
                </a>
            </div>

            <!-- Mua sắm -->
            <div class="col-lg-2 col-md-3 col-6">
                <div class="footer-title">Mua sắm</div>
                <ul class="footer-links">
                    <li><a href="{{ route('products.index') }}">Tất cả sản phẩm</a></li>
                    <li><a href="{{ route('promotions.index') }}">Khuyến mãi</a></li>
                    <li><a href="{{ route('categories.show', ['category' => 'men']) }}">Nam</a></li>
                    <li><a href="{{ route('categories.show', ['category' => 'women']) }}">Nữ</a></li>
                </ul>
            </div>

            <!-- Khám phá -->
            <div class="col-lg-2 col-md-3 col-6">
                <div class="footer-title">Khám phá</div>
                <ul class="footer-links">
                    <li><a href="{{ route('pages.blog') }}">Blog</a></li>
                    <li><a href="{{ route('guides.size') }}">Hướng dẫn chọn size</a></li>
                    <li><a href="{{ route('pages.about') }}">Về chúng tôi</a></li>
                    <li><a href="{{ route('pages.contact') }}">Liên hệ</a></li>
                </ul>
            </div>

            <!-- Hỗ trợ -->
            <div class="col-lg-2 col-md-3 col-6">
                <div class="footer-title">Hỗ trợ</div>
                <ul class="footer-links">
                    <li><a href="{{ route('support.purchase-guide') }}">Hướng dẫn mua hàng</a></li>
                    <li><a href="{{ route('support.faq') }}">Câu hỏi thường gặp</a></li>
                    <li><a href="{{ route('policies.payment') }}">Thanh toán</a></li>
                    @auth
                        <li><a href="{{ route('orders.index') }}">Đơn mua của tôi</a></li>
                    @else
                        <li><a href="{{ route('login') }}">Đăng nhập xem đơn</a></li>
                    @endauth
                </ul>
            </div>

            <!-- Chính sách -->
            <div class="col-lg-2 col-md-3 col-6">
                <div class="footer-title">Chính sách</div>
                <ul class="footer-links">
                    <li><a href="{{ route('policies.shipping') }}">Giao hàng</a></li>
                    <li><a href="{{ route('policies.returns') }}">Đổi trả</a></li>
                    <li><a href="{{ route('policies.privacy') }}">Bảo mật</a></li>
                    <li><a href="{{ route('policies.terms') }}">Điều khoản</a></li>
                </ul>
            </div>
        </div>

        <hr class="footer-divider">
        <div class="d-flex flex-wrap justify-content-between align-items-center footer-bottom">
            <span>© 2026 Sàn Tím Vi En. All Rights Reserved.</span>
            <div class="d-flex gap-3 mt-2 mt-md-0">
                <a class="uix-477a93d71b" href="{{ route('policies.privacy') }}">Chính sách bảo mật</a>
                <a class="uix-477a93d71b" href="{{ route('policies.terms') }}">Điều khoản sử dụng</a>
            </div>
        </div>
    </div>
</footer>

<!-- ── TOAST NOTIFICATIONS ── -->
<div id="toast-container" class="position-fixed bottom-0 end-0 p-3 uix-385992d49e">
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

<!-- ── SIZE PICKER MODAL ── -->
<div class="modal fade" id="sizePickerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content size-picker-modal">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title">Chọn size</h5>
                    <div class="size-picker-subtitle" id="sizePickerProductName">Sản phẩm này bắt buộc chọn size</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="size-picker-alert">
                    Vui lòng chọn size trước khi thêm sản phẩm vào giỏ hàng.
                </div>
                <div class="size-picker-options" id="sizePickerOptions"></div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const CSRF = document.querySelector('meta[name="csrf-token"]').content;
    const SCROLL_KEY = 'st:preserve-scroll:' + window.location.pathname;
    let pendingSizeForm = null;

    window.ST_SAVE_SCROLL = function() {
        sessionStorage.setItem(SCROLL_KEY, String(window.scrollY || window.pageYOffset || 0));
    };

    const savedScroll = sessionStorage.getItem(SCROLL_KEY);
    if (savedScroll !== null) {
        sessionStorage.removeItem(SCROLL_KEY);
        requestAnimationFrame(() => window.scrollTo(0, Number(savedScroll) || 0));
    }

    /* ── TOAST ── */
    window.showToast = function(message, type = 'success') {
        const container = document.getElementById('toast-container');
        if (!container) return;
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

    function getFormSize(form) {
        const sizeInput = form.querySelector('[name="size"]');
        return sizeInput ? sizeInput.value.trim() : '';
    }

    function setFormSize(form, size) {
        let sizeInput = form.querySelector('[name="size"]');
        if (!sizeInput) {
            sizeInput = document.createElement('input');
            sizeInput.type = 'hidden';
            sizeInput.name = 'size';
            form.appendChild(sizeInput);
        }
        sizeInput.value = size;
    }

    function getSizeOptions(form, fallbackSizes = null) {
        if (Array.isArray(fallbackSizes) && fallbackSizes.length) {
            return fallbackSizes;
        }

        try {
            const parsed = JSON.parse(form.dataset.sizeOptions || '[]');
            return Array.isArray(parsed) ? parsed : [];
        } catch {
            return [];
        }
    }

    function openSizePicker(form, fallbackSizes = null) {
        const sizes = getSizeOptions(form, fallbackSizes);
        if (!sizes.length) {
            showToast('Sản phẩm này chưa được cấu hình biến thể.', 'danger');
            return;
        }

        pendingSizeForm = form;
        const modalEl = document.getElementById('sizePickerModal');
        const nameEl = document.getElementById('sizePickerProductName');
        const optionsEl = document.getElementById('sizePickerOptions');
        if (!modalEl || !nameEl || !optionsEl) return;

        nameEl.textContent = form.dataset.productName
            ? `${form.dataset.productName} bắt buộc chọn biến thể`
            : 'Sản phẩm này bắt buộc chọn biến thể';

        optionsEl.replaceChildren(...sizes.map(size => {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'size-picker-option';
            button.dataset.size = String(size);
            button.textContent = String(size);
            return button;
        }));

        bootstrap.Modal.getOrCreateInstance(modalEl).show();
    }

    document.getElementById('sizePickerOptions')?.addEventListener('click', function(e) {
        const btn = e.target.closest('[data-size]');
        if (!btn || !pendingSizeForm) return;

        setFormSize(pendingSizeForm, btn.dataset.size);

        const form = pendingSizeForm;
        const detailSizeButton = form.id === 'addToCartForm'
            ? document.querySelector(`#sizeOptions [data-size="${CSS.escape(btn.dataset.size)}"]`)
            : null;
        if (detailSizeButton) {
            detailSizeButton.click();
        }

        const modalEl = document.getElementById('sizePickerModal');
        bootstrap.Modal.getOrCreateInstance(modalEl).hide();

        pendingSizeForm = null;
        form.requestSubmit();
    });

    /* ── AJAX ADD TO CART ── */
    document.addEventListener('submit', function(e) {
        const form = e.target;
        if (form.action && form.action.includes('cart/add')) {
            e.preventDefault();
            if (form.dataset.requiresSize === '1' && !getFormSize(form)) {
                openSizePicker(form);
                return;
            }

            const formData = new FormData(form);
            const btn = form.querySelector('button[type="submit"]');
            const orig = btn ? btn.innerHTML : '';
            if (btn) { btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>'; }
            fetch(form.action, {
                method: 'POST', body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
            })
            .then(r => {
                if (r.status === 401) {
                    window.location.href = '/login';
                    throw new Error('Unauthenticated');
                }
                return r.json();
            })
            .then(data => {
                if (data.size_required) {
                    openSizePicker(form, data.sizes || []);
                    return;
                }
                showToast(data.message || (data.success ? 'Đã thêm vào giỏ!' : 'Có lỗi!'), data.success ? 'success' : 'danger');
                if (data.success) {
                    const badge = document.getElementById('cart-badge-count');
                    if (badge) { badge.innerText = data.cart_count; badge.classList.remove('d-none'); }
                }
            })
            .catch((e) => {
                if (e.message !== 'Unauthenticated') {
                    showToast('Không thể kết nối máy chủ!', 'danger');
                }
            })
            .finally(() => { if (btn) { btn.disabled = false; btn.innerHTML = orig; } });
        }
    });

    /* ── AJAX WISHLIST TOGGLE ── */
    document.addEventListener('submit', function(e) {
        const form = e.target;
        if (!form.action || !form.action.includes('/wishlist/')) return;

        e.preventDefault();
        const btn = form.querySelector('button[type="submit"]');
        const orig = btn ? btn.innerHTML : '';
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        }

        fetch(form.action, {
            method: 'POST',
            body: new FormData(form),
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': CSRF,
                'Accept': 'application/json'
            }
        })
        .then(r => {
            if (r.status === 401) {
                window.location.href = '/login';
                throw new Error('Unauthenticated');
            }
            return r.json();
        })
        .then(data => {
            showToast(data.message || 'Đã cập nhật yêu thích!', data.success ? 'success' : 'danger');
            if (data.success && btn) {
                btn.classList.toggle('active', !!data.in_wishlist);
                btn.title = data.in_wishlist ? 'Bỏ yêu thích' : 'Yêu thích';
                btn.innerHTML = '<i class="' + (data.in_wishlist ? 'fas' : 'far') + ' fa-heart"></i>';
            }
        })
        .catch((err) => {
            if (err.message !== 'Unauthenticated') {
                showToast('Không thể kết nối máy chủ!', 'danger');
            }
        })
        .finally(() => {
            if (btn && btn.innerHTML.includes('fa-spinner')) {
                btn.innerHTML = orig;
            }
            if (btn) btn.disabled = false;
        });
    });

    document.addEventListener('submit', function(e) {
        const form = e.target;
        if (!e.defaultPrevented && form.method && form.method.toLowerCase() !== 'get') {
            window.ST_SAVE_SCROLL();
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
                <div class="uix-36c680eb9d">${n.time || ''}</div>
            </div>
            ${!isRead ? '<div class="uix-74fb9e9252"></div>' : ''}
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
            markAllBtn.classList.toggle('is-disabled', unreadCount <= 0);
        }

        if (!notifications || notifications.length === 0) {
            list.innerHTML = `<div class="text-center py-4 text-muted uix-9e6595fb01">
                <i class="far fa-bell fa-2x mb-2 d-block uix-30261166ec"></i>Không có thông báo
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

    // Initial fetch + poll every 5s (update liên tục, không cần load trang mới)
    fetchNotifications();
    setInterval(fetchNotifications, 5000);


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
            <div class="modal-content uix-f327d18487">
                <div class="modal-body p-0">
                    <div class="row g-0">
                        <!-- Left: Product Info -->
                        <div class="col-md-5 d-none d-md-block uix-daaca79a61">
                            <div class="p-4 text-center h-100 d-flex flex-column justify-content-center">
                                <img id="reviewModalProductImg" src="" class="img-fluid mb-3 mx-auto uix-609b27b1f8" alt="Product">
                                <h5 class="uix-08d718445d" id="reviewModalProductName"></h5>
                                <div class="uix-7286d32c47" id="reviewModalProductPrice"></div>
                            </div>
                        </div>
                        
                        <!-- Right: Review Form / View -->
                        <div class="col-md-7">
                            <div class="p-4 p-md-5">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h4 class="uix-b45f49dd17" id="reviewModalTitle">Đánh giá sản phẩm</h4>
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
                                        <textarea name="comment" class="form-control uix-504125c268" rows="4" placeholder="Chia sẻ cảm nhận của bạn về sản phẩm..."></textarea>
                                    </div>

                                    <div class="mb-4 uix-23a21395da">
                                        <label class="d-block mb-2 text-muted small fw-bold text-uppercase" " >Hình ảnh & Video</label>
                                        <div class="row g-2">
                                            <div class="col-6">
                                                <div class="upload-btn-wrapper w-100">
                                                    <button class="btn btn-outline-secondary w-100 py-2 uix-e95b25e990" type="button">
                                                        <i class="fas fa-camera me-2"></i>Thêm ảnh (Max 10)
                                                    </button>
                                                    <input type="file" name="images[]" multiple accept="image/*" id="reviewImagesInput">
                                                </div>
                                            </div>
                                            <div class="col-6 uix-23a21395da">
                                                <div class="upload-btn-wrapper w-100">
                                                    <button class="btn btn-outline-secondary w-100 py-2 uix-e95b25e990" type="button">
                                                        <i class="fas fa-video me-2"></i>Thêm Video
                                                    </button>
                                                    <input type="file" name="video" accept="video/*" id="reviewVideoInput">
                                                </div>
                                            </div>
                                        </div>
                                        <div id="mediaPreviews" class="mt-3 d-flex flex-wrap gap-2"></div>
                                    </div>

                                    <button type="submit" class="btn btn-dark w-100 py-3 uix-d75dfe9f13">Gửi đánh giá</button>
                                </form>

                                <!-- View Review Details -->
                                <div id="viewReviewContent" class="d-none">
                                    <div class="mb-4 text-center">
                                        <div id="viewRatingStars" class="star-rating fs-4 uix-e7d1077d40"></div>
                                        <div id="viewReviewDate" class="text-muted small mt-1"></div>
                                    </div>
                                    
                                    <div id="viewReviewComment" class="mb-4 p-3 border-start border-4 border-dark italic uix-6f6f97abd8"></div>

                                    <div id="viewReviewMedia" class="mb-4">
                                        <div id="viewReviewImages" class="d-flex flex-wrap gap-2 mb-3"></div>
                                        <div id="viewReviewVideo"></div>
                                    </div>

                                    <div id="reviewActionsView" class="d-none">
                                        <form id="deleteReviewForm" action="" method="POST" onsubmit="return confirmForm(this, 'Đánh giá này sẽ bị xóa vĩnh viễn và không thể khôi phục.', 'XÓA ĐÁNH GIÁ')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger w-100 py-3 uix-31dd6d940c">
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
                        imgContainer.innerHTML += `<img src="/storage/${img}" class="img-thumbnail uix-6c62354c48" onclick="window.open(this.src)">`;
                    });
                }

                const videoContainer = document.getElementById('viewReviewVideo');
                videoContainer.innerHTML = '';
                if (review.video) {
                    videoContainer.innerHTML = `
                        <video controls class="w-100 uix-f0dc170ba3">
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
<!-- ── PREMIUM DELETE MODAL ── -->
<div class="modal fade premium-modal" id="stDeleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered uix-af383d3ee2">
        <div class="modal-content">
            <div class="modal-body">
                <div class="premium-icon-wrap premium-icon-delete">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h3 class="premium-title" id="stDeleteTitle">Xóa sản phẩm</h3>
                <div class="premium-subtitle-pill" id="stDeletePill"></div>
                <p class="premium-description" id="stDeleteMessage">
                    Dữ liệu này sẽ bị xóa và không thể khôi phục.
                </p>
                <div class="premium-btn-group">
                    <button type="button" class="premium-btn premium-btn-secondary" data-bs-dismiss="modal">Giữ lại</button>
                    <button type="button" class="premium-btn premium-btn-danger" id="stDeleteConfirmBtn">
                        <i class="fas fa-trash-alt me-2"></i>Xóa ngay
                    </button>
                </div>
                <div class="premium-footer-note" id="stDeleteNote">
                    <i class="fas fa-exclamation-triangle uix-50bfccc846"></i> <span id="stDeleteNoteText">Thao tác này không thể hoàn tác</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ── PREMIUM CANCEL ORDER MODAL ── -->
<div class="modal fade premium-modal" id="stCancelOrderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered uix-af383d3ee2">
        <div class="modal-content">
            <div class="modal-body">
                <div class="premium-icon-wrap premium-icon-cancel">
                    <i class="fas fa-ban"></i>
                </div>
                <h3 class="premium-title" id="stActionTitle">Hủy đơn hàng</h3>
                <div class="premium-subtitle-pill" id="stCancelOrderPill">#ORD00000</div>
                <p class="premium-description" id="stActionMessage">Bạn có chắc chắn muốn hủy đơn hàng này không? Yêu cầu hủy sẽ được gửi cho Admin phê duyệt.</p>
                <div class="premium-btn-group">
                    <button type="button" class="premium-btn premium-btn-secondary" id="stActionCancelBtn" data-bs-dismiss="modal">Quay lại</button>
                    <button type="button" class="premium-btn premium-btn-orange" id="stCancelOrderConfirmBtn">Xác nhận hủy</button>
                </div>
                <div class="premium-footer-note" id="stActionNote">
                    <i class="fas fa-info-circle"></i> <span id="stActionNoteText">Yêu cầu sẽ được xử lý theo trạng thái đơn hàng.</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ── PREMIUM SUCCESS MODAL ── -->
<div class="modal fade premium-modal" id="stSuccessModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered uix-af383d3ee2">
        <div class="modal-content">
            <div class="modal-body">
                <div class="premium-icon-wrap premium-icon-success">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h3 class="premium-title" id="stSuccessTitle">Xác nhận</h3>
                <div class="premium-subtitle-pill" id="stSuccessPill">#000000</div>
                <p class="premium-description" id="stSuccessMessage">Bạn có chắc chắn muốn thực hiện hành động này không?</p>
                <div class="premium-btn-group">
                    <button type="button" class="premium-btn premium-btn-secondary" id="stSuccessCancelBtn" data-bs-dismiss="modal">Hủy bỏ</button>
                    <button type="button" class="premium-btn premium-btn-success" id="stSuccessConfirmBtn">Xác nhận ngay</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    /* ── GLOBAL PREMIUM CONFIRM FUNCTIONS ── */
    
    // 1. Confirm Delete
    window.stConfirmDelete = function(options) {
        const modalEl = document.getElementById('stDeleteModal');
        if (!modalEl) return;
        
        // Use existing instance or create new
        let modal = bootstrap.Modal.getInstance(modalEl);
        if (!modal) modal = new bootstrap.Modal(modalEl);
        
        document.getElementById('stDeleteTitle').textContent = options.title || 'Xóa mục này';
        document.getElementById('stDeletePill').textContent = options.pill || '';
        document.getElementById('stDeleteMessage').textContent = options.message || 'Hành động này không thể hoàn tác.';
        document.getElementById('stDeleteNoteText').textContent = options.note || 'Thao tác này không thể hoàn tác';
        
        const confirmBtn = document.getElementById('stDeleteConfirmBtn');
        confirmBtn.textContent = options.confirmText || 'Xóa ngay';
        const newBtn = confirmBtn.cloneNode(true);
        confirmBtn.parentNode.replaceChild(newBtn, confirmBtn);
        
        newBtn.addEventListener('click', function(e) {
            e.preventDefault();
            newBtn.disabled = true;
            newBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang xử lý...';
            if (options.onConfirm) options.onConfirm();
            setTimeout(() => modal.hide(), 500); 
        });
        
        modal.show();
    };

    // 2. Confirm non-delete state-changing action
    window.stConfirmAction = function(options) {
        const modalEl = document.getElementById('stCancelOrderModal');
        if (!modalEl) return;

        let modal = bootstrap.Modal.getInstance(modalEl);
        if (!modal) modal = new bootstrap.Modal(modalEl);

        document.getElementById('stActionTitle').textContent = options.title || 'Xác nhận hành động';
        document.getElementById('stCancelOrderPill').textContent = options.pill || '';
        document.getElementById('stActionMessage').textContent = options.message || 'Bạn có chắc chắn muốn thực hiện hành động này không?';
        document.getElementById('stActionNoteText').textContent = options.note || 'Trạng thái sẽ được cập nhật sau khi xác nhận.';
        document.getElementById('stActionCancelBtn').textContent = options.cancelText || 'Quay lại';

        const confirmBtn = document.getElementById('stCancelOrderConfirmBtn');
        confirmBtn.textContent = options.confirmText || 'Xác nhận';
        const newBtn = confirmBtn.cloneNode(true);
        confirmBtn.parentNode.replaceChild(newBtn, confirmBtn);

        newBtn.addEventListener('click', function(e) {
            e.preventDefault();
            newBtn.disabled = true;
            newBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang xử lý...';
            if (options.onConfirm) options.onConfirm();
            setTimeout(() => modal.hide(), 500);
        });
        
        modal.show();
    };

    window.stConfirmCancelOrder = function(options) {
        stConfirmAction({
            title: 'Hủy đơn hàng',
            pill: options.orderCode || 'Đơn hàng',
            message: 'Bạn có chắc chắn muốn hủy đơn hàng này không? Yêu cầu hủy sẽ được gửi cho Admin phê duyệt.',
            note: 'Yêu cầu hủy sẽ chờ Admin duyệt; hoàn kho và hoàn tiền xử lý theo trạng thái đơn.',
            confirmText: options.confirmText || 'Xác nhận hủy',
            onConfirm: options.onConfirm
        });
    };

    // 3. Confirm Success Action
    window.stConfirmSuccess = function(options) {
        const modalEl = document.getElementById('stSuccessModal');
        if (!modalEl) return;

        let modal = bootstrap.Modal.getInstance(modalEl);
        if (!modal) modal = new bootstrap.Modal(modalEl);
        
        document.getElementById('stSuccessTitle').innerText = options.title || 'Xác nhận';
        document.getElementById('stSuccessPill').innerText = options.pill || 'Hành động';
        document.getElementById('stSuccessMessage').innerText = options.message || 'Bạn có chắc chắn?';
        document.getElementById('stSuccessCancelBtn').textContent = options.cancelText || 'Hủy bỏ';

        const confirmBtn = document.getElementById('stSuccessConfirmBtn');
        confirmBtn.innerText = options.confirmText || 'Xác nhận ngay';
        const newBtn = confirmBtn.cloneNode(true);
        confirmBtn.parentNode.replaceChild(newBtn, confirmBtn);

        newBtn.addEventListener('click', function(e) {
            e.preventDefault();
            newBtn.disabled = true;
            newBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang xử lý...';
            if (options.onConfirm) options.onConfirm();
            setTimeout(() => modal.hide(), 500);
        });
        
        modal.show();
    };

    // Legacy helper for forms (updated to use premium designs)
    window.confirmForm = function(form, message, title = 'XÁC NHẬN', type = 'danger', confirmText = null) {
        const pill = title === 'HỦY ĐƠN HÀNG' ? 'Đơn hàng' : (form.dataset.itemName || 'Hành động');
        const finalConfirmText = confirmText || form.dataset.confirmText || null;
        const note = form.dataset.confirmNote || null;
        
        if (title === 'HỦY ĐƠN HÀNG') {
             stConfirmCancelOrder({
                orderCode: form.dataset.orderCode || 'Đơn hàng',
                confirmText: finalConfirmText,
                onConfirm: () => {
                    window.ST_SAVE_SCROLL();
                    form.onsubmit = null;
                    form.submit();
                }
            });
        } else if (type === 'success') {
            stConfirmSuccess({
                title: title,
                pill: pill,
                message: message,
                confirmText: finalConfirmText,
                onConfirm: () => {
                    window.ST_SAVE_SCROLL();
                    form.onsubmit = null;
                    form.submit();
                }
            });
        } else if (type === 'danger') {
            stConfirmDelete({
                title: title,
                pill: pill,
                message: message,
                confirmText: finalConfirmText,
                note: note,
                onConfirm: () => {
                    window.ST_SAVE_SCROLL();
                    form.onsubmit = null;
                    form.submit();
                }
            });
        } else {
            stConfirmAction({
                title: title,
                pill: pill,
                message: message,
                confirmText: finalConfirmText,
                note: note,
                onConfirm: () => {
                    window.ST_SAVE_SCROLL();
                    form.onsubmit = null;
                    form.submit();
                }
            });
        }
        return false;
    };
</script>
</body>
</html>
