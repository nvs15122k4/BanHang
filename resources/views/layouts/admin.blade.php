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
    <style>
        :root {
            --primary: #6366f1;
            --bg-dark: #f8fafc;
            --bg-card: rgba(0,0,0,0.05);
            --text-main: #1e293b;
            --text-muted: #64748b;
            --border-color: rgba(0,0,0,0.05);
            --gradient-brand: linear-gradient(135deg, #6366f1, #8b5cf6, #a855f7);
            --glass-bg: rgba(255, 255, 255, 0.85);
            --glass-border: 1px solid rgba(0,0,0,0.05);
        }
        body {
            background-color: var(--bg-dark);
            background-image: 
                radial-gradient(circle at 15% 50%, rgba(99, 102, 241, 0.1), transparent 25%),
                radial-gradient(circle at 85% 30%, rgba(217, 70, 239, 0.1), transparent 25%);
            background-attachment: fixed;
            color: var(--text-main);
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
        }
        .admin-navbar {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border-bottom: var(--glass-border);
            padding: 1rem 0;
            position: sticky;
            top: 0;
            z-index: 1050;
        }
        .admin-navbar .navbar-brand {
            color: var(--text-main) !important;
            font-weight: 700;
            font-family: 'Outfit', sans-serif;
        }
        .admin-sidebar {
            background: rgba(0,0,0,0.02);
            border-right: var(--glass-border);
            min-height: calc(100vh - 70px);
            padding: 24px 16px;
        }
        .admin-sidebar .nav-link {
            color: var(--text-muted) !important;
            font-weight: 500;
            padding: 12px 16px !important;
            border-radius: 12px;
            margin-bottom: 4px;
            transition: all 0.3s;
        }
        .admin-sidebar .nav-link:hover {
            background: rgba(0,0,0,0.05);
            color: var(--text-main) !important;
        }
        .admin-sidebar .nav-link.active {
            background: rgba(99, 102, 241, 0.1) !important;
            color: var(--primary) !important;
            font-weight: 600;
        }
        .admin-content {
            padding: 2rem;
        }
        .btn-outline-light {
            border-color: rgba(0,0,0,0.1);
            color: var(--text-main);
        }
        .btn-outline-light:hover {
            background: rgba(0,0,0,0.05);
            color: var(--primary);
        }
        .btn-light {
            background: rgba(0,0,0,0.05);
            border-color: rgba(0,0,0,0.05);
            color: var(--text-main);
        }
        .btn-light:hover {
            background: rgba(0,0,0,0.1);
        }
        
        /* Stats and tables */
        .stat-card {
            background: var(--glass-bg);
            backdrop-filter: blur(15px);
            border: var(--glass-border);
            border-radius: 20px;
            padding: 1.5rem;
            transition: all 0.3s;
        }
        .stat-card:hover {
            transform: translateY(-4px);
            border-color: rgba(99, 102, 241, 0.4);
        }
        .stat-icon {
            width: 48px; height: 48px;
            border-radius: 16px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.5rem;
            color: #fff;
            background: var(--primary);
        }
        .stat-value {
            font-size: 1.5rem;
            font-weight: 700;
            font-family: 'Outfit', sans-serif;
            color: var(--text-main);
        }
        .stat-label {
            font-size: 0.9rem;
            color: var(--text-muted);
            font-weight: 500;
        }
        
        .admin-table {
            background: var(--glass-bg);
            backdrop-filter: blur(15px);
            border: var(--glass-border);
            border-radius: 20px;
            overflow: hidden;
        }
        .admin-table .card-header {
            background: rgba(0,0,0,0.05) !important;
            color: var(--text-main) !important;
            font-family: 'Outfit', sans-serif;
            font-weight: 600 !important;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            padding: 1rem 1.5rem;
        }
        .admin-table table {
            color: var(--text-main);
            margin-bottom: 0;
        }
        .admin-table th {
            background: rgba(0,0,0,0.02) !important;
            color: var(--text-muted) !important;
            font-weight: 600;
            border-bottom: 1px solid rgba(0,0,0,0.05) !important;
        }
        .admin-table td {
            border-bottom: 1px dashed rgba(0,0,0,0.05);
            vertical-align: middle;
        }
        .admin-table tr:hover td {
            background: rgba(0,0,0,0.02);
        }
        .admin-table .card-footer {
            background: rgba(0,0,0,0.02) !important;
            border-top: 1px solid rgba(0,0,0,0.05);
        }
        
        .page-header h1 {
            font-family: 'Outfit', sans-serif;
            font-weight: 700;
            font-size: 2rem;
            color: var(--text-main);
            margin-bottom: 2rem;
        }
    </style>
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
            <div class="col-md-2 admin-sidebar">
                <nav class="nav flex-column">
                    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                       href="{{ route('admin.dashboard') }}">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                    <a class="nav-link {{ request()->routeIs('admin.users') ? 'active' : '' }}"
                       href="{{ route('admin.users') }}">
                        <i class="fas fa-users"></i> Quản lý Users
                    </a>
                    <a class="nav-link {{ request()->routeIs('admin.products') ? 'active' : '' }}"
                       href="{{ route('admin.products') }}">
                        <i class="fas fa-box"></i> Quản lý Sản phẩm
                    </a>
                    <a class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}"
                       href="{{ route('admin.orders.index') }}">
                        <i class="fas fa-shopping-cart"></i> Quản lý Đơn hàng
                    </a>
                    <a class="nav-link {{ request()->routeIs('admin.inventory.*') ? 'active' : '' }}"
                       href="{{ route('admin.inventory.index') }}">
                        <i class="fas fa-warehouse"></i> Quản lý Kho
                    </a>
                    <a class="nav-link {{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}"
                       href="{{ route('admin.reviews.index') }}">
                        <i class="fas fa-star"></i> Đánh giá SP
                    </a>
                    <a class="nav-link {{ request()->routeIs('admin.statistics') ? 'active' : '' }}"
                       href="{{ route('admin.statistics') }}">
                        <i class="fas fa-chart-bar"></i> Thống kê
                    </a>
                </nav>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 admin-content">
                @yield('content')
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Toast Container -->
    <div id="toast-container"></div>

    <!-- Confirm Modal -->
    <div id="confirm-overlay">
        <div id="confirm-box">
            <div style="width:48px;height:48px;background:#FFF0F0;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                <i class="fas fa-exclamation-triangle" style="color:#D30005;font-size:20px;"></i>
            </div>
            <h5 id="confirm-title">Xác nhận</h5>
            <p id="confirm-message"></p>
            <div class="confirm-btns">
                <button class="btn-cancel" onclick="closeConfirm()">Hủy</button>
                <button class="btn-ok" id="confirm-ok-btn">Xác nhận</button>
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

    let _confirmCallback = null;

    function showConfirm(message, onConfirm, title = 'Xác nhận hành động') {
        document.getElementById('confirm-title').textContent   = title;
        document.getElementById('confirm-message').textContent = message;
        _confirmCallback = onConfirm;
        document.getElementById('confirm-overlay').classList.add('show');
    }

    function closeConfirm() {
        document.getElementById('confirm-overlay').classList.remove('show');
        _confirmCallback = null;
    }

    document.getElementById('confirm-ok-btn').addEventListener('click', function () {
        closeConfirm();
        if (typeof _confirmCallback === 'function') _confirmCallback();
    });

    document.getElementById('confirm-overlay').addEventListener('click', function (e) {
        if (e.target === this) closeConfirm();
    });

    @foreach($toastMessages ?? [] as $t)
        showToast('{{ addslashes($t['msg']) }}', '{{ $t['type'] }}');
    @endforeach
    </script>

    @stack('scripts')
</body>
</html>
