@extends('layouts.app')

@section('title', 'Tài khoản của tôi - Sàn Tím Vi En')
@section('robots', 'noindex, nofollow')

@push('styles')
@vite(['public/css/views/profile.css'])
@endpush

@section('content')
<div class="container py-4">
    <h1 class="page-title">Tài khoản của tôi</h1>

    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 mb-4">
            <div class="profile-sidebar">
                <div class="sidebar-title">Quản lý tài khoản</div>
                <nav class="sidebar-nav nav flex-column">
                    <a class="nav-link active" href="#profile" data-bs-toggle="pill">Thông tin cá nhân</a>
                    <a class="nav-link" href="#password" data-bs-toggle="pill">Đổi mật khẩu</a>
                    <a class="nav-link" href="#addresses" data-bs-toggle="pill">Sổ địa chỉ</a>
                    <a class="nav-link" href="{{ route('orders.index') }}">Đơn hàng của tôi</a>
                </nav>
            </div>
        </div>

        <!-- Content -->
        <div class="col-md-9">
            <div class="tab-content">

                {{-- ── TAB: THÔNG TIN CÁ NHÂN ── --}}
                <div class="tab-pane fade show active" id="profile">
                    <div class="profile-card">
                        <h4 class="card-section-title">Thông tin cá nhân</h4>

                        @php
                            $defaultAvatar = \App\Models\User::DEFAULT_AVATAR_URL;
                            $selectedAvatar = $user->avatar ?: $defaultAvatar;
                        @endphp

                        <form id="profileForm">
                            @csrf
                            <div class="row mb-3">
                                <label class="col-md-3 col-form-label form-label">Avatar</label>
                                <div class="col-md-9 avatar-field">
                                    <input type="hidden" name="avatar" id="userAvatar" value="{{ $selectedAvatar }}">
                                    <div class="avatar-compact">
                                        <img src="{{ $selectedAvatar }}" alt="Avatar hiện tại" id="avatarPreview" loading="lazy" decoding="async" onerror="this.onerror=null; this.src='{{ $defaultAvatar }}';">
                                        <button type="button" class="btn-link-action" data-bs-toggle="modal" data-bs-target="#avatarModal">Chọn avatar</button>
                                    </div>
                                    <div class="invalid-feedback-field text-danger small mt-1"></div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-md-3 col-form-label form-label">Họ và tên <span class="text-danger">*</span></label>
                                <div class="col-md-9">
                                    <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
                                    <div class="invalid-feedback-field text-danger small mt-1"></div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-md-3 col-form-label form-label">Email <span class="text-danger">*</span></label>
                                <div class="col-md-9">
                                    <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
                                    <div class="invalid-feedback-field text-danger small mt-1"></div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-md-3 col-form-label form-label">Số điện thoại tài khoản</label>
                                <div class="col-md-9">
                                    <input type="text" name="phone" class="form-control" value="{{ $user->phone }}" placeholder="Số điện thoại liên hệ của bạn">
                                    <div class="form-text text-muted text-sm-custom">Số điện thoại cá nhân, khác với số điện thoại nhận hàng.</div>
                                    <div class="invalid-feedback-field text-danger small mt-1"></div>
                                </div>
                            </div>
                            <div class="row mb-4">
                                <label class="col-md-3 col-form-label form-label">Giới tính</label>
                                <div class="col-md-9">
                                    <select name="gender" class="form-select">
                                        <option value="">Chọn giới tính</option>
                                        <option value="male" {{ $user->gender == 'male'   ? 'selected' : '' }}>Nam</option>
                                        <option value="female" {{ $user->gender == 'female' ? 'selected' : '' }}>Nữ</option>
                                        <option value="other" {{ $user->gender == 'other'  ? 'selected' : '' }}>Khác</option>
                                    </select>
                                </div>
                            </div>

                            {{-- Height --}}
                            <div class="row mb-3">
                                <label class="col-md-3 col-form-label form-label">Chiều cao</label>
                                <div class="col-md-9">
                                    <div class="input-group">
                                        <input type="number" name="height" class="form-control" value="{{ $user->height }}" placeholder="Nhập chiều cao" min="100" max="300">
                                        <span class="input-group-text">cm</span>
                                    </div>
                                    <div class="form-text text-muted text-sm-custom">Chiều cao của bạn (từ 100-300cm) để gợi ý size sản phẩm phù hợp</div>
                                    <div class="invalid-feedback-field text-danger small mt-1"></div>
                                </div>
                            </div>

                            {{-- Weight --}}
                            <div class="row mb-4">
                                <label class="col-md-3 col-form-label form-label">Cân nặng</label>
                                <div class="col-md-9">
                                    <div class="input-group">
                                        <input type="number" name="weight" step="0.1" class="form-control" value="{{ $user->weight }}" placeholder="Nhập cân nặng" min="20" max="300">
                                        <span class="input-group-text">kg</span>
                                    </div>
                                    <div class="form-text text-muted text-sm-custom">Cân nặng của bạn (từ 20-300kg) để gợi ý size sản phẩm phù hợp</div>
                                    <div class="invalid-feedback-field text-danger small mt-1"></div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-9 offset-md-3">
                                    <button type="submit" class="btn-save" id="profileBtn">LƯU THAY ĐỔI</button>
                                    <div class="form-feedback" id="profileFeedback"></div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- ── TAB: ĐỔI MẬT KHẨU ── --}}
                <div class="tab-pane fade" id="password">
                    <div class="profile-card">
                        <h4 class="card-section-title">Đổi mật khẩu</h4>

                        <form id="passwordForm">
                            @csrf
                            <div class="row mb-3">
                                <label class="col-md-4 col-form-label form-label">Mật khẩu hiện tại <span class="text-danger">*</span></label>
                                <div class="col-md-8">
                                    <input type="password" name="current_password" class="form-control" required>
                                    <div class="invalid-feedback-field text-danger small mt-1"></div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-md-4 col-form-label form-label">Mật khẩu mới <span class="text-danger">*</span></label>
                                <div class="col-md-8">
                                    <input type="password" name="password" class="form-control" required>
                                    <div class="invalid-feedback-field text-danger small mt-1"></div>
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
                                    <button type="submit" class="btn-save" id="passwordBtn">CẬP NHẬT MẬT KHẨU</button>
                                    <div class="form-feedback" id="passwordFeedback"></div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- ── TAB: SỔ ĐỊA CHỈ ── --}}
                <div class="tab-pane fade" id="addresses">
                    <div class="profile-card">
                        <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                            <h4 class="card-section-title mb-0 border-0 pb-0">Sổ địa chỉ</h4>
                            <button class="btn-save px-4 py-2" data-bs-toggle="modal" data-bs-target="#addAddressModal">
                                + THÊM ĐỊA CHỈ
                            </button>
                        </div>

                        <div id="addressList">
                            @forelse($addresses as $address)
                            @include('profile.partials.address-card', ['address' => $address])
                            @empty
                            <div class="text-center py-5 text-muted" id="emptyAddresses">
                                <i class="fas fa-map-marker-alt fa-3x mb-3 d-block text-gray-light"></i>
                                <p>Bạn chưa lưu địa chỉ nào.</p>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="avatarModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered avatar-modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Chọn avatar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="avatar-picker" id="avatarPicker">
                    @foreach($avatarOptions as $index => $avatar)
                        <button type="button" class="avatar-option {{ $selectedAvatar === $avatar ? 'active' : '' }}" data-avatar="{{ $avatar }}" aria-label="Avatar {{ $index + 1 }}">
                            <img src="{{ $avatar }}" alt="Avatar {{ $index + 1 }}" loading="lazy" decoding="async" onerror="this.onerror=null; this.src='{{ $defaultAvatar }}';">
                        </button>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── MODAL: THÊM ĐỊA CHỈ ── --}}
