@extends('layouts.admin')
@section('title', 'Quản lý Sản phẩm')

@push('styles')
@vite(['public/css/admin_common.css'])
@endpush

@section('content')
<div class="px-4 py-4">

  {{-- Header --}}
  <div class="d-flex align-items-center justify-content-between mb-4">
    <div>
      <h4 class="fw-bold mb-0"><i class="fas fa-box text-primary me-2"></i>Quản lý Sản phẩm</h4>
      <p class="text-muted small mb-0">Xem và cập nhật trạng thái, tồn kho, giá bán của sản phẩm</p>
    </div>
    <div class="d-flex gap-2">
      @if(request('trash') === 'only')
        <a href="{{ route('admin.products') }}" class="btn btn-outline-secondary">
          <i class="fas fa-list me-2"></i>Danh sách
        </a>
      @else
        <a href="{{ route('admin.products', ['trash' => 'only']) }}" class="btn btn-outline-danger">
          <i class="fas fa-trash-restore me-2"></i>Thùng rác
        </a>
        <a href="{{ route('products.create') }}" class="btn btn-primary">
          <i class="fas fa-plus me-2"></i>Thêm Sản phẩm
        </a>
      @endif
    </div>
  </div>

  {{-- Stats Cards --}}
  @php
    $totalProducts = \App\Models\Product::count();
    $inStock = \App\Models\Product::where('trang_thai', 'con')->count();
    $outOfStock = \App\Models\Product::where('trang_thai', 'het')->count();
    $lowStock = \App\Models\Product::where('so_luong', '<', 10)->count();
  @endphp
  <div class="row g-3 mb-4">
    <div class="col-md-3 col-6">
      <div class="card border-0 shadow-sm text-center py-3">
        <div class="fw-bold fs-3 text-primary">{{ $totalProducts }}</div>
        <div class="text-muted small">Tổng SP</div>
      </div>
    </div>
    <div class="col-md-3 col-6">
      <div class="card border-0 shadow-sm text-center py-3">
        <div class="fw-bold fs-3 text-success">{{ $inStock }}</div>
        <div class="text-muted small">Đang bán</div>
      </div>
    </div>
    <div class="col-md-3 col-6">
      <div class="card border-0 shadow-sm text-center py-3">
        <div class="fw-bold fs-3 text-warning">{{ $lowStock }}</div>
        <div class="text-muted small">Sắp hết hàng</div>
      </div>
    </div>
    <div class="col-md-3 col-6">
      <div class="card border-0 shadow-sm text-center py-3">
        <div class="fw-bold fs-3 text-danger">{{ $outOfStock }}</div>
        <div class="text-muted small">Hết hàng</div>
      </div>
    </div>
  </div>

  {{-- Filter --}}
  <div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
      <form method="GET" action="{{ route('admin.products') }}" class="row g-2 align-items-end">
        @if(request('trash') === 'only')
          <input type="hidden" name="trash" value="only">
        @endif
        <div class="col-md-3">
          <input type="text" name="search" class="form-control" placeholder="Tìm tên sản phẩm..." value="{{ request('search') }}">
        </div>
        <div class="col-md-2">
          <select name="loai" class="form-select">
            <option value="">-- Danh mục --</option>
            @foreach(\App\Models\Product::getLoaiList() as $key => $label)
                <option value="{{ $key }}" {{ request('loai') === $key ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-2">
          <select name="status" class="form-select">
            <option value="">-- Trạng thái --</option>
            <option value="con" {{ request('status') === 'con' ? 'selected' : '' }}>Còn hàng</option>
            <option value="het" {{ request('status') === 'het' ? 'selected' : '' }}>Hết hàng</option>
          </select>
        </div>
        <div class="col-md-3">
          <select name="stock_filter" class="form-select">
            <option value="">-- Lọc Tồn kho --</option>
            <option value="low" {{ request('stock_filter') === 'low' ? 'selected' : '' }}>Sắp hết (< 10)</option>
            <option value="out" {{ request('stock_filter') === 'out' ? 'selected' : '' }}>Đã hết (0)</option>
            <option value="in"  {{ request('stock_filter') === 'in'  ? 'selected' : '' }}>Còn hàng (> 0)</option>
          </select>
        </div>
        <div class="col-md-2">
          <button class="btn btn-primary w-100"><i class="fas fa-search me-1"></i>Lọc</button>
        </div>
      </form>
    </div>
  </div>

  {{-- Table --}}
  <div class="card border-0 shadow-sm">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-product table-hover mb-0">
          <thead class="bg-light">
            <tr>
              <th class="ps-4">Sản phẩm</th>
              <th>Danh mục</th>
              <th>Giá Bán</th>
              <th>Số lượng</th>
              <th>Trạng thái</th>
              <th class="text-end pe-4">Thao tác</th>
            </tr>
          </thead>
          <tbody>
            @forelse($products as $product)
            <tr class="{{ $product->trashed() ? 'table-secondary' : '' }}">
              <td class="ps-4">
                <div class="d-flex align-items-center gap-3">
                    @if($product->anh || $product->productImages->isNotEmpty())
                        <img src="{{ $product->image_path }}" alt="{{ $product->ten_sp }}" class="product-img">
                    @else
                        <div class="product-img-placeholder">
                            <i class="fas fa-image text-muted"></i>
                        </div>
                    @endif
                    <div>
                        <div class="fw-semibold text-dark">{{ $product->ten_sp }}</div>
                        <div class="text-muted uix-e71ae94b55">ID: #{{ $product->id }}</div>
                        @if($product->trashed())
                          <div class="text-danger small">Trong thùng rác - chờ xóa vĩnh viễn sau 60 ngày</div>
                        @endif
                    </div>
                </div>
              </td>
              <td>
                @if($product->loai)
                    <span class="loai-pill">{{ $product->loai_label }}</span>
                @else
                    <span class="text-muted small">—</span>
                @endif
              </td>
              <td>
                <span class="price-val">{{ number_format($product->gia) }}đ</span>
              </td>
              <td>
                <span class="fw-semibold {{ $product->so_luong < 10 ? 'text-danger' : 'text-dark' }}" id="stock-val-{{ $product->id }}">{{ $product->so_luong }}</span>
                <button type="button" class="btn btn-sm btn-link p-0 ms-2 text-primary" onclick="openStockModal({{ $product->id }}, {{ $product->so_luong }}, '{{ addslashes($product->ten_sp) }}')">
                    <i class="fas fa-edit"></i>
                </button>
              </td>
              <td>
                <button type="button" id="status-btn-{{ $product->id }}" onclick="toggleStatus({{ $product->id }}, '{{ $product->trang_thai }}')" class="{{ $product->trang_thai === 'con' ? 'badge-status-con' : 'badge-status-het' }}">
                    {{ $product->trang_thai === 'con' ? 'CÒN HÀNG' : 'HẾT HÀNG' }}
                </button>
              </td>
              <td class="text-end pe-4">
                <div class="d-flex gap-2 justify-content-end">
                  @if($product->trashed())
                    <form method="POST" action="{{ route('admin.products.restore', $product->id) }}">
                      @csrf @method('PATCH')
                      <button type="submit" class="btn btn-sm btn-outline-success" title="Khôi phục">
                        <i class="fas fa-trash-restore"></i>
                      </button>
                    </form>
                  @else
                    <a href="{{ route('products.show', ['product' => $product->slug]) }}" class="btn btn-sm btn-outline-info" title="Xem" target="_blank">
                        <i class="fas fa-eye"></i>
                    </a>
                    <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-outline-primary" title="Sửa">
                        <i class="fas fa-edit"></i>
                    </a>
                    <form method="POST" action="{{ route('products.destroy', $product) }}" id="del-{{ $product->id }}">
                        @csrf @method('DELETE')
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="stConfirmDelete({title:'Chuyển sản phẩm vào thùng rác',pill:'{{ addslashes($product->ten_sp) }}',message:'Sản phẩm sẽ được ẩn khỏi catalog và chuyển vào thùng rác.',note:'Sản phẩm có thể được khôi phục trong 60 ngày.',confirmText:'CHUYỂN VÀO THÙNG RÁC',onConfirm:()=>document.getElementById('del-{{ $product->id }}').submit()})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                  @endif
                </div>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="6" class="text-center py-5 text-muted">
                <i class="fas fa-box fa-3x mb-3 d-block"></i>
                Không tìm thấy sản phẩm nào. <a href="{{ route('products.create') }}">Thêm ngay</a>
              </td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
    @if($products->hasPages())
    <div class="card-footer bg-transparent border-top-0">
      {{ $products->appends(request()->query())->links('pagination::bootstrap-5') }}
    </div>
    @endif
  </div>

</div>

<!-- Update Stock Modal -->
<div class="modal fade" id="updateStockModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered uix-2c8110d4ee">
        <div class="modal-content uix-5702bb2a37">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold">Cập nhật Tồn kho</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small mb-3">Sản phẩm: <strong id="stockProductName" class="text-dark"></strong></p>
                <div>
                    <label class="form-label fw-semibold small">Số lượng mới</label>
                    <input type="number" id="newStock" class="form-control" min="0" required>
                </div>
            </div>
            <div class="modal-footer border-top-0 pt-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-primary px-4" id="btnSaveStock">Lưu thay đổi</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';

function toggleStatus(productId, current) {
    const newStatus = current === 'con' ? 'het' : 'con';
    const btn = document.getElementById(`status-btn-${productId}`);
    btn.disabled = true;

    fetch(`/admin/products/${productId}/status`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': CSRF,
            'Accept': 'application/json',
        },
        body: JSON.stringify({ trang_thai: newStatus }),
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            const isCon = data.trang_thai === 'con';
            btn.className = isCon ? 'badge-status-con' : 'badge-status-het';
            btn.textContent = isCon ? 'CÒN HÀNG' : 'HẾT HÀNG';
            btn.onclick = () => toggleStatus(productId, data.trang_thai);
            showToast(data.message);
        } else {
            showToast(data.message || 'Lỗi cập nhật!', 'error');
        }
    })
    .catch(() => showToast('Lỗi kết nối!', 'error'))
    .finally(() => { btn.disabled = false; });
}

