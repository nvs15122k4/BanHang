{{-- Shared form partial for create & edit promotion --}}
{{-- $prefix: 'c' for create, 'e' for edit --}}

{{-- Step 1: Thông tin cơ bản --}}
<div class="mb-4">
  <div class="d-flex align-items-center gap-2 mb-3">
    <span class="step-badge">1</span>
    <span class="fw-semibold text-dark">Thông tin khuyến mãi</span>
  </div>
  <div class="row g-3">
    <div class="col-md-8">
      <label class="form-label fw-semibold small">Tên chương trình <span class="text-danger">*</span></label>
      <input type="text" name="ten" class="form-control @error('ten') is-invalid @enderror"
        placeholder="VD: Black Friday 2026, Sale 1/1, Giảm 50% hè..." value="{{ old('ten', $promo->ten ?? '') }}" required>
      @error('ten')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
      <label class="form-label fw-semibold small">Tag hiển thị</label>
      <input type="text" name="tag" class="form-control" placeholder="HOT, BLACK FRIDAY, SALE..." value="{{ old('tag', $promo->tag ?? '') }}" maxlength="20">
    </div>
    <div class="col-12">
      <label class="form-label fw-semibold small">Mô tả</label>
      <textarea name="mo_ta" class="form-control" rows="2" placeholder="Mô tả ngắn về chương trình KM...">{{ old('mo_ta', $promo->mo_ta ?? '') }}</textarea>
    </div>
    <div class="col-md-4">
      <label class="form-label fw-semibold small">Loại giảm <span class="text-danger">*</span></label>
      <select name="loai_km" class="form-select" required>
        <option value="percent" {{ old('loai_km', $promo->loai_km ?? '') === 'percent' ? 'selected' : '' }}>Phần trăm (%)</option>
        <option value="fixed"   {{ old('loai_km', $promo->loai_km ?? '') === 'fixed'   ? 'selected' : '' }}>Số tiền cố định (đ)</option>
      </select>
    </div>
    <div class="col-md-4">
      <label class="form-label fw-semibold small">Giá trị giảm <span class="text-danger">*</span></label>
      <div class="input-group">
        <input type="number" name="gia_tri" class="form-control @error('gia_tri') is-invalid @enderror"
          placeholder="45" min="0" step="0.01" value="{{ old('gia_tri', $promo->gia_tri ?? '') }}" required>
        <span class="input-group-text">% / đ</span>
      </div>
      @error('gia_tri')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
      <label class="form-label fw-semibold small">Giảm tối đa (đ)</label>
      <input type="number" name="gia_tri_toi_da" class="form-control" placeholder="Không giới hạn" min="0"
        value="{{ old('gia_tri_toi_da', $promo->gia_tri_toi_da ?? '') }}">
    </div>
    <div class="col-md-6">
      <label class="form-label fw-semibold small">Ngày bắt đầu <span class="text-danger">*</span></label>
      <input type="datetime-local" name="ngay_bat_dau" class="form-control @error('ngay_bat_dau') is-invalid @enderror"
        value="{{ old('ngay_bat_dau', (!empty($promo->ngay_bat_dau)) ? $promo->ngay_bat_dau->format('Y-m-d\TH:i') : '') }}" required>
      @error('ngay_bat_dau')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
      <label class="form-label fw-semibold small">Ngày kết thúc <span class="text-danger">*</span></label>
      <input type="datetime-local" name="ngay_ket_thuc" class="form-control @error('ngay_ket_thuc') is-invalid @enderror"
        value="{{ old('ngay_ket_thuc', (!empty($promo->ngay_ket_thuc)) ? $promo->ngay_ket_thuc->format('Y-m-d\TH:i') : '') }}" required>
      @error('ngay_ket_thuc')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
      <label class="form-label fw-semibold small">Trạng thái</label>
      <select name="trang_thai" class="form-select">
        <option value="active"    {{ old('trang_thai', $promo->trang_thai ?? 'active') === 'active'    ? 'selected' : '' }}>Active (tự bật theo lịch)</option>
        <option value="scheduled" {{ old('trang_thai', $promo->trang_thai ?? '') === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
        <option value="inactive"  {{ old('trang_thai', $promo->trang_thai ?? '') === 'inactive'  ? 'selected' : '' }}>Tắt</option>
      </select>
    </div>
  </div>
</div>

<hr>

{{-- Step 2: Phạm vi áp dụng --}}
<div class="mb-3">
  <div class="d-flex align-items-center gap-2 mb-3">
    <span class="step-badge">2</span>
    <span class="fw-semibold text-dark">Chọn phạm vi áp dụng</span>
  </div>
  <div class="row g-3">
    <div class="col-md-4">
      <label class="pham-vi-card d-block {{ old('pham_vi', $promo->pham_vi ?? 'all') === 'all' ? 'selected' : '' }}">
        <input type="radio" name="pham_vi" value="all" class="me-2" {{ old('pham_vi', $promo->pham_vi ?? 'all') === 'all' ? 'checked' : '' }}>
        <div class="fw-bold text-success">🌐 Toàn bộ</div>
        <div class="text-muted small mt-1">Tự động áp dụng cho tất cả sản phẩm đang bán</div>
      </label>
    </div>
    <div class="col-md-4">
      <label class="pham-vi-card d-block {{ old('pham_vi', $promo->pham_vi ?? '') === 'category' ? 'selected' : '' }}">
        <input type="radio" name="pham_vi" value="category" class="me-2" {{ old('pham_vi', $promo->pham_vi ?? '') === 'category' ? 'checked' : '' }}>
        <div class="fw-bold text-primary">📂 Theo danh mục</div>
        <div class="text-muted small mt-1">VD: Áo, Quần, Điện tử</div>
      </label>
    </div>
    <div class="col-md-4">
      <label class="pham-vi-card d-block {{ old('pham_vi', $promo->pham_vi ?? '') === 'product' ? 'selected' : '' }}">
        <input type="radio" name="pham_vi" value="product" class="me-2" {{ old('pham_vi', $promo->pham_vi ?? '') === 'product' ? 'checked' : '' }}>
        <div class="fw-bold text-warning">📦 Sản phẩm cụ thể</div>
        <div class="text-muted small mt-1">VD: iPhone 16, Nike Air</div>
      </label>
    </div>
  </div>

  {{-- Category selector --}}
  <div class="categorySelector mt-3" style="display:{{ old('pham_vi', $promo->pham_vi ?? 'all') === 'category' ? 'block' : 'none' }}">
    <label class="form-label fw-semibold small text-primary">Chọn danh mục:</label>
    <div class="row g-2">
      @foreach($categories as $cat)
      <div class="col-md-4 col-6">
        <label class="product-checkbox-item">
          <input type="checkbox" name="categories[]" value="{{ $cat }}">
          <span class="small">{{ ucfirst($cat) }}</span>
        </label>
      </div>
      @endforeach
    </div>
  </div>

  {{-- Product selector --}}
  <div class="productSelector mt-3" style="display:{{ old('pham_vi', $promo->pham_vi ?? 'all') === 'product' ? 'block' : 'none' }}">
    <label class="form-label fw-semibold small text-warning">Chọn sản phẩm:</label>
    <input type="text" class="form-control form-control-sm mb-2" placeholder="Tìm sản phẩm..." id="productSearchInput" onkeyup="filterProducts(this)">
    <div class="uix-032ed80867" id="productList">
      @foreach($products as $prod)
      <label class="product-checkbox-item" data-name="{{ strtolower($prod->ten_sp) }}">
        <input type="checkbox" name="product_ids[]" value="{{ $prod->id }}">
        <div class="flex-grow-1">
          <div class="small fw-semibold">{{ $prod->ten_sp }}</div>
          <div class="text-muted uix-e48b05836a">{{ $prod->loai }} · {{ number_format($prod->gia,0,',','.') }}đ</div>
        </div>
      </label>
      @endforeach
    </div>
  </div>
</div>
