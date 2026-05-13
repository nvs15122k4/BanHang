@extends('layouts.app')

@section('title', 'Thêm sản phẩm mới')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/products.css') }}?v={{ filemtime(public_path('css/products.css')) }}">
@endpush

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card" style="border: none; background-color: #FFFFFF;">
                <div class="card-header" style="background-color: var(--bg); border: none; padding: 24px;">
                    <h4 style="margin-bottom: 0; font-weight: 500; color: var(--text); font-size: 24px;">
                        THÊM SẢN PHẨM MỚI
                    </h4>
                </div>
                <div class="card-body" style="padding: 32px;">
                    <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        {{-- Lưu lại trang gốc để redirect đúng sau khi lưu --}}
                        <input type="hidden" name="_ref" value="{{ old('_ref', request()->headers->get('referer')) }}">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Tên sản phẩm <span class="text-danger">*</span></label>
                                    <input type="text" name="ten_sp" class="form-control @error('ten_sp') is-invalid @enderror"
                                           value="{{ old('ten_sp') }}" required>
                                    @error('ten_sp')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Loại sản phẩm</label>
                                    <select name="loai" id="loai_select"
                                            class="form-select @error('loai') is-invalid @enderror"
                                            onchange="handleLoaiChange(this)">
                                        <option value="">-- Chọn loại --</option>
                                        @foreach($loaiList as $key => $label)
                                            <option value="{{ $key }}" {{ old('loai') == $key ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                        <option value="__new__">+ Thêm loại mới...</option>
                                    </select>
                                    <input type="text" name="loai" id="loai_new"
                                           class="form-control mt-2 @error('loai') is-invalid @enderror"
                                           placeholder="Nhập tên loại mới (vd: do_choi)"
                                           style="display:none;"
                                           value="{{ old('loai') && !array_key_exists(old('loai'), $loaiList) ? old('loai') : '' }}">
                                    <div class="form-text" id="loai_new_hint" style="display:none;">
                                        Tên loại sẽ được lưu vào DB và tự động xuất hiện trong danh sách.
                                        <a href="#" onclick="cancelNewLoai(); return false;">Hủy</a>
                                    </div>
                                    @error('loai')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Mô tả</label>
                            <textarea name="mo_ta" class="form-control @error('mo_ta') is-invalid @enderror"
                                      rows="3">{{ old('mo_ta') }}</textarea>
                            @error('mo_ta')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Giá (VNĐ) <span class="text-danger">*</span></label>
                                    <input type="number" name="gia" class="form-control @error('gia') is-invalid @enderror"
                                           min="0" value="{{ old('gia') }}" required>
                                    @error('gia')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Số lượng <span class="text-danger">*</span></label>
                                    <input type="number" name="so_luong" class="form-control @error('so_luong') is-invalid @enderror"
                                           min="0" value="{{ old('so_luong') }}" required>
                                    @error('so_luong')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Trạng thái <span class="text-danger">*</span></label>
                            <select name="trang_thai" class="form-select @error('trang_thai') is-invalid @enderror" required>
                                <option value="con" {{ old('trang_thai', 'con') === 'con' ? 'selected' : '' }}>Còn hàng</option>
                                <option value="het" {{ old('trang_thai') === 'het' ? 'selected' : '' }}>Hết hàng</option>
                            </select>
                            @error('trang_thai')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- ===== HÌNH ẢNH ===== --}}
                        <div class="mb-3">
                            <label class="form-label">Hình ảnh</label>

                            {{-- Tabs --}}
                            <div class="d-flex gap-2 mb-2" role="group">
                                <button type="button" id="btn-tab-file" onclick="switchTab('file')"
                                        class="btn btn-sm btn-primary">
                                    <i class="fas fa-upload me-1"></i>Upload file
                                </button>
                                <button type="button" id="btn-tab-url" onclick="switchTab('url')"
                                        class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-link me-1"></i>Nhập link URL
                                </button>
                            </div>

                            {{-- Tab: Upload file --}}
                            <div id="tab-file">
                                <input type="file" name="anh_file" id="anh_file"
                                       class="form-control @error('anh_file') is-invalid @enderror"
                                       accept="image/*" onchange="previewFile(this)">
                                <div class="form-text">Chấp nhận: JPG, PNG, GIF, WEBP. Tối đa 2MB</div>
                                @error('anh_file')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <div id="preview-file" class="mt-2" style="display:none;">
                                    <img id="preview-file-img" src="" alt="Preview"
                                         style="max-height:120px; border-radius:8px; border:1px solid var(--border);">
                                </div>
                            </div>

                            {{-- Tab: Nhập URL --}}
                            <div id="tab-url" style="display:none;">
                                <input type="text" name="anh" id="anh_url"
                                       class="form-control @error('anh') is-invalid @enderror"
                                       placeholder="https://example.com/image.jpg"
                                       value="{{ old('anh') }}"
                                       oninput="previewUrl(this)">
                                <div class="form-text">Nhập link ảnh đầy đủ (bắt đầu bằng http:// hoặc https://)</div>
                                @error('anh')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div id="preview-url" class="mt-2" style="display:none;">
                                    <img id="preview-url-img" src="" alt="Preview"
                                         style="max-height:120px; border-radius:8px; border:1px solid var(--border);"
                                         onerror="this.parentElement.style.display='none'">
                                </div>
                            </div>
                        </div>
                        {{-- ===== END HÌNH ẢNH ===== --}}

                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-primary">
                                Thêm sản phẩm
                            </button>
                            <a href="{{ old('_ref', request()->headers->get('referer')) ?: route('admin.products') }}"
                               class="btn btn-outline-primary">
                                Quay lại
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
function switchTab(tab) {
    const fileDiv = document.getElementById('tab-file');
    const urlDiv  = document.getElementById('tab-url');
    const btnFile = document.getElementById('btn-tab-file');
    const btnUrl  = document.getElementById('btn-tab-url');

    if (tab === 'file') {
        fileDiv.style.display = '';
        urlDiv.style.display  = 'none';
        btnFile.className = 'btn btn-sm btn-primary';
        btnUrl.className  = 'btn btn-sm btn-outline-secondary';
        document.getElementById('anh_url').value = '';
        document.getElementById('preview-url').style.display = 'none';
    } else {
        fileDiv.style.display = 'none';
        urlDiv.style.display  = '';
        btnFile.className = 'btn btn-sm btn-outline-secondary';
        btnUrl.className  = 'btn btn-sm btn-primary';
        document.getElementById('anh_file').value = '';
        document.getElementById('preview-file').style.display = 'none';
    }
}

function previewFile(input) {
    const preview = document.getElementById('preview-file');
    const img     = document.getElementById('preview-file-img');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => { img.src = e.target.result; preview.style.display = ''; };
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.style.display = 'none';
    }
}