<div class="modal fade" id="addAddressModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thêm địa chỉ mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addAddressForm">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label">Họ và tên người nhận <span class="text-danger">*</span></label>
                        <input type="text" name="recipient_name" class="form-control" required>
                        <div class="invalid-feedback-field text-danger small mt-1"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Số điện thoại nhận hàng <span class="text-danger">*</span></label>
                        <input type="text" name="phone" class="form-control" required placeholder="Số điện thoại để liên hệ khi giao hàng">
                        <div class="invalid-feedback-field text-danger small mt-1"></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Tỉnh/Thành phố <span class="text-danger">*</span></label>
                            <input type="text" name="province" class="form-control" required>
                            <div class="invalid-feedback-field text-danger small mt-1"></div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Quận/Huyện <span class="text-danger">*</span></label>
                            <input type="text" name="district" class="form-control" required>
                            <div class="invalid-feedback-field text-danger small mt-1"></div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Phường/Xã <span class="text-danger">*</span></label>
                            <input type="text" name="ward" class="form-control" required>
                            <div class="invalid-feedback-field text-danger small mt-1"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Địa chỉ chi tiết <span class="text-danger">*</span></label>
                        <input type="text" name="detail" class="form-control" placeholder="Số nhà, tên đường..." required>
                        <div class="invalid-feedback-field text-danger small mt-1"></div>
                    </div>
                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" name="is_default" id="add_is_default" value="1">
                        <label class="form-check-label text-lg-custom" for="add_is_default">Đặt làm địa chỉ mặc định</label>
                    </div>
                    <div class="form-feedback mt-3" id="addAddressFeedback"></div>
                </div>
                <div class="modal-footer border-0 pb-4 pe-4">
                    <button type="button" class="btn btn-outline-dark rounded-0" data-bs-dismiss="modal">HỦY</button>
                    <button type="submit" class="btn-save">LƯU ĐỊA CHỈ</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const CSRF = document.querySelector('meta[name="csrf-token"]').content;

    const avatarPicker = document.getElementById('avatarPicker');
    const avatarInput = document.getElementById('userAvatar');
    const avatarPreview = document.getElementById('avatarPreview');
    const avatarModal = document.getElementById('avatarModal');
    const defaultAvatar = @js($defaultAvatar);

    if (avatarPicker && avatarInput && avatarPreview) {
        avatarPreview.addEventListener('error', function() {
            if (this.getAttribute('src') !== defaultAvatar) {
                this.src = defaultAvatar;
            }
        });

        avatarPicker.querySelectorAll('.avatar-option').forEach(function(option) {
            option.addEventListener('click', function() {
                const avatar = this.dataset.avatar;
                avatarInput.value = avatar;
                avatarPreview.src = avatar;
                avatarPicker.querySelectorAll('.avatar-option').forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');
                if (avatarModal) {
                    bootstrap.Modal.getInstance(avatarModal)?.hide();
                }
            });
        });
    }

    /* ── HELPER: show inline feedback ── */
    function showFeedback(el, type, msg) {
        el.className = 'form-feedback ' + type;
        el.textContent = msg;
        el.style.display = 'block';
        setTimeout(() => {
            el.style.display = 'none';
        }, 5000);
    }

    /* ── HELPER: clear field errors ── */
    function clearErrors(form) {
        form.querySelectorAll('.invalid-feedback-field').forEach(el => el.textContent = '');
        form.querySelectorAll('.form-control, .form-select').forEach(el => el.classList.remove('is-invalid'));
    }

    /* ── HELPER: show field errors ── */
    function showErrors(form, errors) {
        Object.entries(errors).forEach(([field, msgs]) => {
            const input = form.querySelector(`[name="${field}"]`);
            if (input) {
                input.classList.add('is-invalid');
                const fb = input.closest('.col-md-8, .col-md-9, .mb-3, .col-md-4')?.querySelector('.invalid-feedback-field');
                if (fb) fb.textContent = msgs[0];
            }
        });
    }

    /* ── HELPER: AJAX submit ── */
    async function ajaxSubmit(form, url, method, btnEl, feedbackEl) {
        clearErrors(form);
        const orig = btnEl.textContent;
        btnEl.disabled = true;
        btnEl.textContent = 'Đang lưu...';

        const data = new FormData(form);
        // FormData doesn't send unchecked checkboxes — handle is_default
        if (form.querySelector('[name="is_default"]') && !form.querySelector('[name="is_default"]').checked) {
            data.set('is_default', '0');
        }

        try {
            const res = await fetch(url, {
                method: method,
                body: data,
                headers: {
                    'X-CSRF-TOKEN': CSRF,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            });
            const json = await res.json();

            if (res.ok && json.success) {
                showFeedback(feedbackEl, 'success', json.message || 'Lưu thành công!');
                return json;
            } else {
                if (json.errors) showErrors(form, json.errors);
                showFeedback(feedbackEl, 'error', json.message || 'Có lỗi xảy ra!');
                return null;
            }
        } catch (e) {
            showFeedback(feedbackEl, 'error', 'Không thể kết nối máy chủ!');
            return null;
        } finally {
            btnEl.disabled = false;
            btnEl.textContent = orig;
        }
    }

    /* ── PROFILE FORM ── */
    document.getElementById('profileForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        clearErrors(this);
        const btn = document.getElementById('profileBtn');
        const fb = document.getElementById('profileFeedback');
        const orig = btn.textContent;
        btn.disabled = true;
        btn.textContent = 'Đang lưu...';

        const data = new FormData(this);
        data.append('_method', 'PUT');

        try {
            const res = await fetch('{{ route("profile.update") }}', {
                method: 'POST',
                body: data,
                headers: {
                    'X-CSRF-TOKEN': CSRF,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
            const json = await res.json();
            if (res.ok && json.success) {
                showFeedback(fb, 'success', json.message);
            } else {
                if (json.errors) showErrors(this, json.errors);
                showFeedback(fb, 'error', json.message || 'Có lỗi xảy ra!');
            }
        } catch {
            showFeedback(fb, 'error', 'Không thể kết nối máy chủ!');
        } finally {
            btn.disabled = false;
            btn.textContent = orig;
        }
    });

    /* ── PASSWORD FORM ── */
    document.getElementById('passwordForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        clearErrors(this);
        const btn = document.getElementById('passwordBtn');
        const fb = document.getElementById('passwordFeedback');
        const orig = btn.textContent;
        btn.disabled = true;
        btn.textContent = 'Đang lưu...';

        const data = new FormData(this);
        data.append('_method', 'PUT');

        try {
            const res = await fetch('{{ route("profile.password") }}', {
                method: 'POST',
                body: data,
                headers: {
                    'X-CSRF-TOKEN': CSRF,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
            const json = await res.json();
            if (res.ok && json.success) {
                showFeedback(fb, 'success', json.message);
                this.reset();
            } else {
                if (json.errors) showErrors(this, json.errors);
                showFeedback(fb, 'error', json.message || 'Có lỗi xảy ra!');
            }
        } catch {
            showFeedback(fb, 'error', 'Không thể kết nối máy chủ!');
        } finally {
            btn.disabled = false;
            btn.textContent = orig;
        }
    });

    /* ── ADD ADDRESS FORM ── */
    document.getElementById('addAddressForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        clearErrors(this);
        const btn = this.querySelector('[type="submit"]');
        const fb = document.getElementById('addAddressFeedback');
        const orig = btn.textContent;
        btn.disabled = true;
        btn.textContent = 'Đang lưu...';

        const data = new FormData(this);
        if (!this.querySelector('[name="is_default"]').checked) data.set('is_default', '0');

        try {
            const res = await fetch('{{ route("profile.addresses.store") }}', {
                method: 'POST',
                body: data,
                headers: {
                    'X-CSRF-TOKEN': CSRF,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
            const json = await res.json();
            if (res.ok && json.success) {
                bootstrap.Modal.getInstance(document.getElementById('addAddressModal')).hide();
                this.reset();
                // Reload address list
                window.ST_SAVE_SCROLL && window.ST_SAVE_SCROLL();
                window.location.hash = '#addresses';
                window.location.reload();
            } else {
                if (json.errors) showErrors(this, json.errors);
                showFeedback(fb, 'error', json.message || 'Có lỗi xảy ra!');
            }
        } catch {
            showFeedback(fb, 'error', 'Không thể kết nối máy chủ!');
        } finally {
            btn.disabled = false;
            btn.textContent = orig;
        }
    });

    /* ── EDIT ADDRESS ── */
    document.addEventListener('submit', async function(e) {
        const form = e.target;
        if (!form.dataset.editAddress) return;
        e.preventDefault();
        clearErrors(form);
        const btn = form.querySelector('[type="submit"]');
        const fb = form.querySelector('.edit-addr-feedback');
        const orig = btn.textContent;
        btn.disabled = true;
        btn.textContent = 'Đang lưu...';

        const data = new FormData(form);
        data.append('_method', 'PUT');
        if (!form.querySelector('[name="is_default"]').checked) data.set('is_default', '0');

        try {
            const res = await fetch(form.dataset.action, {
                method: 'POST',
                body: data,
                headers: {
                    'X-CSRF-TOKEN': CSRF,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
            const json = await res.json();
            if (res.ok && json.success) {
                const modalEl = form.closest('.modal');
                bootstrap.Modal.getInstance(modalEl).hide();
                window.ST_SAVE_SCROLL && window.ST_SAVE_SCROLL();
                window.location.reload();
            } else {
                if (json.errors) showErrors(form, json.errors);
                showFeedback(fb, 'error', json.message || 'Có lỗi xảy ra!');
            }
        } catch {
            showFeedback(fb, 'error', 'Không thể kết nối máy chủ!');
        } finally {
            btn.disabled = false;
            btn.textContent = orig;
        }
    });

    /* ── DELETE ADDRESS ── */
    document.addEventListener('click', async function(e) {
        const btn = e.target.closest('[data-delete-address]');
        if (!btn) return;
        const addrCard = btn.closest('.address-card-wrap');
        const addrName = addrCard.querySelector('.fw-bold')?.innerText || 'Địa chỉ này';

        stConfirmDelete({
            title: 'XÓA ĐỊA CHỈ',
            pill: addrName,
            message: 'Địa chỉ này sẽ bị xóa khỏi sổ địa chỉ của bạn.',
            onConfirm: async () => {
                const url = btn.dataset.deleteAddress;
                try {
                    const res = await fetch(url, {
                        method: 'POST',
                        body: new URLSearchParams({
                            _method: 'DELETE'
                        }),
                        headers: {
                            'X-CSRF-TOKEN': CSRF,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'Content-Type': 'application/x-www-form-urlencoded'
                        }
                    });
                    const json = await res.json();
                    if (json.success) {
                        addrCard.remove();
                        if (!document.querySelector('.address-card-wrap')) {
                            document.getElementById('addressList').innerHTML = '<div class="text-center py-5 text-muted"><i class="fas fa-map-marker-alt fa-3x mb-3 d-block text-gray-light"></i><p>Bạn chưa lưu địa chỉ nào.</p></div>';
                        }
                        window.showToast && window.showToast(json.message, 'success');
                    }
                } catch {
                    window.showToast && window.showToast('Không thể xóa địa chỉ!', 'danger');
                }
            }
        });
    });

    /* ── SET DEFAULT ADDRESS ── */
    document.addEventListener('click', async function(e) {
        const btn = e.target.closest('[data-set-default]');
        if (!btn) return;

        const url = btn.dataset.setDefault;
        try {
            const res = await fetch(url, {
                method: 'POST',
                body: new URLSearchParams({
                    _method: 'PUT'
                }),
                headers: {
                    'X-CSRF-TOKEN': CSRF,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'Content-Type': 'application/x-www-form-urlencoded'
                }
            });
            const json = await res.json();
            if (json.success) {
                window.ST_SAVE_SCROLL && window.ST_SAVE_SCROLL();
                window.location.reload();
            }
        } catch {}
    });

    /* ── KEEP TAB ACTIVE after reload ── */
    document.addEventListener('DOMContentLoaded', function() {
        const hash = window.location.hash;
        if (hash) {
            const tab = document.querySelector(`.sidebar-nav .nav-link[href="${hash}"]`);
            if (tab) {
                document.querySelectorAll('.sidebar-nav .nav-link').forEach(t => t.classList.remove('active'));
                document.querySelectorAll('.tab-pane').forEach(p => {
                    p.classList.remove('show', 'active');
                });
                tab.classList.add('active');
                const pane = document.querySelector(hash);
                if (pane) {
                    pane.classList.add('show', 'active');
                }
            }
        }
        // Save tab on click
        document.querySelectorAll('.sidebar-nav .nav-link[data-bs-toggle="pill"]').forEach(function(link) {
            link.addEventListener('click', function() {
                history.replaceState(null, '', this.getAttribute('href'));
            });
        });
    });
</script>
@endpush
@endsection
