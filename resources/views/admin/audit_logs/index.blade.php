@extends('layouts.admin')

@section('title', 'Audit Log')

@push('styles')
    @vite(['public/css/admin_common.css'])
@endpush

@section('content')
<div class="page-header">
    <h1><i class="fas fa-clipboard-list me-3"></i>AUDIT LOG</h1>
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
                    <option value="{{ $action }}" {{ request('action') === $action ? 'selected' : '' }}>{{ $action }}</option>
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
                        <td><span class="badge bg-dark">{{ $log->action }}</span></td>
                        <td>
                            @if($log->auditable_type)
                                <code>{{ class_basename($log->auditable_type) }}#{{ $log->auditable_id }}</code>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>{{ $log->description }}</td>
                        <td class="small">
                            @if($log->old_values || $log->new_values)
                                <details>
                                    <summary>Xem dữ liệu</summary>
                                    @if($log->old_values)
                                        <div class="mt-2">
                                            <strong>Trước:</strong>
                                            <pre class="bg-light p-2 mb-2">{{ json_encode($log->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                        </div>
                                    @endif
                                    @if($log->new_values)
                                        <div>
                                            <strong>Sau:</strong>
                                            <pre class="bg-light p-2 mb-0">{{ json_encode($log->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                        </div>
                                    @endif
                                </details>
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
@endsection
