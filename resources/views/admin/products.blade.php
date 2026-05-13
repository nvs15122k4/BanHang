@extends('layouts.admin')

@section('title', 'Quản lý Sản phẩm')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-box me-3"></i>QUẢN LÝ SẢN PHẨM</h1>
        <a href="{{ route('products.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>THÊM SẢN PHẨM MỚI
        </a>
    </div>
</div>

<!-- Filter Section -->
<div class="filter-section">
    <form method="GET" action="{{ route('admin.products') }}" class="row g-3">
        <div class="col-md-3">
            <input type="text" name="search" class="form-control" placeholder="Tìm kiếm sản phẩm..."
                   value="{{ request('search') }}">
        </div>
        <div class="col-md-2">
            <select name="loai" class="form-select">
                <option value="">-- Loại SP --</option>
                @foreach(\App\Models\Product::getLoaiList() as $key => $label)
                    <option value="{{ $key }}" {{ request('loai') === $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <select name="status" class="form-select">
                <option value="">-- Tồn kho --</option>
                <option value="con" {{ request('status') === 'con' ? 'selected' : '' }}>Còn hàng</option>
                <option value="het" {{ request('status') === 'het' ? 'selected' : '' }}>Hết hàng</option>
            </select>
        </div>
        <div class="col-md-2">
            <select name="stock_filter" class="form-select">
                <option value="">-- Tồn kho --</option>
                <option value="low" {{ request('stock_filter') === 'low' ? 'selected' : '' }}>Sắp hết (< 10)</option>
                <option value="out" {{ request('stock_filter') === 'out' ? 'selected' : '' }}>Hết hàng</option>
                <option value="in"  {{ request('stock_filter') === 'in'  ? 'selected' : '' }}>Còn hàng</option>
            </select>
        </div>
        <div class="col-md-3">
            <button type="submit" class="btn btn-primary w-100">
                <i class="fas fa-search me-2"></i>Lọc
            </button>
        </div>
    </form>
</div>

<!-- Products Table -->
<div class="card admin-table">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th style="width: 60px;">ID</th>
                    <th style="width: 80px;">Ảnh</th>
                    <th>Tên sản phẩm</th>
                    <th style="width: 110px;">Loại</th>
                    <th style="width: 120px;">Giá</th>
                    <th style="width: 100px;">Số lượng</th>
                    <th style="width: 110px;">Tồn kho</th>
                    <th style="width: 200px;">Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                    <tr>
                        <td style="font-weight: 500;">#{{ $product->id }}</td>
                        <td>
                            @if($product->anh)
                                <img src="{{ $product->image_path }}" 
                                     alt="{{ $product->ten_sp }}" 
                                     style="width: 50px; height: 50px; object-fit: cover;">
                            @else
                                <div style="width: 50px; height: 50px; background-color: var(--bg); display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-image text-muted"></i>
                                </div>
                            @endif
                        </td>
                        <td style="font-weight: 500;">{{ $product->ten_sp }}</td>
                        <td>
                            @if($product->loai)
                                <span style="background:var(--bg);color:var(--text);padding:3px 10px;border-radius:20px;font-size:12px;font-weight:500;white-space:nowrap;">
                                    {{ $product->loai_label }}
                                </span>
                            @else
                                <span style="color:#AAAAAA;font-size:12px;">—</span>
                            @endif
                        </td>
                        <td>{{ number_format($product->gia) }}đ</td>
                        <td>
                            <span class="{{ $product->so_luong < 10 ? 'text-danger fw-bold' : '' }}"
                                  id="stock-val-{{ $product->id }}">
                                {{ $product->so_luong }}
                            </span>
                            <button type="button" class="btn btn-sm btn-link p-0 ms-1"
                                    onclick="openStockModal({{ $product->id }}, {{ $product->so_luong }}, '{{ addslashes($product->ten_sp) }}')">
                                <i class="fas fa-edit"></i>
                            </button>
                        </td>
                        <td>
                            <button type="button"
                                    id="status-btn-{{ $product->id }}"
                                    onclick="toggleStatus({{ $product->id }}, '{{ $product->trang_thai }}')"
                                    class="{{ $product->trang_thai === 'con' ? 'badge-status-active' : 'badge-status-inactive' }} border-0"
                                    style="cursor:pointer;">
                                {{ $product->trang_thai === 'con' ? 'CÒN HÀNG' : 'HẾT HÀNG' }}
                            </button>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('products.show', $product) }}" class="btn btn-outline-info" title="Xem">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('products.edit', $product) }}" class="btn btn-outline-primary" title="Sửa">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-outline-danger"
                                        onclick="deleteProduct({{ $product->id }}, '{{ addslashes($product->ten_sp) }}')" title="Xóa">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-5">
                            <i class="fas fa-box fa-3x mb-3 d-block"></i>
                            Không tìm thấy sản phẩm nào
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($products->hasPages())
        <div class="card-footer" style="background-color: var(--bg);">
            {{ $products->appends(request()->query())->links('pagination.bootstrap-5') }}
        </div>
    @endif
