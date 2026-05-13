@extends('layouts.app')

@section('title', 'Chỉnh sửa sản phẩm')

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
                        CHỈNH SỬA SẢN PHẨM
                    </h4>
                </div>
                <div class="card-body" style="padding: 32px;">
                    <form action="{{ route('products.update', $product) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        {{-- Lưu lại trang gốc để redirect đúng sau khi lưu --}}
                        <input type="hidden" name="_ref" value="{{ old('_ref', request()->headers->get('referer')) }}">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Tên sản phẩm <span class="text-danger">*</span></label>
                                    <input type="text" name="ten_sp" class="form-control @error('ten_sp') is-invalid @enderror"
                                           value="{{ old('ten_sp', $product->ten_sp) }}" required>
                                    @error('ten_sp')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Loại sản phẩm</label>
                                    @php
                                        $currentLoai = old('loai', $product->loai);
                                        $isNewLoai   = $currentLoai && !array_key_exists($currentLoai, $loaiList);
                                    @endphp
                                    <select name="loai" id="loai_select"
                                            class="form-select @error('loai') is-invalid @enderror"
                                            onchange="handleLoaiChange(this)"
                                            style="{{ $isNewLoai ? 'display:none;' : '' }}">
                                        <option value="">-- Chọn loại --</option>
                                        @foreach($loaiList as $key => $label)
                                            <option value="{{ $key }}" {{ $currentLoai == $key ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                        <option value="__new__">+ Thêm loại mới...</option>
                                    </select>
                                    <input type="text" name="{{ $isNewLoai ? 'loai' : '' }}" id="loai_new"
                                           class="form-control mt-2 @error('loai') is-invalid @enderror"
                                           placeholder="Nhập tên loại mới (vd: do_choi)"
                                           style="{{ $isNewLoai ? '' : 'display:none;' }}"
                                           value="{{ $isNewLoai ? $currentLoai : '' }}">
                                    <div class="form-text" id="loai_new_hint" style="{{ $isNewLoai ? '' : 'display:none;' }}">
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
                                      rows="3">{{ old('mo_ta', $product->mo_ta) }}</textarea>
                            @error('mo_ta')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Giá (VNĐ) <span class="text-danger">*</span></label>
                                    <input type="number" name="gia" class="form-control @error('gia') is-invalid @enderror"
                                           min="0" value="{{ old('gia', $product->gia) }}" required>
                                    @error('gia')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Số lượng <span class="text-danger">*</span></label>
                                    <input type="number" name="so_luong" class="form-control @error('so_luong') is-invalid @enderror"
                                           min="0" value="{{ old('so_luong', $product->so_luong) }}" required>
                                    @error('so_luong')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Trạng thái <span class="text-danger">*</span></label>
                            <select name="trang_thai" class="form-select @error('trang_thai') is-invalid @enderror" required>
                                <option value="con" {{ old('trang_thai', $product->trang_thai) === 'con' ? 'selected' : '' }}>Còn hàng</option>
                                <option value="het" {{ old('trang_thai', $product->trang_thai) === 'het' ? 'selected' : '' }}>Hết hàng</option>
                            </select>
                            @error('trang_thai')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- ===== HÌNH ẢNH ===== --}}
                        @php
                            $isUrl = $product->is_image_url;
                            $activeTab = old('anh') ? 'url' : ($isUrl ? 'url' : 'file');
                        @endphp
                        <div class="mb-3">
                            <label class="form-label">Hình ảnh</label>

                            {{-- Ảnh hiện tại --}}
                            @if($product->anh)
                                <div class="mb-2 d-flex align-items-center gap-3">
                                    <img src="{{ $product->image_path }}" alt="Ảnh hiện tại"
                                         style="width:72px; height:72px; object-fit:cover; border-radius:8px; border:1px solid var(--border);"
                                         onerror="this.src='{{ asset('images/default-product.svg') }}'">
                                    <div>
                                        <div style="font-size:12px; color:var(--text-secondary); font-weight:500;">Ảnh hiện tại</div>
                                        <div style="font-size:13px; color:var(--text); word-break:break-all;">
                                            @if($isUrl)
                                                <a href="{{ $product->anh }}" target="_blank" style="color:#0066CC;">{{ Str::limit($product->anh, 60) }}</a>
                                            @else
                                                {{ $product->anh }}
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- Tabs --}}
                            <div class="d-flex gap-2 mb-2" role="group">
                                <button type="button" id="btn-tab-file" onclick="switchTab('file')"
                                        class="btn btn-sm {{ $activeTab === 'file' ? 'btn-primary' : 'btn-outline-secondary' }}">
                                    <i class="fas fa-upload me-1"></i>Upload file mới
                                </button>
                                <button type="button" id="btn-tab-url" onclick="switchTab('url')"
                                        class="btn btn-sm {{ $activeTab === 'url' ? 'btn-primary' : 'btn-outline-secondary' }}">
                                    <i class="fas fa-link me-1"></i>Nhập link URL
                                </button>
                            </div>

                            {{-- Tab: Upload file --}}
                            <div id="tab-file" style="{{ $activeTab !== 'file' ? 'display:none;' : '' }}">
                                <input type="file" name="anh_file" id="anh_file"
                                       class="form-control @error('anh_file') is-invalid @enderror"
                                       accept="image/*" onchange="previewFile(this)">
                                <div class="form-text">Chấp nhận: JPG, PNG, GIF, WEBP. Tối đa 2MB. Để trống nếu không muốn thay đổi.</div>
                                @error('anh_file')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <div id="preview-file" class="mt-2" style="display:none;">
                                    <img id="preview-file-img" src="" alt="Preview"
                                         style="max-height:120px; border-radius:8px; border:1px solid var(--border);">
                                </div>
                            </div>

                            {{-- Tab: Nhập URL --}}
                            <div id="tab-url" style="{{ $activeTab !== 'url' ? 'display:none;' : '' }}">
                                <input type="text" name="anh" id="anh_url"
                                       class="form-control @error('anh') is-invalid @enderror"
                                       placeholder="https://example.com/image.jpg"
                                       value="{{ old('anh', $isUrl ? $product->anh : '') }}"
                                       oninput="previewUrl(this)">
                                <div class="form-text">Nhập link ảnh đầy đủ. Để trống nếu không muốn thay đổi.</div>
                                @error('anh')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div id="preview-url" class="mt-2"
                                     style="{{ ($isUrl && $product->anh) ? '' : 'display:none;' }}">
                                    <img id="preview-url-img"
                                         src="{{ $isUrl ? $product->anh : '' }}"
                                         alt="Preview"
                                         style="max-height:120px; border-radius:8px; border:1px solid var(--border);"
                                         onerror="this.parentElement.style.display='none'">
                                </div>
                            </div>
                        </div>
                        {{-- ===== END HÌNH ẢNH ===== --}}

                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-primary">
                                Cập nhật
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
</script>
@endpush
@endsection
