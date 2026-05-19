@extends('layouts.admin')

@section('title', 'Quản lý Kho')

@push('styles')
    @vite(['resources/css/admin_common.css'])
@endpush

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
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="stat-card h-100 text-center d-flex flex-column justify-content-center">
            <div class="stat-value">{{ $stats['total_products'] }}</div>
            <div class="stat-label">Tổng sản phẩm</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card h-100 text-center d-flex flex-column justify-content-center">
            <div class="stat-value text-success">{{ number_format($stats['total_stock']) }}</div>
            <div class="stat-label">Tổng tồn kho</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card h-100 text-center d-flex flex-column justify-content-center">
            <div class="stat-value text-warning">{{ $stats['low_stock'] }}</div>
            <div class="stat-label">Sắp hết hàng</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card h-100 text-center d-flex flex-column justify-content-center">
            <div class="stat-value text-danger">{{ $stats['out_of_stock'] }}</div>
            <div class="stat-label">Hết hàng</div>
        </div>
    </div>
    <div class="col-12">
        <div class="stat-card text-center">
            <div class="stat-value text-success">{{ number_format($stats['total_value']) }}đ</div>
            <div class="stat-label">Tổng giá trị tồn kho</div>
        </div>
    </div>
</div>

<!-- Filter -->
<div class="filter-section">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <span class="text-xs-custom text-gray-medium">
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
                    <th class="uix-2b12d8628e">ID</th>
                    <th class="uix-d26d79f4c2">Ảnh</th>
                    <th>Tên sản phẩm</th>
                    <th class="uix-9fe45cd88c">Giá</th>
                    <th class="uix-9fe45cd88c">Tồn kho</th>
                    <th class="uix-9fe45cd88c">Trạng thái</th>
                    <th class="uix-d4a09a0d6e">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                    <tr class="{{ $product->so_luong === 0 ? 'table-danger' : ($product->so_luong < 10 ? 'table-warning' : '') }}">
                        <td class="font-medium-custom">#{{ $product->id }}</td>
                        <td>
                            @if($product->anh)
                                <img src="{{ $product->image_path }}"
                                     class="w-50-px-custom h-50-px-custom object-cover">
                            @else
                                <div class="w-50-px-custom h-50-px-custom bg-gray-light-custom d-flex align-items-center justify-content-center">
                                    <i class="fas fa-image text-muted"></i>
                                </div>
                            @endif
                        </td>
                        <td class="font-medium-custom">{{ $product->ten_sp }}</td>
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
        <div class="card-footer bg-gray-light-custom">
            {{ $products->appends(request()->query())->links('pagination.bootstrap-5') }}
        </div>
    @endif
</div>

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content glass-modal-custom">
            <div class="modal-header border-bottom-1-px-rgba-custom">
                <h5 class="modal-title font-bold font-outfit-custom text-success"><i class="fas fa-arrow-down me-2"></i>NHẬP KHO</h5>
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
                <div class="modal-footer border-top-1-px-rgba-custom">
                    <button type="button" class="btn btn-secondary rounded-12-px-custom px-15-rem-custom py-05-rem-custom" data-bs-dismiss="modal">HỦY</button>
                    <button type="submit" class="btn btn-success rounded-12-px-custom px-15-rem-custom py-05-rem-custom">NHẬP KHO</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Export Modal -->
<div class="modal fade" id="exportModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content glass-modal-custom">
            <div class="modal-header border-bottom-1-px-rgba-custom">
                <h5 class="modal-title font-bold font-outfit-custom text-warning"><i class="fas fa-arrow-up me-2"></i>XUẤT KHO</h5>
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
                <div class="modal-footer border-top-1-px-rgba-custom">
                    <button type="button" class="btn btn-secondary rounded-12-px-custom px-15-rem-custom py-05-rem-custom" data-bs-dismiss="modal">HỦY</button>
                    <button type="submit" class="btn btn-warning rounded-12-px-custom px-15-rem-custom py-05-rem-custom">XUẤT KHO</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Adjust Modal -->
<div class="modal fade" id="adjustModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content glass-modal-custom">
            <div class="modal-header border-bottom-1-px-rgba-custom">
                <h5 class="modal-title font-bold font-outfit-custom text-info"><i class="fas fa-edit me-2"></i>ĐIỀU CHỈNH TỒN KHO</h5>
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
                <div class="modal-footer border-top-1-px-rgba-custom">
                    <button type="button" class="btn btn-secondary rounded-12-px-custom px-15-rem-custom py-05-rem-custom" data-bs-dismiss="modal">HỦY</button>
                    <button type="submit" class="btn btn-info text-white rounded-12-px-custom px-15-rem-custom py-05-rem-custom">ĐIỀU CHỈNH</button>
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
