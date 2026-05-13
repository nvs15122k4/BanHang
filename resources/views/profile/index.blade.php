@extends('layouts.app')

@section('title', 'Tài khoản của tôi - AVA')

@push('styles')
<style>
    /* =========================================
       PROFILE PAGE - AVA STYLE
       ========================================= */
    .page-title {
        font-weight: 700;
        font-size: 32px;
        color: var(--text-main);
        text-align: center;
        margin: 40px 0;
        text-transform: uppercase;
    }

    .profile-sidebar {
        background: #F6F6F6;
        padding: 30px;
        position: sticky;
        top: 40px;
    }

    .sidebar-title {
        font-weight: 700;
        font-size: 18px;
        text-transform: uppercase;
        margin-bottom: 20px;
        border-bottom: 2px solid #DDDDDD;
        padding-bottom: 10px;
    }

    .list-group-item {
        background: transparent;
        border: none;
        color: var(--text-light);
        padding: 12px 15px;
        border-radius: 0 !important;
        margin-bottom: 5px;
        font-weight: 500;
        transition: all 0.3s;
    }
    
    .list-group-item:hover {
        background: rgba(0,0,0,0.05);
        color: var(--text-main);
    }
    
    .list-group-item.active {
        background: var(--text-main);
        color: #FFFFFF;
        font-weight: 600;
    }

    .profile-card {
        border: 1px solid #EEEEEE;
        padding: 40px;
    }

    .card-title {
        font-weight: 700;
        font-size: 24px;
        text-transform: uppercase;
        margin-bottom: 30px;
        border-bottom: 1px solid #EEEEEE;
        padding-bottom: 15px;
    }

    .form-control, .form-select {
        border-radius: 0;
        border: 1px solid #DDDDDD;
        padding: 12px 15px;
    }
    
    .form-label {
        font-weight: 600;
        color: var(--text-main);
        font-size: 14px;
        text-transform: uppercase;
    }

    .address-card {
        border: 1px solid #EEEEEE;
        padding: 20px;
        margin-bottom: 20px;
        transition: all 0.3s;
    }
    
    .address-card:hover {
        border-color: #CCCCCC;
    }
    
    .address-card.border-primary {
        border-color: var(--text-main) !important;
        background: #F9F9F9;
    }

    .btn-action {
        color: var(--text-main);
        font-weight: 600;
        text-decoration: underline;
        font-size: 14px;
        background: none;
        border: none;
        padding: 0;
    }
    
    .btn-action:hover {
        color: var(--primary);
    }

    /* Modal */
    .modal-content {
        border-radius: 0;
        border: none;
    }
    
    .modal-header {
        background: #F6F6F6;
        border-radius: 0;
        border-bottom: 1px solid #EEEEEE;
    }
    
    .modal-title {
        font-weight: 700;
        text-transform: uppercase;
    }
</style>
@endpush

