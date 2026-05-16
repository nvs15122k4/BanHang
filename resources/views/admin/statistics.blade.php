@extends('layouts.admin')

@section('title', 'Thống kê')

@push('styles')
    @vite(['resources/css/admin_common.css'])
@endpush

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-chart-bar me-3"></i>THỐNG KÊ HỆ THỐNG</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('export.products') }}" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-file-excel me-2"></i>Xuất Excel sản phẩm
            </a>
            <a href="{{ route('export.statistics') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-chart-line me-2"></i>Xuất thống kê
            </a>
        </div>
    </div>
</div>

<!-- User Statistics -->
<div class="row">
    <div class="col-12">
        <div class="card admin-table mb-4">
            <div class="card-header">
                <i class="fas fa-users me-2"></i>THỐNG KÊ NGƯỜI DÙNG
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-2">
                        <div class="text-center">
                            <div class="stat-value">{{ $userStats['total'] }}</div>
                            <div class="stat-label">Tổng Users</div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="text-center">
                            <div class="stat-value text-danger">{{ $userStats['admins'] }}</div>
                            <div class="stat-label">Admins</div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="text-center">
                            <div class="stat-value text-primary">{{ $userStats['users'] }}</div>
                            <div class="stat-label">Customers</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <div class="stat-value text-success">{{ $userStats['with_addresses'] }}</div>
                            <div class="stat-label">Có địa chỉ</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <div class="stat-value text-muted">{{ $userStats['without_addresses'] }}</div>
                            <div class="stat-label">Chưa có địa chỉ</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Product Statistics -->
<div class="row">
    <div class="col-12">
        <div class="card admin-table mb-4">
            <div class="card-header">
                <i class="fas fa-box me-2"></i>THỐNG KÊ SẢN PHẨM
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="text-center">
                            <div class="stat-value">{{ $productStats['total'] }}</div>
                            <div class="stat-label">Tổng sản phẩm</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <div class="stat-value text-success">{{ $productStats['active'] }}</div>
                            <div class="stat-label">Đang bán</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <div class="stat-value text-secondary">{{ $productStats['inactive'] }}</div>
                            <div class="stat-label">Ngừng bán</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <div class="stat-value text-warning">{{ number_format($productStats['total_stock']) }}</div>
                            <div class="stat-label">Tổng tồn kho</div>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-3">
                        <div class="text-center">
                            <div class="stat-value">{{ number_format($productStats['avg_price']) }}đ</div>
                            <div class="stat-label">Giá trung bình</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <div class="stat-value text-success">{{ number_format($productStats['max_price']) }}đ</div>
                            <div class="stat-label">Giá cao nhất</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <div class="stat-value text-info">{{ number_format($productStats['min_price']) }}đ</div>
                            <div class="stat-label">Giá thấp nhất</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <div class="stat-value text-danger">{{ $productStats['low_stock'] }}</div>
                            <div class="stat-label">Sắp hết hàng</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts -->
<div class="row">
    <div class="col-md-6">
        <div class="card admin-table mb-4">
            <div class="card-header">
                <i class="fas fa-chart-line me-2"></i>NGƯỜI DÙNG ĐĂNG KÝ (12 THÁNG GẦN NHẤT)
            </div>
            <div class="card-body">
                <canvas id="usersChart" height="300"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card admin-table mb-4">
            <div class="card-header">
                <i class="fas fa-chart-line me-2"></i>SẢN PHẨM TẠO MỚI (12 THÁNG GẦN NHẤT)
            </div>
            <div class="card-body">
                <canvas id="productsChart" height="300"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Top Products Tables -->
<div class="row">
    <div class="col-md-6">
        <div class="card admin-table mb-4">
            <div class="card-header">
                <i class="fas fa-arrow-up me-2"></i>TOP 10 SẢN PHẨM GIÁ CAO NHẤT
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr style="background-color: var(--bg);">
                            <th>Tên sản phẩm</th>
                            <th>Giá</th>
                            <th>Số lượng</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($topExpensiveProducts as $product)
                            <tr>
                                <td style="font-weight: 500;">{{ Str::limit($product->ten_sp, 40) }}</td>
                                <td class="text-success fw-bold">{{ number_format($product->gia) }}đ</td>
                                <td>{{ $product->so_luong }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card admin-table mb-4">
            <div class="card-header">
                <i class="fas fa-arrow-down me-2"></i>TOP 10 SẢN PHẨM GIÁ THẤP NHẤT
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr style="background-color: var(--bg);">
                            <th>Tên sản phẩm</th>
                            <th>Giá</th>
                            <th>Số lượng</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($topCheapestProducts as $product)
                            <tr>
                                <td style="font-weight: 500;">{{ Str::limit($product->ten_sp, 40) }}</td>
                                <td class="text-info fw-bold">{{ number_format($product->gia) }}đ</td>
                                <td>{{ $product->so_luong }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card admin-table mb-4">
            <div class="card-header">
                <i class="fas fa-warehouse me-2"></i>TOP 10 SẢN PHẨM TỒN KHO CAO NHẤT
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr style="background-color: var(--bg);">
                            <th>Tên sản phẩm</th>
                            <th>Giá</th>
                            <th>Số lượng tồn</th>
                            <th>Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($topStockProducts as $product)
                            <tr>
                                <td class="font-medium-custom">{{ $product->ten_sp }}</td>
                                <td>{{ number_format($product->gia) }}đ</td>
                                <td class="text-warning fw-bold">{{ $product->so_luong }}</td>
                                <td>
                                    <span class="badge-status-{{ $product->trang_thai }}">
                                        {{ $product->trang_thai === 'active' ? 'ĐANG BÁN' : 'NGỪNG BÁN' }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Users Chart
const usersCtx = document.getElementById('usersChart').getContext('2d');
const usersChart = new Chart(usersCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode($usersPerMonth->pluck('month')) !!},
        datasets: [{
            label: 'Người dùng đăng ký',
            data: {!! json_encode($usersPerMonth->pluck('total')) !!},
            borderColor: 'var(--bg-dark)',
            backgroundColor: 'rgba(17, 17, 17, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});

// Products Chart
const productsCtx = document.getElementById('productsChart').getContext('2d');
const productsChart = new Chart(productsCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode($productsPerMonth->pluck('month')) !!},
        datasets: [{
            label: 'Sản phẩm tạo mới',
            data: {!! json_encode($productsPerMonth->pluck('total')) !!},
            borderColor: '#28A745',
            backgroundColor: 'rgba(40, 167, 69, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});
</script>
@endpush
@endsection
