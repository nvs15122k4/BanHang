@extends('layouts.admin')

@section('title', 'Quản lý Users')

@push('styles')
    @vite(['resources/css/admin_common.css'])
@endpush

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-users me-3"></i>QUẢN LÝ USERS</h1>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createUserModal">
            <i class="fas fa-plus me-2"></i>TẠO USER MỚI
        </button>
    </div>
</div>

<!-- Filter Section -->
<div class="filter-section">
    <form method="GET" action="{{ route('admin.users') }}" class="row g-3">
        <div class="col-md-3">
            <input type="text" name="search" class="form-control" placeholder="Tìm theo tên, email, SĐT..."
                   value="{{ request('search') }}">
        </div>
        <div class="col-md-2">
            <select name="role" class="form-select">
                <option value="">-- Vai trò --</option>
                <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="user"  {{ request('role') === 'user'  ? 'selected' : '' }}>User</option>
            </select>
        </div>
        <div class="col-md-2">
            <select name="status" class="form-select">
                <option value="">-- Trạng thái --</option>
                <option value="active"   {{ request('status') === 'active'   ? 'selected' : '' }}>Đang hoạt động</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Vô hiệu hóa</option>
            </select>
        </div>
        <div class="col-md-2">
            <select name="sort_by" class="form-select">
                <option value="created_at" {{ request('sort_by') === 'created_at' ? 'selected' : '' }}>Ngày tạo</option>
                <option value="name"       {{ request('sort_by') === 'name'       ? 'selected' : '' }}>Tên</option>
                <option value="email"      {{ request('sort_by') === 'email'      ? 'selected' : '' }}>Email</option>
            </select>
        </div>
        <div class="col-md-1">
            <select name="sort_order" class="form-select">
                <option value="desc" {{ request('sort_order') === 'desc' ? 'selected' : '' }}>↓</option>
                <option value="asc"  {{ request('sort_order') === 'asc'  ? 'selected' : '' }}>↑</option>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100">
                <i class="fas fa-search me-2"></i>Lọc
            </button>
        </div>
    </form>
</div>

<!-- Users Table -->
<div class="card admin-table">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tên</th>
                    <th>Email</th>
                    <th>Số điện thoại</th>
                    <th>Vai trò</th>
                    <th>Trạng thái</th>
                    <th>Ngày tạo</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr class="{{ !$user->is_active ? 'table-secondary' : '' }}">
                        <td>{{ $user->id }}</td>
                        <td class="font-medium-custom {{ !$user->is_active ? 'text-gray-custom' : '' }}">
                            {{ $user->name }}
                            @if(!$user->is_active)
                                <small class="text-gray-custom">(vô hiệu)</small>
                            @endif
                        </td>
                        <td class="{{ !$user->is_active ? 'text-gray-custom' : '' }}">{{ $user->email }}</td>
                        <td class="{{ !$user->is_active ? 'text-gray-custom' : '' }}">{{ $user->phone ?? '-' }}</td>
                        <td>
                            <span class="badge-role-{{ $user->role }}">
                                {{ $user->role === 'admin' ? 'ADMIN' : 'USER' }}
                            </span>
                        </td>
                        <td>
                            @if($user->is_active)
                                <span class="badge-status-active">
                                    <i class="fas fa-check-circle me-1"></i>Hoạt động
                                </span>
                            @else
                                <span class="badge-status-inactive">
                                    <i class="fas fa-ban me-1"></i>Vô hiệu hóa
                                </span>
                            @endif
                        </td>
                        <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            @if($user->id !== auth()->id())
                                <div class="d-flex gap-2">
                                    {{-- Đổi vai trò --}}
                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                            onclick="changeRole({{ $user->id }}, '{{ $user->role }}', '{{ addslashes($user->name) }}')"
                                            title="Đổi vai trò">
                                        <i class="fas fa-user-tag"></i>
                                    </button>

                                    {{-- Vô hiệu hóa / Mở lại --}}
                                    <form method="POST" action="{{ route('admin.users.status', $user) }}" class="d-inline"
                                          id="form-status-{{ $user->id }}">
                                        @csrf @method('PUT')
                                        @if($user->is_active)
                                            <button type="button" class="btn btn-sm btn-outline-warning"
                                                    title="Vô hiệu hóa tài khoản"
                                                    onclick="showConfirm(
                                                        'Tài khoản &quot;{{ addslashes($user->name) }}&quot; sẽ bị vô hiệu hóa. Người dùng sẽ không thể đăng nhập.',
                                                        () => document.getElementById('form-status-{{ $user->id }}').submit(),
                                                        'Vô hiệu hóa tài khoản'
                                                    )">
                                                <i class="fas fa-ban"></i>
                                            </button>
                                        @else
                                            <button type="button" class="btn btn-sm btn-outline-success"
                                                    title="Mở lại tài khoản"
                                                    onclick="document.getElementById('form-status-{{ $user->id }}').submit()">
                                                <i class="fas fa-check-circle"></i>
                                            </button>
                                        @endif
                                    </form>
                                </div>
                            @else
                                <span class="badge-status-inactive">BẠN</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-5">
                            <i class="fas fa-users fa-3x mb-3 d-block"></i>
                            Không tìm thấy người dùng nào
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($users->hasPages())
        <div class="card-footer bg-gray-light-custom">
            {{ $users->appends(request()->query())->links('pagination.bootstrap-5') }}
        </div>
    @endif