@section('content')
<div class="container py-4">
    <h1 class="page-title">My Account</h1>
    
    @if(session('success'))
        <div class="alert alert-success rounded-0 border-0" style="background-color: #E8F5E9; color: #2E7D32;">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger rounded-0 border-0" style="background-color: #FFEBEE; color: #C62828;">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        </div>
    @endif

    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 mb-4">
            <div class="profile-sidebar">
                <div class="sidebar-title">QUẢN LÝ TÀI KHOẢN</div>
                <div class="list-group list-group-flush">
                    <a href="#profile" class="list-group-item list-group-item-action active" data-bs-toggle="pill">
                        Thông tin cá nhân
                    </a>
                    <a href="#password" class="list-group-item list-group-item-action" data-bs-toggle="pill">
                        Đổi mật khẩu
                    </a>
                    <a href="#addresses" class="list-group-item list-group-item-action" data-bs-toggle="pill">
                        Sổ địa chỉ
                    </a>
                    <a href="{{ route('orders.index') }}" class="list-group-item list-group-item-action">
                        Đơn hàng của tôi
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-9">
            <div class="tab-content">
                <!-- Profile Tab -->
                <div class="tab-pane fade show active" id="profile">
                    <div class="profile-card">
                        <h4 class="card-title">Thông tin cá nhân</h4>
                        
                        <form method="POST" action="{{ route('profile.update') }}">
                            @csrf
                            @method('PUT')

                            <div class="row mb-3">
                                <label class="col-md-3 col-form-label form-label">Họ và tên <span class="text-danger">*</span></label>
                                <div class="col-md-9">
                                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                           value="{{ old('name', $user->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-md-3 col-form-label form-label">Email <span class="text-danger">*</span></label>
                                <div class="col-md-9">
                                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                                           value="{{ old('email', $user->email) }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-md-3 col-form-label form-label">Số điện thoại</label>
                                <div class="col-md-9">
                                    <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" 
                                           value="{{ old('phone', $user->phone) }}">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-4">
                                <label class="col-md-3 col-form-label form-label">Giới tính</label>
                                <div class="col-md-9">
                                    <select name="gender" class="form-select @error('gender') is-invalid @enderror">
                                        <option value="">Chọn giới tính</option>
                                        <option value="male" {{ old('gender', $user->gender) == 'male' ? 'selected' : '' }}>Nam</option>
                                        <option value="female" {{ old('gender', $user->gender) == 'female' ? 'selected' : '' }}>Nữ</option>
                                        <option value="other" {{ old('gender', $user->gender) == 'other' ? 'selected' : '' }}>Khác</option>
                                    </select>
                                    @error('gender')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-9 offset-md-3">
                                    <button type="submit" class="btn btn-ava-dark px-5">LƯU THAY ĐỔI</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Password Tab -->
                <div class="tab-pane fade" id="password">
                    <div class="profile-card">
                        <h4 class="card-title">Đổi mật khẩu</h4>
                        
                        <form method="POST" action="{{ route('profile.password') }}">
                            @csrf
                            @method('PUT')

                            <div class="row mb-3">
                                <label class="col-md-4 col-form-label form-label">Mật khẩu hiện tại <span class="text-danger">*</span></label>
                                <div class="col-md-8">
                                    <input type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" required>
                                    @error('current_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-md-4 col-form-label form-label">Mật khẩu mới <span class="text-danger">*</span></label>
                                <div class="col-md-8">
                                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-4">
                                <label class="col-md-4 col-form-label form-label">Xác nhận mật khẩu mới <span class="text-danger">*</span></label>
                                <div class="col-md-8">
                                    <input type="password" name="password_confirmation" class="form-control" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-8 offset-md-4">
                                    <button type="submit" class="btn btn-ava-dark px-5">CẬP NHẬT MẬT KHẨU</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Addresses Tab -->
                <div class="tab-pane fade" id="addresses">
                    <div class="profile-card">
                        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                            <h4 class="card-title mb-0 border-0 pb-0">Sổ địa chỉ</h4>
                            <button class="btn btn-ava btn-sm" data-bs-toggle="modal" data-bs-target="#addAddressModal">
                                + THÊM ĐỊA CHỈ MỚI
                            </button>
                        </div>

                        <div class="row">
                            @forelse($addresses as $address)
                                <div class="col-md-6">
                                    <div class="address-card {{ $address->is_default ? 'border-primary' : '' }}">
                                        @if($address->is_default)
                                            <span class="badge bg-dark mb-2 rounded-0">Mặc định</span>
                                        @endif
                                        <h5 class="fw-bold mb-1">{{ $address->ten_nguoi_nhan }}</h5>
                                        <p class="text-muted mb-2"><i class="fas fa-phone-alt me-2"></i>{{ $address->sdt_nguoi_nhan }}</p>
                                        <p class="mb-3">{{ $address->dia_chi_chi_tiet }}</p>
                                        
                                        <div class="d-flex gap-3 pt-3 border-top">
                                            <button class="btn-action" data-bs-toggle="modal" data-bs-target="#editAddressModal{{ $address->id }}">SỬA</button>
                                            
                                            <form action="{{ route('profile.addresses.destroy', $address->id) }}" method="POST" class="d-inline">
                                                @csrf @method('DELETE')
                                                <button type="button" class="btn-action text-danger" onclick="if(confirm('Bạn có chắc chắn muốn xóa địa chỉ này?')) this.form.submit()">XÓA</button>
                                            </form>
                                            @if(!$address->is_default)
                                                <form action="{{ route('profile.addresses.default', $address->id) }}" method="POST" class="ms-auto">
                                                    @csrf @method('PUT')
                                                    <button type="submit" class="btn-action" style="color: var(--text-main);">ĐẶT LÀM MẶC ĐỊNH</button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Edit Address Modal -->
                                <div class="modal fade" id="editAddressModal{{ $address->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Sửa địa chỉ</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="{{ route('profile.addresses.update', $address->id) }}" method="POST">
                                                @csrf @method('PUT')
                                                <div class="modal-body p-4">
                                                    <div class="mb-3">
                                                        <label class="form-label">Họ và tên</label>
                                                        <input type="text" name="ten_nguoi_nhan" class="form-control" value="{{ $address->ten_nguoi_nhan }}" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Số điện thoại</label>
                                                        <input type="text" name="sdt_nguoi_nhan" class="form-control" value="{{ $address->sdt_nguoi_nhan }}" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Địa chỉ chi tiết</label>
                                                        <input type="text" name="dia_chi_chi_tiet" class="form-control" value="{{ $address->dia_chi_chi_tiet }}" required>
                                                    </div>
                                                    <div class="form-check mt-3">
                                                        <input class="form-check-input" type="checkbox" name="is_default" id="is_default_{{ $address->id }}" {{ $address->is_default ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="is_default_{{ $address->id }}">Đặt làm địa chỉ mặc định</label>
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-0 pb-4 pe-4">
                                                    <button type="button" class="btn btn-outline-dark rounded-0" data-bs-dismiss="modal">HỦY BỎ</button>
                                                    <button type="submit" class="btn btn-ava-dark">LƯU THAY ĐỔI</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12 text-center py-5">
                                    <i class="fas fa-map-marker-alt fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">Bạn chưa lưu địa chỉ nào.</h5>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Address Modal -->
<div class="modal fade" id="addAddressModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thêm địa chỉ mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('profile.addresses.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label">Họ và tên <span class="text-danger">*</span></label>
                        <input type="text" name="ten_nguoi_nhan" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                        <input type="text" name="sdt_nguoi_nhan" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Địa chỉ chi tiết <span class="text-danger">*</span></label>
                        <input type="text" name="dia_chi_chi_tiet" class="form-control" placeholder="Số nhà, tên đường, phường/xã, quận/huyện, tỉnh/thành phố" required>
                    </div>
                    <div class="form-check mt-3">
                        <input class="form-check-input" type="checkbox" name="is_default" id="is_default_new">
                        <label class="form-check-label" for="is_default_new">Đặt làm địa chỉ mặc định</label>
                    </div>
                </div>
                <div class="modal-footer border-0 pb-4 pe-4">
                    <button type="button" class="btn btn-outline-dark rounded-0" data-bs-dismiss="modal">HỦY BỎ</button>
                    <button type="submit" class="btn btn-ava-dark">LƯU ĐỊA CHỈ</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
