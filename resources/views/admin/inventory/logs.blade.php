@extends('layouts.admin')

@section('title', 'Lịch sử xuất nhập kho')

@push('styles')
    @vite(['resources/css/admin_common.css'])
@endpush

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-history me-3"></i>LỊCH SỬ XUẤT NHẬP KHO</h1>
        <a href="{{ route('admin.inventory.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Quay lại kho
        </a>
    </div>
</div>

<!-- Filter -->
<div class="filter-section">
    <form method="GET" action="{{ route('admin.inventory.logs') }}" class="row g-3">
        <div class="col-md-3">
            <select name="product_id" class="form-select">
                <option value="">-- Tất cả sản phẩm --</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>
                        {{ $product->ten_sp }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <select name="loai" class="form-select">
                <option value="">-- Loại giao dịch --</option>
                <option value="in" {{ request('loai') === 'in' ? 'selected' : '' }}>Nhập kho</option>
                <option value="out" {{ request('loai') === 'out' ? 'selected' : '' }}>Xuất kho</option>
                <option value="adjust" {{ request('loai') === 'adjust' ? 'selected' : '' }}>Điều chỉnh</option>
            </select>
        </div>
        <div class="col-md-2">
            <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}" placeholder="Từ ngày">
        </div>
        <div class="col-md-2">
            <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}" placeholder="Đến ngày">
        </div>
        <div class="col-md-3">
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1">
                    <i class="fas fa-search me-2"></i>Lọc
                </button>
                <a href="{{ route('admin.inventory.logs') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </div>
    </form>
</div>

<!-- Logs Table -->
<div class="card admin-table">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Thời gian</th>
                    <th>Sản phẩm</th>
                    <th>Loại</th>
                    <th>Trước</th>
                    <th>Thay đổi</th>
                    <th>Sau</th>
                    <th>Lý do</th>
                    <th>Đơn hàng</th>
                    <th>Người thực hiện</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr>
                        <td>{{ $log->created_at->format('d/m/Y H:i') }}</td>
                        <td class="font-medium-custom">{{ $log->product->ten_sp ?? 'N/A' }}</td>
                        <td>
                            @if($log->loai === 'in')
                                <span class="badge bg-success">Nhập kho</span>
                            @elseif($log->loai === 'out')
                                <span class="badge bg-warning text-dark">Xuất kho</span>
                            @else
                                <span class="badge bg-info">Điều chỉnh</span>
                            @endif
                        </td>
                        <td>{{ $log->so_luong_truoc }}</td>
                        <td>
                            <span class="{{ $log->so_luong_thay_doi > 0 ? 'text-success' : 'text-danger' }} fw-bold">
                                {{ $log->so_luong_thay_doi > 0 ? '+' : '' }}{{ $log->so_luong_thay_doi }}
                            </span>
                        </td>
                        <td class="font-medium-custom">{{ $log->so_luong_sau }}</td>
                        <td>{{ $log->ly_do }}</td>
                        <td>
                            @if($log->order)
                                <a href="{{ route('admin.orders.show', $log->order) }}" class="text-primary">
                                    {{ $log->order->ma_don_hang }}
                                </a>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>{{ $log->user->name ?? 'Hệ thống' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-5">
                            <i class="fas fa-history fa-3x mb-3 d-block"></i>
                            Chưa có lịch sử xuất nhập kho
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($logs->hasPages())
        <div class="card-footer bg-gray-light-custom">
            {{ $logs->appends(request()->query())->links('pagination.bootstrap-5') }}
        </div>
    @endif
</div>
@endsection
