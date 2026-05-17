{{-- Shared form partial for create & edit product --}}
<div class="mb-4">
  <div class="d-flex align-items-center gap-2 mb-3">
    <span class="step-badge">1</span>
    <span class="fw-semibold text-dark">Thông tin cơ bản</span>
  </div>
  <div class="row g-3">
    <div class="col-md-6">
      <label class="form-label fw-semibold small">Tên sản phẩm <span class="text-danger">*</span></label>
      <input type="text" name="ten_sp" class="form-control @error('ten_sp') is-invalid @enderror"
        placeholder="Nhập tên sản phẩm..." value="{{ old('ten_sp', $product->ten_sp ?? '') }}" required>
      @error('ten_sp')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
      <label class="form-label fw-semibold small">Loại sản phẩm</label>
      @php
        $currentLoai = old('loai', $product->loai ?? '');
        $isNewLoai   = $currentLoai && !array_key_exists($currentLoai, $loaiList);
      @endphp
      <select name="loai" id="loai_select" class="form-select @error('loai') is-invalid @enderror" onchange="handleLoaiChange(this)" style="{{ $isNewLoai ? 'display:none;' : '' }}">
        <option value="">-- Chọn danh mục --</option>
        @foreach($loaiList as $key => $label)
          <option value="{{ $key }}" {{ $currentLoai == $key ? 'selected' : '' }}>{{ $label }}</option>
        @endforeach
        <option value="__new__">+ Thêm danh mục mới...</option>
      </select>
      <input type="text" name="{{ $isNewLoai ? 'loai' : '' }}" id="loai_new" class="form-control mt-2 @error('loai') is-invalid @enderror" placeholder="Nhập mã loại mới (vd: the_thao)" style="{{ $isNewLoai ? '' : 'display:none;' }}" value="{{ $isNewLoai ? $currentLoai : '' }}">
      <div class="form-text" id="loai_new_hint" style="{{ $isNewLoai ? '' : 'display:none;' }}">
        Tên loại sẽ tự động lưu vào hệ thống. <a href="#" onclick="cancelNewLoai(); return false;">Hủy</a>
      </div>
      @error('loai')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-12">
      <label class="form-label fw-semibold small">Mô tả sản phẩm</label>
      <textarea name="mo_ta" class="form-control @error('mo_ta') is-invalid @enderror" rows="3" placeholder="Nhập mô tả chi tiết sản phẩm...">{{ old('mo_ta', $product->mo_ta ?? '') }}</textarea>
      @error('mo_ta')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
  </div>
</div>

<hr>

<div class="mb-4">
  <div class="d-flex align-items-center gap-2 mb-3">
    <span class="step-badge">2</span>
    <span class="fw-semibold text-dark">Giá & Tồn kho</span>
  </div>
  <div class="row g-3">
    <div class="col-md-4">
      <label class="form-label fw-semibold small">Giá bán (VNĐ) <span class="text-danger">*</span></label>
      <input type="number" name="gia" class="form-control @error('gia') is-invalid @enderror" min="0" value="{{ old('gia', $product->gia ?? '') }}" required>
      @error('gia')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
      <label class="form-label fw-semibold small">Số lượng <span class="text-danger">*</span></label>
      <input type="number" name="so_luong" class="form-control @error('so_luong') is-invalid @enderror" min="0" value="{{ old('so_luong', $product->so_luong ?? '') }}" required>
      @error('so_luong')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
      <label class="form-label fw-semibold small">Trạng thái <span class="text-danger">*</span></label>
      <select name="trang_thai" class="form-select @error('trang_thai') is-invalid @enderror" required>
        <option value="con" {{ old('trang_thai', $product->trang_thai ?? 'con') === 'con' ? 'selected' : '' }}>Còn hàng</option>
        <option value="het" {{ old('trang_thai', $product->trang_thai ?? '') === 'het' ? 'selected' : '' }}>Hết hàng</option>
      </select>
      @error('trang_thai')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
  </div>
</div>

<hr>

<div class="mb-4">
  <div class="d-flex align-items-center gap-2 mb-3">
    <span class="step-badge">3</span>
    <span class="fw-semibold text-dark">Hình ảnh</span>
  </div>
  
  @php
    $currentImg = isset($product) ? $product->anh : null;
    $isUrl = isset($product) ? $product->is_image_url : false;
    $activeTab = old('anh') ? 'url' : ($isUrl ? 'url' : 'file');
  @endphp

  @if($currentImg)
    <div class="mb-3 d-flex align-items-center gap-3 p-3 bg-light rounded">
      <img src="{{ $product->image_path }}" alt="Ảnh hiện tại" style="width:64px; height:64px; object-fit:cover; border-radius:8px; border:1px solid #ddd;" onerror="this.src='{{ asset('images/default-product.svg') }}'">
      <div>
        <div class="fw-semibold text-dark">Ảnh hiện tại</div>
        <div class="small text-muted" style="word-break:break-all;">
          @if($isUrl)
            <a href="{{ $currentImg }}" target="_blank" class="text-primary">{{ Str::limit($currentImg, 60) }}</a>
          @else
            {{ $currentImg }}
          @endif
        </div>
      </div>
    </div>
  @endif

  <div class="card border-0 shadow-sm">
    <div class="card-body">
      <div class="d-flex gap-2 mb-3" role="group">
        <button type="button" id="btn-tab-file" onclick="switchTab('file')" class="btn btn-sm {{ $activeTab === 'file' ? 'btn-primary' : 'btn-outline-secondary' }}">
          <i class="fas fa-upload me-1"></i>Upload file ảnh
        </button>
        <button type="button" id="btn-tab-url" onclick="switchTab('url')" class="btn btn-sm {{ $activeTab === 'url' ? 'btn-primary' : 'btn-outline-secondary' }}">
          <i class="fas fa-link me-1"></i>Nhập link URL
        </button>
      </div>

      <div id="tab-file" style="{{ $activeTab !== 'file' ? 'display:none;' : '' }}">
        <input type="file" name="anh_file" id="anh_file" class="form-control @error('anh_file') is-invalid @enderror" accept="image/*" onchange="previewFile(this)">
        <div class="form-text mt-2">Chấp nhận: JPG, PNG, WEBP. Tối đa 2MB. Để trống nếu không muốn thay đổi.</div>
        @error('anh_file')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        <div id="preview-file" class="mt-3" style="display:none;">
          <img id="preview-file-img" src="" alt="Preview" style="max-height:120px; border-radius:8px; border:1px solid #ddd;">
        </div>
      </div>

      <div id="tab-url" style="{{ $activeTab !== 'url' ? 'display:none;' : '' }}">
        <input type="text" name="anh" id="anh_url" class="form-control @error('anh') is-invalid @enderror" placeholder="https://example.com/image.jpg" value="{{ old('anh', $isUrl ? $currentImg : '') }}" oninput="previewUrl(this)">
        <div class="form-text mt-2">Nhập đường dẫn trực tiếp tới ảnh (http:// hoặc https://)</div>
        @error('anh')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        <div id="preview-url" class="mt-3" style="{{ ($isUrl && $currentImg) ? '' : 'display:none;' }}">
          <img id="preview-url-img" src="{{ $isUrl ? $currentImg : '' }}" alt="Preview" style="max-height:120px; border-radius:8px; border:1px solid #ddd;" onerror="this.parentElement.style.display='none'">
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
