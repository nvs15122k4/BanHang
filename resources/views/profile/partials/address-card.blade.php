<div class="address-card-wrap">
    <div class="address-card {{ $address->is_default ? 'is-default' : '' }}">
        @if($address->is_default)
            <span class="badge mb-2 rounded-0" style="background:var(--primary); font-size:11px; letter-spacing:0.5px;">MẶC ĐỊNH</span>
        @endif
        <h6 class="fw-bold mb-1" style="font-size:15px;">{{ $address->recipient_name }}</h6>
        <p class="text-muted mb-1" style="font-size:13px;"><i class="fas fa-phone-alt me-2"></i>{{ $address->phone }}</p>
        <p class="mb-3" style="font-size:13px; color:#555;">{{ $address->detail }}, {{ $address->ward }}, {{ $address->district }}, {{ $address->province }}</p>

        <div class="d-flex gap-3 pt-3 border-top align-items-center">
            <button class="btn-link-action" data-bs-toggle="modal" data-bs-target="#editAddressModal{{ $address->id }}">Sửa</button>
            <button class="btn-link-action danger" data-delete-address="{{ route('profile.addresses.destroy', $address->id) }}">Xóa</button>
            @if(!$address->is_default)
                <button class="btn-link-action ms-auto" data-set-default="{{ route('profile.addresses.default', $address->id) }}">Đặt mặc định</button>
            @endif
        </div>
    </div>
</div>

{{-- Edit Modal --}}
<div class="modal fade" id="editAddressModal{{ $address->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Sửa địa chỉ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form data-edit-address="1" data-action="{{ route('profile.addresses.update', $address->id) }}">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label">Họ và tên người nhận <span class="text-danger">*</span></label>
                        <input type="text" name="recipient_name" class="form-control" value="{{ $address->recipient_name }}" required>
                        <div class="invalid-feedback-field text-danger small mt-1"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Số điện thoại nhận hàng <span class="text-danger">*</span></label>
                        <input type="text" name="phone" class="form-control" value="{{ $address->phone }}" required>
                        <div class="invalid-feedback-field text-danger small mt-1"></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Tỉnh/Thành phố <span class="text-danger">*</span></label>
                            <input type="text" name="province" class="form-control" value="{{ $address->province }}" required>
                            <div class="invalid-feedback-field text-danger small mt-1"></div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Quận/Huyện <span class="text-danger">*</span></label>
                            <input type="text" name="district" class="form-control" value="{{ $address->district }}" required>
                            <div class="invalid-feedback-field text-danger small mt-1"></div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Phường/Xã <span class="text-danger">*</span></label>
                            <input type="text" name="ward" class="form-control" value="{{ $address->ward }}" required>
                            <div class="invalid-feedback-field text-danger small mt-1"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Địa chỉ chi tiết <span class="text-danger">*</span></label>
                        <input type="text" name="detail" class="form-control" value="{{ $address->detail }}" required>
                        <div class="invalid-feedback-field text-danger small mt-1"></div>
                    </div>
                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" name="is_default" id="edit_default_{{ $address->id }}" value="1" {{ $address->is_default ? 'checked' : '' }}>
                        <label class="form-check-label" for="edit_default_{{ $address->id }}" style="font-size:14px;">Đặt làm địa chỉ mặc định</label>
                    </div>
                    <div class="form-feedback edit-addr-feedback mt-3"></div>
                </div>
                <div class="modal-footer border-0 pb-4 pe-4">
                    <button type="button" class="btn btn-outline-dark rounded-0" data-bs-dismiss="modal">HỦY</button>
                    <button type="submit" class="btn-save">LƯU THAY ĐỔI</button>
                </div>
            </form>
        </div>
    </div>
</div>
