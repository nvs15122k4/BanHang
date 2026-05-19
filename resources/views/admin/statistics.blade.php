@extends('layouts.admin')

@section('title', 'Thống kê hệ thống')

@push('styles')
    @vite(['resources/css/admin_common.css'])
@endpush

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-chart-line me-3"></i>THỐNG KÊ HỆ THỐNG</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('export.statistics') }}" class="btn btn-primary btn-sm rounded-pill px-3">
                <i class="fas fa-file-export me-2"></i>XUẤT BÁO CÁO
            </a>
        </div>
    </div>
</div>

<!-- Overview Stats -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon uix-4ab3c9f677">
                <i class="fas fa-wallet"></i>
            </div>
            <div class="mt-3">
                <div class="stat-value">{{ number_format($orderStats['revenue']) }}đ</div>
                <div class="stat-label">Doanh thu (Đã xong)</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon uix-1b8f34875f">
                <i class="fas fa-shopping-bag"></i>
            </div>
            <div class="mt-3">
                <div class="stat-value">{{ $orderStats['total'] }}</div>
                <div class="stat-label">Tổng đơn hàng</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon uix-790a3823c9">
                <i class="fas fa-box"></i>
            </div>
            <div class="mt-3">
                <div class="stat-value">{{ $productStats['total'] }}</div>
                <div class="stat-label">Tổng sản phẩm</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon uix-0bc5cea11c">
                <i class="fas fa-users"></i>
            </div>
            <div class="mt-3">
                <div class="stat-value">{{ $userStats['total'] }}</div>
                <div class="stat-label">Tổng người dùng</div>
            </div>
        </div>
    </div>
</div>

<!-- Monthly Trends -->
<div class="row">
    <div class="col-lg-8">
        <div class="chart-card">
            <div class="chart-header">
                <h3 class="chart-title"><i class="fas fa-chart-area me-2"></i>Xu hướng doanh thu (12 Tháng)</h3>
            </div>
            <div class="chart-container">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="chart-card">
            <div class="chart-header">
                <h3 class="chart-title"><i class="fas fa-chart-pie me-2"></i>Trạng thái đơn hàng</h3>
            </div>
            <div class="chart-container">
                <canvas id="orderStatusPie"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="chart-card">
            <div class="chart-header">
                <h3 class="chart-title"><i class="fas fa-users me-2"></i>Người dùng mới</h3>
            </div>
            <div class="chart-container">
                <canvas id="usersChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="chart-card">
            <div class="chart-header">
                <h3 class="chart-title"><i class="fas fa-box-open me-2"></i>Phân loại sản phẩm</h3>
            </div>
            <div class="chart-container">
                <canvas id="categoryPie"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Tables -->
<div class="row">
    <div class="col-md-6">
        <div class="card admin-table">
            <div class="card-header">
                <i class="fas fa-star me-2"></i>SẢN PHẨM GIÁ CAO NHẤT
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Sản phẩm</th>
                            <th class="text-end">Giá</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($topExpensiveProducts as $product)
                            <tr>
                                <td>{{ Str::limit($product->ten_sp, 40) }}</td>
                                <td class="text-end fw-bold">{{ number_format($product->gia) }}đ</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card admin-table">
            <div class="card-header">
                <i class="fas fa-exclamation-triangle me-2"></i>TỒN KHO THẤP
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Sản phẩm</th>
                            <th class="text-end">Kho</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($topStockProducts->where('so_luong', '<', 10) as $product)
                            <tr>
                                <td>{{ Str::limit($product->ten_sp, 40) }}</td>
                                <td class="text-end text-danger fw-bold">{{ $product->so_luong }}</td>
                            </tr>
                        @endforeach
                        @if($topStockProducts->where('so_luong', '<', 10)->isEmpty())
                            <tr>
                                <td colspan="2" class="text-center text-muted py-3">Không có sản phẩm nào sắp hết hàng</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const charts = {};

    function initChart(id, type, labels, data, label, color) {
        const ctx = document.getElementById(id).getContext('2d');
        const gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, color.replace('1)', '0.2)'));
        gradient.addColorStop(1, color.replace('1)', '0)'));

        charts[id] = new Chart(ctx, {
            type: type,
            data: {
                labels: labels,
                datasets: [{
                    label: label,
                    data: data,
                    borderColor: color,
                    backgroundColor: gradient,
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointRadius: 4,
                    pointBackgroundColor: color,
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        backgroundColor: 'rgba(255, 255, 255, 0.9)',
                        titleColor: '#111',
                        bodyColor: '#666',
                        borderColor: 'rgba(0,0,0,0.1)',
                        borderWidth: 1,
                        padding: 10,
                        displayColors: false
                    }
                },
                scales: {
                    y: { 
                        beginAtZero: true,
                        grid: { color: 'rgba(0,0,0,0.05)', drawBorder: false },
                        ticks: { font: { size: 11 } }
                    },
                    x: { 
                        grid: { display: false },
                        ticks: { font: { size: 11 } }
                    }
                }
            }
        });
    }

    function initPieChart(id, labels, data, colors) {
        const ctx = document.getElementById(id).getContext('2d');
        charts[id] = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: colors,
                    borderWidth: 0,
                    hoverOffset: 10
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: { size: 11, weight: '600' }
                        }
                    }
                },
                cutout: '70%'
            }
        });
    }


    // Initialize Charts
    document.addEventListener('DOMContentLoaded', function() {
        // Revenue Chart
        initChart('revenueChart', 'line', 
            {!! json_encode($revenuePerMonth->pluck('month')) !!}, 
            {!! json_encode($revenuePerMonth->pluck('total')) !!}, 
            'Doanh thu', '#7c3aed');

        // Users Chart
        initChart('usersChart', 'line', 
            {!! json_encode($usersPerMonth->pluck('month')) !!}, 
            {!! json_encode($usersPerMonth->pluck('total')) !!}, 
            'Người dùng mới', '#3b82f6');

        // Category Pie
        initPieChart('categoryPie', 
            {!! json_encode($categoryDistribution->pluck('loai')) !!}, 
            {!! json_encode($categoryDistribution->pluck('total')) !!}, 
            ['#7c3aed', '#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#6366f1']);

        // Order Status Pie
        initPieChart('orderStatusPie', 
            {!! json_encode($orderStatusDistribution->pluck('trang_thai')->map(fn($s) => \App\Models\Order::adminStatusLabels()[$s] ?? $s)) !!}, 
            {!! json_encode($orderStatusDistribution->pluck('total')) !!}, 
            ['#f59e0b', '#3b82f6', '#10b981', '#ef4444', '#6366f1', '#64748b']);
    });
</script>
@endpush
@endsection