</div>

<!-- Create User Modal -->
<div class="modal fade" id="createUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content glass-modal-custom">
            <div class="modal-header border-bottom-1-px-rgba-custom">
                <h5 class="modal-title font-bold font-outfit-custom">TẠO USER MỚI</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.users.create') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Họ và tên <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mật khẩu <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control" required>
                        <small class="text-muted">Tối thiểu 8 ký tự, có chữ hoa và chữ thường</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Số điện thoại</label>
                        <input type="text" name="phone" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Vai trò <span class="text-danger">*</span></label>
                        <select name="role" class="form-select" required>
                            <option value="user">User</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-top-1-px-rgba-custom">
                    <button type="button" class="btn btn-secondary rounded-12-px-custom px-15-rem-custom py-05-rem-custom" data-bs-dismiss="modal">HỦY</button>
                    <button type="submit" class="btn btn-primary rounded-12-px-custom px-15-rem-custom py-05-rem-custom">TẠO USER</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Change Role Modal -->
<div class="modal fade" id="changeRoleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content glass-modal-custom">
            <div class="modal-header border-bottom-1-px-rgba-custom">
                <h5 class="modal-title font-bold font-outfit-custom">THAY ĐỔI VAI TRÒ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="changeRoleForm">
                @csrf @method('PUT')
                <div class="modal-body">
                    <p>Thay đổi vai trò của: <strong id="roleUserName"></strong></p>
                    <div class="mb-3">
                        <label class="form-label">Vai trò mới</label>
                        <select name="role" id="newRole" class="form-select" required>
                            <option value="user">User</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-top-1-px-rgba-custom">
                    <button type="button" class="btn btn-secondary rounded-12-px-custom px-15-rem-custom py-05-rem-custom" data-bs-dismiss="modal">HỦY</button>
                    <button type="submit" class="btn btn-primary rounded-12-px-custom px-15-rem-custom py-05-rem-custom">CẬP NHẬT</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function changeRole(userId, currentRole, userName) {
    document.getElementById('changeRoleForm').action = `/admin/users/${userId}/role`;
    document.getElementById('roleUserName').textContent = userName;
    document.getElementById('newRole').value = currentRole === 'admin' ? 'user' : 'admin';
    new bootstrap.Modal(document.getElementById('changeRoleModal')).show();
}
</script>
@endpush
@endsection