</div>

<!-- Update Stock Modal -->
<div class="modal fade" id="updateStockModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="background: var(--bg-card); backdrop-filter: blur(15px); border: var(--glass-border); border-radius: 24px;">
            <div class="modal-header" style="border-bottom: 1px solid rgba(0,0,0,0.1);">
                <h5 class="modal-title" style="font-weight: 700; font-family: 'Outfit', sans-serif;">CẬP NHẬT SỐ LƯỢNG</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Sản phẩm: <strong id="stockProductName"></strong></p>
                <div class="mb-3">
                    <label class="form-label">Số lượng mới</label>
                    <input type="number" id="newStock" class="form-control" min="0" required>
                </div>
            </div>
            <div class="modal-footer" style="border-top: 1px solid rgba(0,0,0,0.1);">
                <button type="button" class="btn btn-secondary" style="border-radius: 12px; padding: 0.5rem 1.5rem;" data-bs-dismiss="modal">HỦY</button>
                <button type="button" class="btn btn-primary" style="padding: 0.5rem 1.5rem; border-radius: 12px;" id="btnSaveStock">CẬP NHẬT</button>
            </div>
        </div>
    </div>
</div>

<!-- Toast thông báo -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 9999;">
    <div id="adminToast" class="toast align-items-center text-white border-0" role="alert">
        <div class="d-flex">
            <div class="toast-body" id="adminToastMsg"></div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>

@push('scripts')
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';

/* ── Hiển thị toast ── */
function showToast(msg, ok = true) {
    const el  = document.getElementById('adminToast');
    const txt = document.getElementById('adminToastMsg');
    el.className = `toast align-items-center text-white border-0 bg-${ok ? 'success' : 'danger'}`;
    txt.textContent = msg;
    bootstrap.Toast.getOrCreateInstance(el, { delay: 2500 }).show();
}

/* ── Toggle trạng thái (AJAX) ── */
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
            btn.className = `${isCon ? 'badge-status-active' : 'badge-status-inactive'} border-0`;
            btn.textContent = isCon ? 'CÒN HÀNG' : 'HẾT HÀNG';
            btn.onclick = () => toggleStatus(productId, data.trang_thai);
            showToast(data.message);
        } else {
            showToast(data.message || 'Lỗi cập nhật!', false);
        }
    })
    .catch(() => showToast('Lỗi kết nối!', false))
    .finally(() => { btn.disabled = false; });
}

/* ── Cập nhật số lượng (AJAX) ── */
let _stockProductId = null;

function openStockModal(productId, currentStock, productName) {
    _stockProductId = productId;
    document.getElementById('stockProductName').textContent = productName;
    document.getElementById('newStock').value = currentStock;
    bootstrap.Modal.getOrCreateInstance(document.getElementById('updateStockModal')).show();
}

document.getElementById('btnSaveStock').addEventListener('click', function () {
    const soLuong = parseInt(document.getElementById('newStock').value);
    if (isNaN(soLuong) || soLuong < 0) return;

    this.disabled = true;
    this.textContent = 'Đang lưu...';

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
            span.className   = data.so_luong < 10 ? 'text-danger fw-bold' : '';
            bootstrap.Modal.getInstance(document.getElementById('updateStockModal')).hide();
            showToast(data.message);
        } else {
            showToast(data.message || 'Lỗi cập nhật!', false);
        }
    })
    .catch(() => showToast('Lỗi kết nối!', false))
    .finally(() => {
        this.disabled = false;
        this.textContent = 'CẬP NHẬT';
    });
});

/* ── Xóa sản phẩm (AJAX) ── */
function deleteProduct(productId, productName) {
    showConfirm(
        `Sản phẩm "${productName}" sẽ bị xóa vĩnh viễn.`,
        () => {
            fetch(`/products/${productId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': CSRF,
                    'Accept': 'application/json',
                },
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    // Xóa hàng khỏi bảng không reload
                    const row = document.querySelector(`#status-btn-${productId}`)?.closest('tr');
                    if (row) row.remove();
                    showToast(data.message);
                } else {
                    showToast(data.message || 'Không thể xóa!', false);
                }
            })
            .catch(() => showToast('Lỗi kết nối!', false));
        },
        'Xóa sản phẩm'
    );
}
</script>
@endpush
@endsection
