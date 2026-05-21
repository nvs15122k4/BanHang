@extends('layouts.admin')

@section('title', 'Quản lý Đánh giá')

@push('styles')
    @vite(['public/css/admin_common.css'])
@endpush

@section('content')
<div class="page-header">
    <h1><i class="fas fa-star me-3"></i>QUẢN LÝ ĐÁNH GIÁ SẢN PHẨM</h1>
</div>

<!-- Statistics -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="stat-card h-100 text-center d-flex flex-column justify-content-center">
            <div class="stat-value">{{ $stats['total'] }}</div>
            <div class="stat-label">Tổng đánh giá</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card h-100 text-center d-flex flex-column justify-content-center">
            <div class="stat-value text-warning">{{ $stats['pending'] }}</div>
            <div class="stat-label">Chờ duyệt</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card h-100 text-center d-flex flex-column justify-content-center">
            <div class="stat-value text-success">{{ $stats['approved'] }}</div>
            <div class="stat-label">Đã duyệt</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card h-100 text-center d-flex flex-column justify-content-center">
            <div class="stat-value">{{ number_format($stats['avg_rating'], 1) }} ★</div>
            <div class="stat-label">Rating trung bình</div>
        </div>
    </div>
</div>

<!-- Filter Section -->
<div class="filter-section">
    <form method="GET" action="{{ route('admin.reviews.index') }}" class="row g-3">
        <div class="col-md-3">
            <input type="text" name="search" class="form-control" placeholder="Tìm theo tên user, sản phẩm..."
                   value="{{ request('search') }}">
        </div>
        <div class="col-md-2">
            <select name="status" class="form-select">
                <option value="">-- Trạng thái --</option>
                <option value="pending"  {{ request('status') === 'pending'  ? 'selected' : '' }}>Chờ duyệt</option>
                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Đã duyệt</option>
                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Từ chối</option>
            </select>
        </div>
        <div class="col-md-2">
            <select name="rating" class="form-select">
                <option value="">-- Số sao --</option>
                @for($i = 5; $i >= 1; $i--)
                    <option value="{{ $i }}" {{ request('rating') == $i ? 'selected' : '' }}>{{ $i }} sao</option>
                @endfor
            </select>
        </div>
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
            <button type="submit" class="btn btn-primary w-100">
                <i class="fas fa-search me-2"></i>Lọc
            </button>
        </div>
    </form>
</div>

<!-- Reviews Table -->
<div class="card admin-table">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Người dùng</th>
                    <th>Sản phẩm</th>
                    <th class="uix-3b33845933">Đánh giá</th>
                    <th>Nhận xét</th>
                    <th class="uix-3b33845933">Trạng thái</th>
                    <th class="uix-3b33845933">Ngày gửi</th>
                    <th class="uix-611bf920db">Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reviews as $review)
                    <tr>
                        <td>
                            <div class="font-medium-custom">{{ $review->user->name }}</div>
                            <small class="text-muted">{{ $review->user->email }}</small>
                        </td>
                        <td>
                            <div class="font-medium-custom">{{ $review->product->ten_sp }}</div>
                        </td>
                        <td>
                            <div class="text-warning-custom text-lg-custom letter-spacing-1-custom">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star{{ $i <= $review->rating ? '' : ' text-muted' }}"></i>
                                @endfor
                            </div>
                            <small class="font-medium-custom">{{ $review->rating }}/5</small>
                        </td>
                        <td>
                            <span class="text-gray-medium">
                                {{ $review->comment ? \Str::limit($review->comment, 80) : '(Không có nhận xét)' }}
                            </span>
                        </td>
                        <td>
                            @if($review->trang_thai === 'approved')
                                <span class="badge bg-success">Đã duyệt</span>
                            @elseif($review->trang_thai === 'rejected')
                                <span class="badge bg-danger">Từ chối</span>
                            @else
                                <span class="badge bg-warning text-dark">Chờ duyệt</span>
                            @endif
                        </td>
                        <td>
                            <small>{{ $review->created_at->format('d/m/Y H:i') }}</small>
                        </td>
                        <td>
                            <div class="d-flex gap-1 flex-wrap">
                                @if($review->trang_thai !== 'approved')
                                    <form method="POST" action="{{ route('admin.reviews.approve', $review) }}" class="d-inline">
                                        @csrf @method('PUT')
                                        <button type="submit" class="btn btn-sm btn-success" title="Duyệt">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                @endif
                                @if($review->trang_thai !== 'rejected')
                                    <form method="POST" action="{{ route('admin.reviews.reject', $review) }}" class="d-inline">
                                        @csrf @method('PUT')
                                        <button type="submit" class="btn btn-sm btn-warning" title="Từ chối">
                                            <i class="fas fa-ban"></i>
                                        </button>
                                    </form>
                                @endif
                                <form method="POST" action="{{ route('admin.reviews.destroy', $review) }}" class="d-inline"
                                      id="form-del-review-{{ $review->id }}">
                                    @csrf @method('DELETE')
                                    <button type="button" class="btn btn-sm btn-danger" title="Xóa"
                                            onclick="showConfirm('Đánh giá này sẽ bị xóa vĩnh viễn.', () => document.getElementById('form-del-review-{{ $review->id }}').submit(), 'Xóa đánh giá')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5">
                            <i class="fas fa-star fa-3x mb-3 d-block"></i>
                            Không tìm thấy đánh giá nào
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($reviews->hasPages())
        <div class="card-footer">
            {{ $reviews->appends(request()->query())->links('pagination.bootstrap-5') }}
        </div>
    @endif
</div>
@endsection