function previewUrl(input) {
    const preview = document.getElementById('preview-url');
    const img     = document.getElementById('preview-url-img');
    const url = input.value.trim();
    if (url.startsWith('http://') || url.startsWith('https://')) {
        img.src = url;
        preview.style.display = '';
    } else {
        preview.style.display = 'none';
    }
}

// Nếu có lỗi validation ở field anh (URL), mở lại tab URL
@if(old('anh'))
    switchTab('url');
@endif

// Xử lý loại mới nếu old value không có trong danh sách
@if(old('loai') && !array_key_exists(old('loai'), $loaiList))
    document.getElementById('loai_select').style.display = 'none';
    document.getElementById('loai_select').removeAttribute('name');
    document.getElementById('loai_new').style.display = '';
    document.getElementById('loai_new_hint').style.display = '';
@endif

function handleLoaiChange(sel) {
    const newInput = document.getElementById('loai_new');
    const hint     = document.getElementById('loai_new_hint');
    if (sel.value === '__new__') {
        sel.removeAttribute('name');
        newInput.setAttribute('name', 'loai');
        newInput.style.display = '';
        newInput.focus();
        hint.style.display = '';
        sel.style.display = 'none';
    }
}

function cancelNewLoai() {
    const sel      = document.getElementById('loai_select');
    const newInput = document.getElementById('loai_new');
    const hint     = document.getElementById('loai_new_hint');
    sel.setAttribute('name', 'loai');
    sel.value = '';
    sel.style.display = '';
    newInput.removeAttribute('name');
    newInput.value = '';
    newInput.style.display = 'none';
    hint.style.display = 'none';
}
</script>
@endpush
@endsection