let _stockProductId = null;

function openStockModal(productId, currentStock, productName) {
    _stockProductId = productId;
    document.getElementById('stockProductName').textContent = productName;
    document.getElementById('newStock').value = currentStock;
    new bootstrap.Modal(document.getElementById('updateStockModal')).show();
}

document.getElementById('btnSaveStock').addEventListener('click', function () {
    const soLuong = parseInt(document.getElementById('newStock').value);
    if (isNaN(soLuong) || soLuong < 0) return;

    this.disabled = true;
    this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang lưu...';

    fetch(`/admin/products/${_stockProductId}/stock`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': CSRF,
            'Accept': 'application/json',
        },
        body: JSON.stringify({ so_luong: soLuong }),
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            const span = document.getElementById(`stock-val-${_stockProductId}`);
            span.textContent = data.so_luong;
            span.className   = data.so_luong < 10 ? 'text-danger fw-semibold' : 'text-dark fw-semibold';
            bootstrap.Modal.getInstance(document.getElementById('updateStockModal')).hide();
            showToast(data.message);
        } else {
            showToast(data.message || 'Lỗi cập nhật!', 'error');
        }
    })
    .catch(() => showToast('Lỗi kết nối!', 'error'))
    .finally(() => {
        this.disabled = false;
        this.textContent = 'Lưu thay đổi';
    });
});
</script>
@endpush
