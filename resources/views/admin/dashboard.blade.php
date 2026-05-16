@extends('layouts.admin')

@section('title', 'Dashboard')

@push('styles')
    @vite(['resources/css/admin_common.css'])
@endpush

@section('content')
<div class="page-header">
    <h1><i class="fas fa-tachometer-alt me-3"></i>DASHBOARD</h1>
</div>

<!-- Statistics Cards -->
<div class="row">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-center">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="ms-3 flex-grow-1">
                    <div class="stat-value">{{ $stats['total_users'] }}</div>
                    <div class="stat-label">Tổng Users</div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-center">
                <div class="stat-icon bg-danger-custom">
                    <i class="fas fa-user-shield"></i>
                </div>
                <div class="ms-3 flex-grow-1">
                    <div class="stat-value">{{ $stats['total_admins'] }}</div>
                    <div class="stat-label">Admins</div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-center">
                <div class="stat-icon bg-success-custom">
                    <i class="fas fa-box"></i>
                </div>
                <div class="ms-3 flex-grow-1">
                    <div class="stat-value">{{ $stats['total_products'] }}</div>
                    <div class="stat-label">Tổng Sản phẩm</div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-center">
                <div class="stat-icon bg-warning-custom">
                    <i class="fas fa-warehouse"></i>
                </div>
                <div class="ms-3 flex-grow-1">
                    <div class="stat-value">{{ number_format($stats['total_stock']) }}</div>
                    <div class="stat-label">Tổng Tồn kho</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-center">
                <div class="stat-icon bg-info-custom">
                    <i class="fas fa-user"></i>
                </div>
                <div class="ms-3 flex-grow-1">
                    <div class="stat-value">{{ $stats['total_customers'] }}</div>
                    <div class="stat-label">Khách hàng</div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-center">
                <div class="stat-icon bg-success-custom">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="ms-3 flex-grow-1">
                    <div class="stat-value">{{ $stats['active_products'] }}</div>
                    <div class="stat-label">SP Đang bán</div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-center">
                <div class="stat-icon bg-secondary-custom">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="ms-3 flex-grow-1">
                    <div class="stat-value">{{ $stats['inactive_products'] }}</div>
                    <div class="stat-label">SP Ngừng bán</div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-center">
                <div class="stat-icon bg-danger-custom">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="ms-3 flex-grow-1">
                    <div class="stat-value">{{ $stats['low_stock_products'] }}</div>
                    <div class="stat-label">SP Sắp hết</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Data -->
