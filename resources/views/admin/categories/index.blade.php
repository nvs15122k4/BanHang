@extends('layouts.admin')

@section('title', 'Quản lý Danh mục')

@push('styles')
    @vite(['public/css/admin_common.css'])
@endpush

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-tags me-3"></i>QUẢN LÝ DANH MỤC</h1>
        <button type="button" class="btn btn-primary" onclick="openCategoryModal()">
            <i class="fas fa-plus me-2"></i>TẠO DANH MỤC MỚI
        </button>
    </div>
</div>

<!-- Categories Table -->
<div class="card admin-table">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Icon</th>
                    <th>Tên danh mục</th>
                    <th>Mã (Slug)</th>
                    <th>Mô tả</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $category)
                    <tr>
                        <td>{{ $category->id }}</td>
                        <td>
                            <i class="{{ $category->icon }} fa-lg inline-color-primary"></i>
                        </td>
                        <td class="font-medium-custom">{{ $category->name }}</td>
                        <td><code>{{ $category->slug }}</code></td>
                        <td>{{ Str::limit($category->description, 50) }}</td>
                        <td>
                            <div class="d-flex gap-2">
                                {{-- Nút sửa --}}
                                <button type="button" class="btn btn-sm btn-outline-primary"
                                        onclick="openCategoryModal({{ $category->id }}, '{{ addslashes($category->name) }}', '{{ addslashes($category->slug) }}', '{{ addslashes($category->icon) }}', '{{ addslashes($category->description) }}')"
                                        title="Chỉnh sửa danh mục">
                                    <i class="fas fa-edit"></i>
                                </button>

                                {{-- Nút xóa --}}
                                <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" class="d-inline"
                                      id="form-delete-{{ $category->id }}">
                                    @csrf @method('DELETE')
                                    <button type="button" class="btn btn-sm btn-outline-danger"
                                            title="Xóa danh mục"
                                            onclick="showConfirm(
                                                'Bạn có chắc chắn muốn xóa danh mục &quot;{{ addslashes($category->name) }}&quot;?',
                                                () => document.getElementById('form-delete-{{ $category->id }}').submit(),
                                                'XÓA DANH MỤC',
                                                'danger',
                                                'XÓA DANH MỤC',
                                                '{{ addslashes($category->name) }}',
                                                'Danh mục chỉ được xóa khi không còn sản phẩm liên kết.'
                                            )">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-5">
                            <i class="fas fa-tags fa-3x mb-3 d-block"></i>
                            Chưa có danh mục nào
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Create/Edit Category Modal -->
<div class="modal fade admin-modal" id="categoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content admin-modal-content">
            <div class="modal-header admin-modal-header">
                <h5 class="modal-title admin-modal-title" id="categoryModalTitle">TẠO DANH MỤC MỚI</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.categories.store') }}" id="categoryForm">
                @csrf
                <input type="hidden" name="_method" id="categoryMethod" value="POST">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label">Tên danh mục <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="catName" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mã danh mục (Slug)</label>
                        <input type="text" name="slug" id="catSlug" class="form-control" placeholder="Để trống để tự tạo">
                        <small class="text-muted">Ví dụ: <code>dien_tu</code>, <code>thoi_trang</code>. Sẽ được dùng trên thanh địa chỉ URL.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label d-flex justify-content-between align-items-center">
                            <span>Icon (FontAwesome)</span>
                            <a href="https://fontawesome.com/search?o=r&m=free" target="_blank" class="text-decoration-none small" title="Tìm icon miễn phí">
                                <i class="fas fa-external-link-alt me-1"></i>Tra cứu Icon
                            </a>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text"><i id="catIconPreview" class="fas fa-tag"></i></span>
                            <input type="text" name="icon" id="catIcon" class="form-control" placeholder="fas fa-laptop" oninput="document.getElementById('catIconPreview').className = this.value || 'fas fa-tag'">
                        </div>
                        <small class="text-muted mt-1 d-block">Copy thẻ class dán vào (VD: <code>fas fa-star</code>, <code>fas fa-tshirt</code>)</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mô tả</label>
                        <textarea name="description" id="catDescription" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer admin-modal-footer">
                    <button type="button" class="btn btn-secondary admin-btn-rounded" data-bs-dismiss="modal">HỦY</button>
                    <button type="submit" class="btn btn-primary admin-btn-rounded" id="categorySubmitBtn">TẠO MỚI</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function openCategoryModal(id = null, name = '', slug = '', icon = '', description = '') {
    const modalTitle = document.getElementById('categoryModalTitle');
    const form = document.getElementById('categoryForm');
    const methodInput = document.getElementById('categoryMethod');
    const submitBtn = document.getElementById('categorySubmitBtn');

    // Reset inputs
    document.getElementById('catName').value = name;
    document.getElementById('catSlug').value = slug;
    document.getElementById('catIcon').value = icon;
    document.getElementById('catDescription').value = description;
    document.getElementById('catIconPreview').className = icon || 'fas fa-tag';

    if (id) {
        modalTitle.textContent = 'CHỈNH SỬA DANH MỤC';
        form.action = `/admin/categories/${id}`;
        methodInput.value = 'PUT';
        submitBtn.textContent = 'CẬP NHẬT';
    } else {
        modalTitle.textContent = 'TẠO DANH MỤC MỚI';
        form.action = '{{ route("admin.categories.store") }}';
        methodInput.value = 'POST';
        submitBtn.textContent = 'TẠO MỚI';
    }

    new bootstrap.Modal(document.getElementById('categoryModal')).show();
}
</script>
@endpush
@endsection
