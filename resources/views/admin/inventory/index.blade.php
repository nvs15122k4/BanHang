@extends('layouts.admin')

@section('title', 'Quản lý Kho')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-warehouse me-3"></i>QUẢN LÝ KHO</h1>
        <a href="{{ route('admin.inventory.logs') }}" class="btn btn-secondary">
            <i class="fas fa-history me-2"></i>Lịch sử xuất nhập kho
        </a>
    </div>
</div>

<!-- Statistics -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-center">
                <div class="stat-icon"><i class="fas fa-box"></i></div>
                <div class="ms-3">
                    <div class="stat-value">{{ $stats['total_products'] }}</div>
                    <div class="stat-label">Tổng sản phẩm</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-center">
                <div class="stat-icon" style="background-color: #28A745;"><i class="fas fa-cubes"></i></div>
                <div class="ms-3">
                    <div class="stat-value">{{ number_format($stats['total_stock']) }}</div>
                    <div class="stat-label">Tổng tồn kho</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-center">
                <div class="stat-icon" style="background-color: #FFC107;"><i class="fas fa-exclamation-triangle"></i></div>
                <div class="ms-3">
                    <div class="stat-value">{{ $stats['low_stock'] }}</div>
                    <div class="stat-label">Sắp hết hàng</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-center">
                <div class="stat-icon" style="background-color: #DC3545;"><i class="fas fa-times-circle"></i></div>
                <div class="ms-3">
                    <div class="stat-value">{{ $stats['out_of_stock'] }}</div>
                    <div class="stat-label">Hết hàng</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="stat-card">
            <div class="stat-value text-success">{{ number_format($stats['total_value']) }}đ</div>
            <div class="stat-label">Tổng giá trị tồn kho (Số lượng × Giá)</div>
        </div>
    </div>
</div>

<!-- Filter -->
<div class="filter-section">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <span style="font-size:13px;color:var(--text-secondary);">
            <i class="fas fa-info-circle me-1"></i>
            Trạng thái tự động: <strong>Tồn kho = 0</strong> → Ngừng bán &nbsp;|&nbsp; <strong>Tồn kho > 0</strong> → Còn hàng
        </span>
    </div>
    <form method="GET" action="{{ route('admin.inventory.index') }}" class="row g-3">
        <div class="col-md-5">
            <input type="text" name="search" class="form-control" placeholder="Tìm kiếm sản phẩm..."
                   value="{{ request('search') }}">
        </div>
        <div class="col-md-4">
            <select name="stock_filter" class="form-select">
                <option value="">-- Tất cả --</option>
                <option value="low" {{ request('stock_filter') === 'low' ? 'selected' : '' }}>Sắp hết (< 10)</option>
                <option value="out" {{ request('stock_filter') === 'out' ? 'selected' : '' }}>Hết hàng (= 0)</option>
                <option value="in" {{ request('stock_filter') === 'in' ? 'selected' : '' }}>Còn hàng (> 0)</option>
            </select>
        </div>
        <div class="col-md-3">
            <button type="submit" class="btn btn-primary w-100">
                <i class="fas fa-search me-2"></i>Lọc
            </button>
        </div>
    </form>
</div>

<!-- Inventory Table -->
<div class="card admin-table">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th style="width:60px;">ID</th>
                    <th style="width:70px;">Ảnh</th>
                    <th>Tên sản phẩm</th>
                    <th style="width:120px;">Giá</th>
                    <th style="width:120px;">Tồn kho</th>
                    <th style="width:120px;">Trạng thái</th>
                    <th style="width:200px;">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                    <tr class="{{ $product->so_luong === 0 ? 'table-danger' : ($product->so_luong < 10 ? 'table-warning' : '') }}">
                        <td style="font-weight:500;">#{{ $product->id }}</td>
                        <td>
                            @if($product->anh)
                                <img src="{{ $product->image_path }}"
                                     style="width:50px;height:50px;object-fit:cover;">
                            @else
                                <div style="width:50px;height:50px;background:var(--bg);display:flex;align-items:center;justify-content:center;">
                                    <i class="fas fa-image text-muted"></i>
                                </div>
                            @endif
                        </td>
                        <td style="font-weight:500;">{{ $product->ten_sp }}</td>
                        <td>{{ number_format($product->gia) }}đ</td>
                        <td>
                            @if($product->so_luong === 0)
                                <span class="badge bg-danger">Hết hàng</span>
                            @elseif($product->so_luong < 10)
                                <span class="text-warning fw-bold">{{ $product->so_luong }} ⚠️</span>
                            @else
                                <span class="text-success fw-bold">{{ $product->so_luong }}</span>
                            @endif
                        </td>
                        <td>
                            <span class="{{ $product->trang_thai === 'con' ? 'badge-status-active' : 'badge-status-inactive' }}">
                                {{ $product->trang_thai === 'con' ? 'CÒN HÀNG' : 'NGỪNG BÁN' }}
                            </span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <button type="button" class="btn btn-outline-success"
                                        onclick="openImport({{ $product->id }}, '{{ $product->ten_sp }}', {{ $product->so_luong }})"
                                        title="Nhập kho">
                                    <i class="fas fa-arrow-down"></i> Nhập
                                </button>
                                <button type="button" class="btn btn-outline-warning"
                                        onclick="openExport({{ $product->id }}, '{{ $product->ten_sp }}', {{ $product->so_luong }})"
                                        title="Xuất kho">
                                    <i class="fas fa-arrow-up"></i> Xuất
                                </button>
                                <button type="button" class="btn btn-outline-info"
                                        onclick="openAdjust({{ $product->id }}, '{{ $product->ten_sp }}', {{ $product->so_luong }})"
                                        title="Điều chỉnh">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5">
                            <i class="fas fa-warehouse fa-3x mb-3 d-block"></i>
                            Không tìm thấy sản phẩm nào
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($products->hasPages())
        <div class="card-footer" style="background-color:#F5F5F5;">
            {{ $products->appends(request()->query())->links('pagination.bootstrap-5') }}
        </div>
    @endif
