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
      <label class="form-label fw-semibold small">Danh mục</label>
      @php
        $currentLoai = old('loai', $product->loai ?? '');
        $isNewCategory = filled(old('new_category_name'));
      @endphp
      <select name="loai" id="category_select" class="form-select @error('loai') is-invalid @enderror" onchange="handleCategoryChange(this)" style="{{ $isNewCategory ? 'display:none;' : '' }}">
        <option value="">-- Chọn danh mục --</option>
        @foreach($loaiList as $key => $label)
          <option value="{{ $key }}" {{ $currentLoai == $key ? 'selected' : '' }}>{{ $label }}</option>
        @endforeach
        <option value="__new__">+ Thêm danh mục mới...</option>
      </select>
      <div id="new_category_fields" class="mt-2" style="{{ $isNewCategory ? '' : 'display:none;' }}">
        <input type="text" name="new_category_name" id="new_category_name" class="form-control @error('new_category_name') is-invalid @enderror" placeholder="Tên danh mục mới, ví dụ: Áo polo nam" value="{{ old('new_category_name') }}">
        @error('new_category_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        <select name="new_category_parent_id" class="form-select mt-2 @error('new_category_parent_id') is-invalid @enderror">
          <option value="">-- Danh mục gốc (không có cha) --</option>
          @foreach($categoryTree ?? [] as $categoryOption)
            <option value="{{ $categoryOption->id }}" {{ (string) old('new_category_parent_id') === (string) $categoryOption->id ? 'selected' : '' }}>{{ $categoryOption->path }}</option>
          @endforeach
        </select>
        @error('new_category_parent_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        <div class="form-text">
          Chọn đường dẫn cha rồi nhập tên danh mục con. Danh mục mới sẽ được đánh dấu <strong>Mới</strong>.
          <a href="#" onclick="cancelNewCategory(); return false;">Hủy</a>
        </div>
      </div>
      @error('loai')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
      <label class="form-label fw-semibold small">Thương hiệu</label>
      <input type="text" name="brand_name" list="brand_options" class="form-control @error('brand_name') is-invalid @enderror"
        placeholder="Ví dụ: Nike, Apple" value="{{ old('brand_name', $product->brand?->name ?? '') }}">
      <datalist id="brand_options">
        @foreach($brands ?? [] as $brand)
          <option value="{{ $brand->name }}"></option>
        @endforeach
      </datalist>
      @error('brand_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-12">
      <label class="form-label fw-semibold small">Mô tả sản phẩm</label>
      <textarea name="mo_ta" class="form-control @error('mo_ta') is-invalid @enderror" rows="5" placeholder="Áo Nike&#10;- Size M màu đen&#10;- Size L màu đen&#10;- Size XL màu trắng">{{ old('mo_ta', $product->mo_ta ?? '') }}</textarea>
      @error('mo_ta')<div class="invalid-feedback">{{ $message }}</div>@enderror
      <div class="form-text">Nội dung được hiển thị nguyên dòng tại trang sản phẩm; có thể nhập cấu hình, màu sắc hoặc ghi chú bán hàng.</div>
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
      <input type="text" name="gia" class="form-control js-price-input @error('gia') is-invalid @enderror" inputmode="numeric" pattern="[0-9.,\s]*" value="{{ old('gia', isset($product->gia) ? (int) $product->gia : '') }}" required>
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
      <img src="{{ $product->image_path }}" alt="Ảnh hiện tại" class="inline-product-image-sm" onerror="this.src='{{ asset('images/default-product.svg') }}'">
      <div>
        <div class="fw-semibold text-dark">Ảnh hiện tại</div>
        <div class="small text-muted uix-29ee55a0c6">
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
        <div class="form-text mt-2">Ảnh đại diện. Chấp nhận: JPG, PNG, WEBP. Tối đa 2MB.</div>
        @error('anh_file')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        <div id="preview-file" class="mt-3 uix-c8be1ccba6">
          <img class="uix-b917c0d0bb" id="preview-file-img" src="" alt="Preview">
        </div>
      </div>

      <div id="tab-url" style="{{ $activeTab !== 'url' ? 'display:none;' : '' }}">
        <input type="text" name="anh" id="anh_url" class="form-control @error('anh') is-invalid @enderror" placeholder="https://example.com/image.jpg" value="{{ old('anh', $isUrl ? $currentImg : '') }}" oninput="previewUrl(this)">
        <div class="form-text mt-2">Nhập đường dẫn trực tiếp tới ảnh (http:// hoặc https://)</div>
        @error('anh')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        <div id="preview-url" class="mt-3" style="{{ ($isUrl && $currentImg) ? '' : 'display:none;' }}">
          <img class="uix-b917c0d0bb" id="preview-url-img" src="{{ $isUrl ? $currentImg : '' }}" alt="Preview" onerror="this.parentElement.style.display='none'">
        </div>
      </div>
    </div>
  </div>

  <div class="mt-3">
    <label class="form-label fw-semibold small">Ảnh bổ sung</label>
    <input type="file" name="image_files[]" class="form-control @error('image_files.*') is-invalid @enderror" accept="image/*" multiple>
    <textarea name="image_urls" class="form-control mt-2 @error('image_urls') is-invalid @enderror" rows="3" placeholder="Hoặc nhập mỗi URL ảnh bổ sung trên một dòng">{{ old('image_urls') }}</textarea>
    @error('image_files.*')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
    @error('image_urls')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
    @if(isset($product) && $product->productImages->isNotEmpty())
      <div class="d-flex flex-wrap gap-2 mt-3">
        @foreach($product->productImages as $image)
          <img src="{{ $image->image_url }}" alt="Ảnh sản phẩm" class="inline-product-image-sm">
        @endforeach
      </div>
    @endif
  </div>
</div>

<hr>

<div class="mb-4">
  <div class="d-flex align-items-center gap-2 mb-3">
    <span class="step-badge">4</span>
    <span class="fw-semibold text-dark">Biến thể sản phẩm</span>
  </div>
  <div class="row g-3">
    <div class="col-12">
      <label class="form-label fw-semibold small">Mỗi dòng là một lựa chọn khách có thể mua</label>
      @php
        $variantLines = old('variants_text', implode("\n", $product->variant_options ?? []));
      @endphp
      <textarea name="variants_text" class="form-control @error('variants_text') is-invalid @enderror" rows="5" placeholder="Size M màu đen&#10;Size L màu đen&#10;Size XL màu trắng">{{ $variantLines }}</textarea>
      @error('variants_text')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
      <div class="form-text mt-2">Ví dụ quần áo: <code>Size M màu đen</code>. Ví dụ điện thoại: <code>256GB xanh</code>. Để trống nếu sản phẩm không cần chọn biến thể.</div>
    </div>
  </div>
</div>

@push('scripts')
<script>
function handleCategoryChange(sel) {
  const fields = document.getElementById('new_category_fields');
  const nameInput = document.getElementById('new_category_name');
  if (sel.value === '__new__') {
    sel.removeAttribute('name');
    fields.style.display = '';
    nameInput.focus();
    sel.style.display = 'none';
  }
}
function cancelNewCategory() {
  const sel = document.getElementById('category_select');
  const fields = document.getElementById('new_category_fields');
  const nameInput = document.getElementById('new_category_name');
  sel.setAttribute('name', 'loai');
  sel.value = '';
  sel.style.display = '';
  nameInput.value = '';
  fields.style.display = 'none';
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

document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.js-price-input').forEach(input => {
    input.addEventListener('input', function () {
      this.value = this.value.replace(/[^\d.,\s]/g, '');
    });

    input.form?.addEventListener('submit', function () {
      input.value = input.value.replace(/[^\d]/g, '');
    });
  });
});
</script>
@endpush
