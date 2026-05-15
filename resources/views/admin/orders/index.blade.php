@extends('layouts.admin')

@section('title', 'Quản lý Đơn hàng')

@section('content')
<!-- <div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-shopping-cart me-3"></i>QUẢN LÝ ĐƠN HÀNG</h1>
        <a href="{{ route('admin.orders.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>TẠO ĐƠN HÀNG MỚI
        </a>
    </div>
</div> -->

<!-- Statistics -->
<div class="row mb-4">
    <div class="col-md-2">
        <div class="stat-card">
            <div class="stat-value">{{ $stats['total'] }}</div>
            <div class="stat-label">Tổng đơn</div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="stat-card">
            <div class="stat-value text-warning">{{ $stats['pending'] }}</div>
            <div class="stat-label">Chờ duyệt đơn</div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="stat-card">
            <div class="stat-value text-info">{{ $stats['confirmed'] }}</div>
            <div class="stat-label">Đã duyệt đơn</div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="stat-card">
            <div class="stat-value text-primary">{{ $stats['shipping'] }}</div>
            <div class="stat-label">Đang giao hàng</div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="stat-card">
            <div class="stat-value text-danger">{{ $stats['disputing'] }}</div>
            <div class="stat-label">Đang khiếu nại</div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="stat-card">
            <div class="stat-value text-warning" style="color: #f39c12 !important;">{{ $stats['cancelling'] }}</div>
            <div class="stat-label">Chờ duyệt hủy</div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="stat-card">
            <div class="stat-value text-success">{{ $stats['completed'] }}</div>
            <div class="stat-label">Hoàn thành</div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="stat-card">
            <div class="stat-value text-success">{{ number_format($stats['total_revenue']) }}đ</div>
            <div class="stat-label">Tổng doanh thu (Đơn hoàn thành)</div>
        </div>
    </div>
</div>

<!-- Filter Section -->
<div class="filter-section">
    <form method="GET" action="{{ route('admin.orders.index') }}" class="row g-3">
        <div class="col-md-4">
            <input type="text" name="search" class="form-control" placeholder="Tìm mã đơn, tên, SĐT..." 
                   value="{{ request('search') }}">
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="">-- Trạng thái đơn --</option>
                @foreach(\App\Models\Order::adminStatusLabels() as $val => $label)
                    <option value="{{ $val }}" {{ request('status') === $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <select name="payment_status" class="form-select">
                <option value="">-- Trạng thái thanh toán --</option>
                <option value="unpaid" {{ request('payment_status') === 'unpaid' ? 'selected' : '' }}>Chưa thanh toán</option>
                <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>Đã thanh toán</option>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100">
                <i class="fas fa-search me-2"></i>Lọc
            </button>
        </div>
    </form>
</div>

<!-- Orders Table -->
<div class="card admin-table">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Mã đơn</th>
                    <th>Khách hàng</th>
                    <th>Người nhận</th>
                    <th>Tổng tiền</th>
                    <th>Trạng thái</th>
                    <th>Thanh toán</th>
                    <th>Ngày đặt</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td style="font-weight: 500;">{{ $order->ma_don_hang }}</td>
                        <td>{{ $order->user->name }}</td>
                        <td>
                            {{ $order->ten_nguoi_nhan }}<br>
                            <small class="text-muted">{{ $order->sdt_nguoi_nhan }}</small>
                        </td>
                        <td style="font-weight: 500;">{{ number_format($order->thanh_tien) }}đ</td>
                        <td>
                            <span class="badge bg-{{ $order->status_color }}">{{ $order->status_label }}</span>
                        </td>
                        <td>
                            <span class="badge bg-{{ $order->trang_thai_thanh_toan === 'paid' ? 'success' : 'secondary' }}">
                                {{ $order->payment_status_label }}
                            </span>
                        </td>
                        <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-eye"></i> Chi tiết
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-5">
                            <i class="fas fa-shopping-cart fa-3x mb-3 d-block"></i>
                            Chưa có đơn hàng nào
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($orders->hasPages())
        <div class="card-footer">
            {{ $orders->appends(request()->query())->links('pagination.bootstrap-5') }}
        </div>
    @endif
</div>
@endsection
