@extends('layouts.admin')

@section('title', 'Audit Log')

@push('styles')
    @vite(['public/css/admin_common.css'])
@endpush

@section('content')
<div class="page-header">
    <h1><i class="fas fa-clipboard-list me-3"></i>NHẬT KÝ GẦN ĐÂY</h1>
</div>

<div class="filter-section">
    <form method="GET" action="{{ route('admin.audit-logs.index') }}" class="row g-3">
        <div class="col-md-3">
            <input type="text" name="search" class="form-control" placeholder="Tìm mô tả, model..."
                   value="{{ request('search') }}">
        </div>
        <div class="col-md-2">
            <select name="action" class="form-select">
                <option value="">-- Hành động --</option>
                @foreach($actions as $action)
                    <option value="{{ $action }}" {{ request('action') === $action ? 'selected' : '' }}>
                        {{ \App\Models\AuditLog::actionLabelFor($action) }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <select name="user_id" class="form-select">
                <option value="">-- Admin/User --</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ (string) request('user_id') === (string) $user->id ? 'selected' : '' }}>
                        {{ $user->name }} - {{ $user->email }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
        </div>
        <div class="col-md-2">
            <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
        </div>
        <div class="col-md-12 d-flex gap-2">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search me-2"></i>Lọc
            </button>
            <a href="{{ route('admin.audit-logs.index') }}" class="btn btn-outline-secondary">Xóa lọc</a>
        </div>
    </form>
</div>

<div class="card admin-table">
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle">
            <thead>
                <tr>
                    <th>Thời gian</th>
                    <th>Người thực hiện</th>
                    <th>Hành động</th>
                    <th>Đối tượng</th>
                    <th>Mô tả</th>
                    <th>Thay đổi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr>
                        <td class="text-nowrap">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            @if($log->user)
                                <strong>{{ $log->user->name }}</strong><br>
                                <small class="text-muted">{{ $log->user->email }}</small>
                            @else
                                <span class="text-muted">System</span>
                            @endif
                        </td>
                        <td><span class="badge bg-dark">{{ $log->action_label }}</span></td>
                        <td>
                            @if($log->auditable_type)
                                <span>{{ $log->auditable_label }}</span>
                            @else
                                <span class="text-muted">{{ $log->auditable_label }}</span>
                            @endif
                        </td>
                        <td>{{ $log->description_label }}</td>
                        <td class="small">
                            @if(count($log->change_rows))
                                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#auditLogDetailModal{{ $log->id }}">
                                    <i class="fas fa-eye me-1"></i>Xem chi tiết
                                </button>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-5">
                            <i class="fas fa-clipboard-list fa-3x mb-3 d-block"></i>
                            Chưa có audit log nào
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($logs->hasPages())
        <div class="card-footer bg-gray-light-custom">
            {{ $logs->links('pagination.bootstrap-5') }}
        </div>
    @endif
</div>

@foreach($logs as $log)
    @if(count($log->change_rows))
        <div class="modal fade admin-modal" id="auditLogDetailModal{{ $log->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content admin-modal-content">
                    <div class="modal-header admin-modal-header">
                        <h5 class="modal-title admin-modal-title">Chi tiết thay đổi</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <div class="text-muted small">Thời gian</div>
                                <div class="fw-semibold">{{ $log->created_at->format('d/m/Y H:i') }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted small">Người thực hiện</div>
                                <div class="fw-semibold">{{ $log->user ? $log->user->name : 'System' }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted small">Hành động</div>
                                <div><span class="badge bg-dark">{{ $log->action_label }}</span></div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted small">Đối tượng</div>
                                <div class="fw-semibold">{{ $log->auditable_label }}</div>
                            </div>
                            <div class="col-12">
                                <div class="text-muted small">Mô tả</div>
                                <div>{{ $log->description_label }}</div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-sm table-bordered mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Trường</th>
                                        <th>Trước</th>
                                        <th>Sau</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($log->change_rows as $change)
                                        <tr>
                                            <td>{{ $change['field'] }}</td>
                                            <td>{{ $change['old'] ?? '-' }}</td>
                                            <td>{{ $change['new'] ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer admin-modal-footer">
                        <button type="button" class="btn btn-secondary admin-btn-rounded" data-bs-dismiss="modal">Đóng</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endforeach
@endsection
