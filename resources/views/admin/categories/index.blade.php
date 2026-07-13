@extends('layouts.admin')

@section('title', 'Quản lý Danh mục')

@push('styles')
    @vite(['public/css/admin_common.css'])
    <style>
        .category-name-cell { min-width: 280px; }
        .category-toggle { width: 28px; text-align: center; }
        .category-tree-line { color: #94a3b8; margin-right: .45rem; }
    </style>
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

@if($errors->any())
    <div class="alert alert-danger">{{ $errors->first() }}</div>
@endif

<div class="card admin-table">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Danh mục phân tầng</th>
                    <th>Mã (Slug)</th>
                    <th>Vai trò</th>
                    <th>Mô tả</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $category)
                    <tr id="category-row-{{ $category->id }}"
                        class="category-row {{ $category->parent_id ? 'd-none' : '' }}"
                        data-id="{{ $category->id }}"
                        data-parent-id="{{ $category->parent_id ?? '' }}">
                        <td class="category-name-cell font-medium-custom">
                            <div class="d-flex align-items-center" style="padding-left: {{ $category->depth * 26 }}px;">
                                @if($category->has_children)
                                    <button type="button" id="toggle-{{ $category->id }}" class="btn btn-sm btn-link text-decoration-none p-0 me-2 category-toggle" onclick="openCategoryBranch({{ $category->id }})" title="Xem danh mục con">
                                        <i class="fas fa-chevron-right"></i>
                                    </button>
                                @else
                                    <span class="category-toggle me-2"></span>
                                @endif
                                @if($category->depth > 0)
                                    <span class="category-tree-line">└──</span>
                                @endif
                                <i class="{{ $category->icon }} me-2 text-primary"></i>
                                <span>{{ $category->name }}</span>
                                @if($category->is_new)
                                    <span id="new-badge-{{ $category->id }}" class="badge bg-success ms-2">Mới</span>
                                @endif
                            </div>
                        </td>
                        <td><code>{{ $category->slug }}</code></td>
                        <td>
                            @if($category->has_children)
                                <span class="badge bg-primary">Danh mục cha</span>
                            @else
                                <span class="badge bg-light text-dark">Danh mục con</span>
                            @endif
                        </td>
                        <td>{{ Str::limit((string) $category->description, 50) ?: '-' }}</td>
                        <td>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="markCategorySeen({{ $category->id }})" title="Xem danh mục">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-primary"
                                    onclick='editCategory(@js(["id" => $category->id, "name" => $category->name, "slug" => $category->slug, "icon" => $category->icon, "description" => $category->description, "parent_id" => $category->parent_id]))'
                                    title="Chỉnh sửa danh mục">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" class="d-inline" id="form-delete-{{ $category->id }}">
                                    @csrf @method('DELETE')
                                    <button type="button" class="btn btn-sm btn-outline-danger" title="Xóa danh mục"
                                        onclick="showConfirm(
                                            'Bạn có chắc chắn muốn xóa danh mục &quot;{{ addslashes($category->name) }}&quot;?',
                                            () => document.getElementById('form-delete-{{ $category->id }}').submit(),
                                            'XÓA DANH MỤC',
                                            'danger',
                                            'XÓA DANH MỤC',
                                            '{{ addslashes($category->name) }}',
                                            'Danh mục chỉ được xóa khi không còn sản phẩm hoặc danh mục con.'
                                        )">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-5">
                            <i class="fas fa-tags fa-3x mb-3 d-block"></i>
                            Chưa có danh mục nào
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

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
                        <small class="text-muted">Ví dụ: <code>ao-polo-nam</code>. Slug dùng trên đường dẫn URL.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Danh mục cha</label>
                        <select name="parent_id" id="catParentId" class="form-select">
                            <option value="">-- Danh mục gốc (không có cha) --</option>
                            @foreach($categories as $parentCategory)
                                <option value="{{ $parentCategory->id }}">{{ $parentCategory->path }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted">Ví dụ: chọn <strong>Thời trang nam &gt; Áo nam</strong> trước khi tạo <strong>Áo polo nam</strong>.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Icon (FontAwesome)</label>
                        <div class="input-group">
                            <span class="input-group-text"><i id="catIconPreview" class="fas fa-tag"></i></span>
                            <input type="text" name="icon" id="catIcon" class="form-control" placeholder="fas fa-tag" oninput="document.getElementById('catIconPreview').className = this.value || 'fas fa-tag'">
                        </div>
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
const categorySeenUrl = @json(route('admin.categories.seen', ['category' => '__id__']));

function markCategorySeen(id) {
    const badge = document.getElementById(`new-badge-${id}`);
    if (!badge) {
        return Promise.resolve();
    }

    return fetch(categorySeenUrl.replace('__id__', id), {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    }).then(response => {
        if (response.ok) {
            badge.remove();
        }
    });
}

function collapseDescendants(parentId) {
    document.querySelectorAll(`.category-row[data-parent-id="${parentId}"]`).forEach(row => {
        row.classList.add('d-none');
        collapseDescendants(row.dataset.id);
        const childToggle = document.getElementById(`toggle-${row.dataset.id}`);
        childToggle?.querySelector('i')?.classList.replace('fa-chevron-down', 'fa-chevron-right');
        childToggle?.setAttribute('data-open', 'false');
    });
}

function openCategoryBranch(id) {
    markCategorySeen(id);
    const button = document.getElementById(`toggle-${id}`);
    const isOpen = button.dataset.open === 'true';
    const children = document.querySelectorAll(`.category-row[data-parent-id="${id}"]`);

    if (isOpen) {
        collapseDescendants(id);
        button.querySelector('i').classList.replace('fa-chevron-down', 'fa-chevron-right');
        button.dataset.open = 'false';
        return;
    }

    children.forEach(row => row.classList.remove('d-none'));
    button.querySelector('i').classList.replace('fa-chevron-right', 'fa-chevron-down');
    button.dataset.open = 'true';
}

function editCategory(category) {
    markCategorySeen(category.id);
    openCategoryModal(category);
}

function openCategoryModal(category = null) {
    const data = category ?? { id: null, name: '', slug: '', icon: '', description: '', parent_id: '' };
    const modalTitle = document.getElementById('categoryModalTitle');
    const form = document.getElementById('categoryForm');
    const methodInput = document.getElementById('categoryMethod');
    const submitBtn = document.getElementById('categorySubmitBtn');
    const parentSelect = document.getElementById('catParentId');

    document.getElementById('catName').value = data.name ?? '';
    document.getElementById('catSlug').value = data.slug ?? '';
    document.getElementById('catIcon').value = data.icon ?? '';
    document.getElementById('catDescription').value = data.description ?? '';
    parentSelect.value = data.parent_id ?? '';
    document.getElementById('catIconPreview').className = data.icon || 'fas fa-tag';

    Array.from(parentSelect.options).forEach(option => {
        option.disabled = data.id && Number(option.value) === Number(data.id);
    });

    if (data.id) {
        modalTitle.textContent = 'CHỈNH SỬA DANH MỤC';
        form.action = `/admin/categories/${data.id}`;
        methodInput.value = 'PUT';
        submitBtn.textContent = 'CẬP NHẬT';
    } else {
        modalTitle.textContent = 'TẠO DANH MỤC MỚI';
        form.action = @json(route('admin.categories.store'));
        methodInput.value = 'POST';
        submitBtn.textContent = 'TẠO MỚI';
    }

    new bootstrap.Modal(document.getElementById('categoryModal')).show();
}
</script>
@endpush
@endsection