<div class="row mt-4">
    <!-- Recent Users -->
    <div class="col-md-6">
        <div class="card admin-table">
            <div class="card-header">
                <i class="fas fa-users me-2"></i>NGƯỜI DÙNG MỚI NHẤT
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr class="bg-gray-light-custom">
                            <th>Tên</th>
                            <th>Email</th>
                            <th>Vai trò</th>
                            <th>Ngày tạo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentUsers as $user)
                            <tr>
                                <td class="font-medium-custom">{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    <span class="badge-role-{{ $user->role }}">
                                        {{ $user->role === 'admin' ? 'ADMIN' : 'USER' }}
                                    </span>
                                </td>
                                <td>{{ $user->created_at->format('d/m/Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">Chưa có người dùng nào</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer text-center bg-gray-light-custom">
                <a href="{{ route('admin.users') }}" class="btn btn-sm btn-primary">
                    Xem tất cả <i class="fas fa-arrow-right ms-2"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Recent Products -->
    <div class="col-md-6">
        <div class="card admin-table">
            <div class="card-header">
                <i class="fas fa-box me-2"></i>SẢN PHẨM MỚI NHẤT
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr class="bg-gray-light-custom">
                            <th>Tên sản phẩm</th>
                            <th>Giá</th>
                            <th>Số lượng</th>
                            <th>Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentProducts as $product)
                            <tr>
                                <td class="font-medium-custom">{{ Str::limit($product->ten_sp, 30) }}</td>
                                <td>{{ number_format($product->gia) }}đ</td>
                                <td>
                                    <span class="{{ $product->so_luong < 10 ? 'text-danger' : '' }} font-medium-custom">
                                        {{ $product->so_luong }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge-status-{{ $product->trang_thai }}">
                                        {{ $product->trang_thai === 'active' ? 'ĐANG BÁN' : 'NGỪNG BÁN' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">Chưa có sản phẩm nào</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer text-center bg-gray-light-custom">
                <a href="{{ route('admin.products') }}" class="btn btn-sm btn-primary">
                    Xem tất cả <i class="fas fa-arrow-right ms-2"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Low Stock Alert -->
@if($lowStockProducts->count() > 0)
<div class="row mt-4">
    <div class="col-12">
        <div class="card admin-table">
            <div class="card-header bg-danger-light-alert-custom text-danger-alert-custom border-danger-alert-custom">
                <i class="fas fa-exclamation-triangle me-2"></i>CẢNH BÁO: SẢN PHẨM SẮP HẾT HÀNG (< 10)
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr class="bg-gray-light-custom">
                            <th>Tên sản phẩm</th>
                            <th>Giá</th>
                            <th>Số lượng còn</th>
                            <th>Trạng thái</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($lowStockProducts as $product)
                            <tr>
                                <td class="font-medium-custom">{{ $product->ten_sp }}</td>
                                <td>{{ number_format($product->gia) }}đ</td>
                                <td>
                                    <span class="text-danger fw-bold">{{ $product->so_luong }}</span>
                                </td>
                                <td>
                                    <span class="badge-status-{{ $product->trang_thai }}">
                                        {{ $product->trang_thai === 'active' ? 'ĐANG BÁN' : 'NGỪNG BÁN' }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i> Nhập thêm
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Product Status Chart -->
<div class="row mt-4">
    <div class="col-md-6">
        <div class="card admin-table">
            <div class="card-header">
                <i class="fas fa-chart-pie me-2"></i>PHÂN BỐ TRẠNG THÁI SẢN PHẨM
            </div>
            <div class="card-body">
                <canvas id="productStatusChart" height="200"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card admin-table">
            <div class="card-header">
                <i class="fas fa-info-circle me-2"></i>THÔNG TIN HỆ THỐNG
            </div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr>
                        <td class="font-medium-custom"><i class="fas fa-calendar me-2"></i>Ngày hôm nay:</td>
                        <td>{{ now()->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <td class="font-medium-custom"><i class="fas fa-clock me-2"></i>Giờ hiện tại:</td>
                        <td>{{ now()->format('H:i:s') }}</td>
                    </tr>
                    <tr>
                        <td class="font-medium-custom"><i class="fas fa-user-shield me-2"></i>Đăng nhập với:</td>
                        <td>{{ Auth::user()->name }} ({{ Auth::user()->email }})</td>
                    </tr>
                    <tr>
                        <td class="font-medium-custom"><i class="fas fa-database me-2"></i>Database:</td>
                        <td>MySQL</td>
                    </tr>
                    <tr>
                        <td class="font-medium-custom"><i class="fas fa-code me-2"></i>Framework:</td>
                        <td>Laravel {{ app()->version() }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Product Status Chart
const ctx = document.getElementById('productStatusChart').getContext('2d');
const productStatusChart = new Chart(ctx, {
    type: 'doughnut',
    data: {
        labels: ['Đang bán', 'Ngừng bán'],
        datasets: [{
            data: [{{ $stats['active_products'] }}, {{ $stats['inactive_products'] }}],
            backgroundColor: ['#28A745', '#6C757D'],
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    font: {
                        family: 'Inter',
                        size: 14,
                        weight: 500
                    },
                    padding: 20
                }
            }
        }
    }
});
</script>
@endpush
@endsection
