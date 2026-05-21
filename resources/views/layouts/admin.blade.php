<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') - Quản trị hệ thống</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Airbnb Style -->
    <link rel="stylesheet" href="{{ asset('css/airbnb-style.css') }}?v={{ filemtime(public_path('css/airbnb-style.css')) }}">
    <!-- Admin Style -->
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}?v={{ filemtime(public_path('css/admin.css')) }}">

    @stack('styles')
    @vite(['public/css/admin_layout.css', 'public/js/app.js'])
    @vite(['public/css/extracted-inline.css'])
</head>
<body>

    <!-- Top Navbar -->
    <nav class="navbar admin-navbar navbar-expand-lg">
        <div class="container-fluid px-4">
            <a class="navbar-brand" href="{{ route('admin.dashboard') }}">
                <i class="fas fa-shield-alt me-2"></i>ADMIN PANEL
            </a>
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('home') }}" class="btn btn-light btn-sm"><i class="fas fa-home me-1"></i>
                Trang chủ
                </a>
                <div class="dropdown">
                    <button class="btn btn-light btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        {{ Auth::user()->name }}
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><h6 class="dropdown-header">{{ Auth::user()->email }}</h6></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="{{ route('profile.index') }}">
                                <i class="fas fa-user me-2"></i>Thông tin cá nhân
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    <i class="fas fa-sign-out-alt me-2"></i>Đăng xuất
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Flash Messages → Toast Popup -->
    @php
        $toastMessages = [];
        if (session('success')) $toastMessages[] = ['type' => 'success', 'msg' => session('success')];
        if (session('error'))   $toastMessages[] = ['type' => 'error',   'msg' => session('error')];
        if (session('warning')) $toastMessages[] = ['type' => 'warning', 'msg' => session('warning')];
        if (session('info'))    $toastMessages[] = ['type' => 'info',    'msg' => session('info')];
    @endphp

    <div class="container-fluid">
        <div class="row g-0">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block admin-sidebar collapse">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column gap-2 px-2">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                                <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}" href="{{ route('admin.users') }}">
                                <i class="fas fa-users me-2"></i> Người dùng
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.products*') ? 'active' : '' }}" href="{{ route('admin.products') }}">
                                <i class="fas fa-box me-2"></i> Sản phẩm
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.categories*') ? 'active' : '' }}" href="{{ route('admin.categories.index') }}">
                                <i class="fas fa-tags me-2"></i> Danh mục
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.orders*') ? 'active' : '' }}" href="{{ route('admin.orders.index') }}">
                                <i class="fas fa-shopping-cart me-2"></i> Đơn hàng
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.inventory*') ? 'active' : '' }}" href="{{ route('admin.inventory.index') }}">
                                <i class="fas fa-warehouse me-2"></i> Kho hàng
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.promotions*') ? 'active' : '' }}" href="{{ route('admin.promotions.index') }}">
                                <i class="fas fa-tags me-2"></i> Khuyến mãi
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.statistics') ? 'active' : '' }}" href="{{ route('admin.statistics') }}">
                                <i class="fas fa-chart-bar me-2"></i> Thống kê
                            </a>
                        </li>
                    </ul>

                    <div class="mt-5 px-3">
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger w-100 rounded-12-px-custom py-2 fw-bold uix-eefff2f8dd">
                                <i class="fas fa-sign-out-alt me-2"></i> ĐĂNG XUẤT
                            </button>
                        </form>
                    </div>
                </div>
            </nav>

            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 admin-content">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Toast Container -->
    <div id="toast-container"></div>

    <!-- ── PREMIUM DELETE MODAL ── -->
    <div class="modal fade premium-modal" id="stDeleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered uix-af383d3ee2">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="premium-icon-wrap premium-icon-delete">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <h3 class="premium-title" id="stDeleteTitle">Xóa mục này</h3>
                    <div class="premium-subtitle-pill" id="stDeletePill">Đang chọn...</div>
                    <p class="premium-description" id="stDeleteMessage">
                        Hành động này sẽ xóa vĩnh viễn dữ liệu và không thể khôi phục.
                    </p>
                    <div class="premium-btn-group">
                        <button type="button" class="premium-btn premium-btn-secondary" data-bs-dismiss="modal">HỦY</button>
                        <button type="button" class="premium-btn premium-btn-danger" id="stDeleteConfirmBtn">
                            XÓA NGAY
                        </button>
                    </div>
                    <div class="premium-footer-note">
                        <i class="fas fa-exclamation-triangle uix-50bfccc846"></i> Thao tác này không thể hoàn tác
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
                    <h3 class="premium-title">Xác nhận hành động</h3>
                    <div class="premium-subtitle-pill" id="stCancelOrderPill">#ORD00000</div>
                    <p class="premium-description">Bạn có chắc chắn muốn thực hiện hành động này không?</p>
                    <div class="premium-btn-group">
                        <button type="button" class="premium-btn premium-btn-secondary" data-bs-dismiss="modal">QUAY LẠI</button>
                        <button type="button" class="premium-btn premium-btn-orange" id="stCancelOrderConfirmBtn">XÁC NHẬN</button>
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
                    <div class="premium-subtitle-pill" id="stSuccessPill">Dữ liệu</div>
                    <p class="premium-description" id="stSuccessMessage">Bạn có chắc chắn muốn thực hiện hành động này không?</p>
                    <div class="premium-btn-group">
                        <button type="button" class="premium-btn premium-btn-secondary" data-bs-dismiss="modal">Hủy bỏ</button>
                        <button type="button" class="premium-btn premium-btn-success" id="stSuccessConfirmBtn">Xác nhận ngay</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast + Confirm System -->
    <script>
    const _toastIcons = {
        success: 'fa-check-circle',
        error:   'fa-exclamation-circle',
        warning: 'fa-exclamation-triangle',
        info:    'fa-info-circle',
    };

    function showToast(message, type = 'success', duration = 4000) {
        const container = document.getElementById('toast-container');
        const item = document.createElement('div');
        item.className = `toast-item toast-${type}`;
        item.innerHTML = `
            <i class="fas ${_toastIcons[type] || _toastIcons.info} toast-icon"></i>
            <div class="toast-body">${message}</div>
            <button class="toast-close" onclick="dismissToast(this.parentElement)">
                <i class="fas fa-times"></i>
            </button>`;
        container.appendChild(item);
        const timer = setTimeout(() => dismissToast(item), duration);
        item._timer = timer;
    }

    function dismissToast(item) {
        if (!item || item._dismissed) return;
        item._dismissed = true;
        clearTimeout(item._timer);
        item.classList.add('toast-out');
        setTimeout(() => item.remove(), 260);
    }

    // 1. Confirm Delete
    window.stConfirmDelete = function(options) {
        const modalEl = document.getElementById('stDeleteModal');
        if (!modalEl) return;
        let modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        
        document.getElementById('stDeleteTitle').innerText = options.title || 'Xác nhận xóa';
        document.getElementById('stDeletePill').innerText = options.pill || 'Dữ liệu';
        document.getElementById('stDeleteMessage').innerText = options.message || 'Hành động này không thể hoàn tác.';
        
        const confirmBtn = document.getElementById('stDeleteConfirmBtn');
        const newBtn = confirmBtn.cloneNode(true);
        confirmBtn.parentNode.replaceChild(newBtn, confirmBtn);
        
        newBtn.addEventListener('click', function() {
            newBtn.disabled = true;
            newBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            if (options.onConfirm) options.onConfirm();
            setTimeout(() => modal.hide(), 500);
        });
        modal.show();
    };

    // 2. Confirm Action (Cancel/Approve etc)
    window.stConfirmAction = function(options) {
        const modalEl = document.getElementById('stCancelOrderModal');
        if (!modalEl) return;
        let modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        
        modalEl.querySelector('.premium-title').innerText = options.title || 'Xác nhận';
        document.getElementById('stCancelOrderPill').innerText = options.pill || '';
        modalEl.querySelector('.premium-description').innerText = options.message || 'Bạn có chắc chắn?';
        
        const confirmBtn = document.getElementById('stCancelOrderConfirmBtn');
        const newBtn = confirmBtn.cloneNode(true);
        confirmBtn.parentNode.replaceChild(newBtn, confirmBtn);
        
        newBtn.addEventListener('click', function() {
            newBtn.disabled = true;
            newBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            if (options.onConfirm) options.onConfirm();
            setTimeout(() => modal.hide(), 500);
        });
        modal.show();
    };

    // 3. Confirm Success Action
    window.stConfirmSuccess = function(options) {
        const modalEl = document.getElementById('stSuccessModal');
        if (!modalEl) return;
        let modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        
        document.getElementById('stSuccessTitle').innerText = options.title || 'Xác nhận';
        document.getElementById('stSuccessPill').innerText = options.pill || 'Hành động';
        document.getElementById('stSuccessMessage').innerText = options.message || 'Bạn có chắc chắn?';
        
        const confirmBtn = document.getElementById('stSuccessConfirmBtn');
        const newBtn = confirmBtn.cloneNode(true);
        confirmBtn.parentNode.replaceChild(newBtn, confirmBtn);
        
        newBtn.addEventListener('click', function() {
            newBtn.disabled = true;
            newBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            if (options.onConfirm) options.onConfirm();
            setTimeout(() => modal.hide(), 500);
        });
        modal.show();
    };

    // Compatibility for existing showConfirm
    window.showConfirm = function(message, onConfirm, title = 'XÁC NHẬN', type = 'danger') {
        if (type === 'danger') {
            stConfirmDelete({ title, message, pill: 'Dữ liệu', onConfirm });
        } else if (type === 'success') {
            stConfirmSuccess({ title, message, onConfirm });
        } else {
            stConfirmAction({ title, message, onConfirm });
        }
    };

    // Helper for forms
    window.confirmForm = function(form, message, title = 'XÁC NHẬN', type = 'danger') {
        if (type === 'danger') {
            stConfirmDelete({ title, message, pill: 'Hành động', onConfirm: () => form.submit() });
        } else if (type === 'success') {
            stConfirmSuccess({ title, message, pill: 'Hành động', onConfirm: () => form.submit() });
        } else {
            stConfirmAction({ title, message, onConfirm: () => form.submit() });
        }
        return false;
    };

    @foreach($toastMessages ?? [] as $t)
        showToast('{{ addslashes($t['msg']) }}', '{{ $t['type'] }}');
    @endforeach

    @if($errors->any())
        @foreach($errors->all() as $error)
            showToast('{{ addslashes($error) }}', 'error');
        @endforeach
    @endif
    </script>

    @stack('scripts')
</body>
</html>