</div>

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="backdrop-filter: blur(15px); border: var(--glass-border); border-radius: 24px;">
            <div class="modal-header" style="border-bottom: 1px solid rgba(40, 167, 69, 0.2);">
                <h5 class="modal-title" style="font-weight: 700; font-family: 'Outfit', sans-serif; color: #28a745;"><i class="fas fa-arrow-down me-2"></i>NHẬP KHO</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.inventory.import') }}">
                @csrf
                <input type="hidden" name="product_id" id="import_product_id">
                <div class="modal-body">
                    <p>Sản phẩm: <strong id="import_product_name"></strong></p>
                    <p>Tồn kho hiện tại: <strong id="import_current_stock" class="text-primary"></strong></p>
                    <div class="mb-3">
                        <label class="form-label">Số lượng nhập <span class="text-danger">*</span></label>
                        <input type="number" name="so_luong" class="form-control" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Lý do <span class="text-danger">*</span></label>
                        <input type="text" name="ly_do" class="form-control" placeholder="VD: Nhập hàng từ nhà cung cấp" required>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid rgba(40, 167, 69, 0.2);">
                    <button type="button" class="btn btn-secondary" style="border-radius: 12px; padding: 0.5rem 1.5rem;" data-bs-dismiss="modal">HỦY</button>
                    <button type="submit" class="btn btn-success" style="border-radius: 12px; padding: 0.5rem 1.5rem;">NHẬP KHO</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Export Modal -->
<div class="modal fade" id="exportModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="backdrop-filter: blur(15px); border: var(--glass-border); border-radius: 24px;">
            <div class="modal-header" style="border-bottom: 1px solid rgba(255, 193, 7, 0.2);">
                <h5 class="modal-title" style="font-weight: 700; font-family: 'Outfit', sans-serif; color: #d39e00;"><i class="fas fa-arrow-up me-2"></i>XUẤT KHO</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.inventory.export') }}">
                @csrf
                <input type="hidden" name="product_id" id="export_product_id">
                <div class="modal-body">
                    <p>Sản phẩm: <strong id="export_product_name"></strong></p>
                    <p>Tồn kho hiện tại: <strong id="export_current_stock" class="text-primary"></strong></p>
                    <div class="mb-3">
                        <label class="form-label">Số lượng xuất <span class="text-danger">*</span></label>
                        <input type="number" name="so_luong" id="export_qty" class="form-control" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Lý do <span class="text-danger">*</span></label>
                        <input type="text" name="ly_do" class="form-control" placeholder="VD: Hàng hỏng, mất mát..." required>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid rgba(255, 193, 7, 0.2);">
                    <button type="button" class="btn btn-secondary" style="border-radius: 12px; padding: 0.5rem 1.5rem;" data-bs-dismiss="modal">HỦY</button>
                    <button type="submit" class="btn btn-warning" style="border-radius: 12px; padding: 0.5rem 1.5rem;">XUẤT KHO</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Adjust Modal -->
<div class="modal fade" id="adjustModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="backdrop-filter: blur(15px); border: var(--glass-border); border-radius: 24px;">
            <div class="modal-header" style="border-bottom: 1px solid rgba(23, 162, 184, 0.2);">
                <h5 class="modal-title" style="font-weight: 700; font-family: 'Outfit', sans-serif; color: #17a2b8;"><i class="fas fa-edit me-2"></i>ĐIỀU CHỈNH TỒN KHO</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.inventory.adjust') }}">
                @csrf
                <input type="hidden" name="product_id" id="adjust_product_id">
                <div class="modal-body">
                    <p>Sản phẩm: <strong id="adjust_product_name"></strong></p>
                    <p>Tồn kho hiện tại: <strong id="adjust_current_stock" class="text-primary"></strong></p>
                    <div class="mb-3">
                        <label class="form-label">Số lượng mới <span class="text-danger">*</span></label>
                        <input type="number" name="so_luong_moi" id="adjust_qty" class="form-control" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Lý do điều chỉnh <span class="text-danger">*</span></label>
                        <input type="text" name="ly_do" class="form-control" placeholder="VD: Kiểm kê thực tế..." required>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid rgba(23, 162, 184, 0.2);">
                    <button type="button" class="btn btn-secondary" style="border-radius: 12px; padding: 0.5rem 1.5rem;" data-bs-dismiss="modal">HỦY</button>
                    <button type="submit" class="btn btn-info text-white" style="border-radius: 12px; padding: 0.5rem 1.5rem;">ĐIỀU CHỈNH</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function openImport(id, name, stock) {
    document.getElementById('import_product_id').value = id;
    document.getElementById('import_product_name').textContent = name;
    document.getElementById('import_current_stock').textContent = stock;
    new bootstrap.Modal(document.getElementById('importModal')).show();
}
function openExport(id, name, stock) {
    document.getElementById('export_product_id').value = id;
    document.getElementById('export_product_name').textContent = name;
    document.getElementById('export_current_stock').textContent = stock;
    document.getElementById('export_qty').max = stock;
    new bootstrap.Modal(document.getElementById('exportModal')).show();
}
function openAdjust(id, name, stock) {
    document.getElementById('adjust_product_id').value = id;
    document.getElementById('adjust_product_name').textContent = name;
    document.getElementById('adjust_current_stock').textContent = stock;
    document.getElementById('adjust_qty').value = stock;
    new bootstrap.Modal(document.getElementById('adjustModal')).show();
}
</script>
@endpush
@endsection
