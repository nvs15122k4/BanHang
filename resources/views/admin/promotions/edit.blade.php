@extends('layouts.admin')
@section('title', 'Chỉnh Sửa Khuyến Mãi')

@push('styles')
<style>
.pham-vi-card { border: 2px solid #e5e7eb; border-radius: 12px; padding: 16px; cursor: pointer; transition: all .2s; }
.pham-vi-card:hover, .pham-vi-card.selected { border-color: #6366f1; background: #eef2ff; }
.pham-vi-card input[type=radio] { accent-color: #6366f1; }
.step-badge { width:28px; height:28px; border-radius:50%; background:#6366f1; color:#fff; display:inline-flex; align-items:center; justify-content:center; font-weight:700; font-size:13px; }
.product-checkbox-item { border:1px solid #e5e7eb; border-radius:8px; padding:8px 12px; margin-bottom:6px; display:flex; align-items:center; gap:10px; cursor:pointer; }
.product-checkbox-item:hover { background:#f9fafb; }
.product-checkbox-item input { accent-color:#6366f1; }
</style>
@endpush

@section('content')
<div class="px-4 py-4">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('admin.promotions.index') }}" class="btn btn-light me-3"><i class="fas fa-arrow-left"></i> Quay lại</a>
        <h4 class="fw-bold mb-0"><i class="fas fa-edit text-warning me-2"></i>Chỉnh Sửa Khuyến Mãi</h4>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('admin.promotions.update', $promotion) }}">
                @csrf @method('PUT')
                @include('admin.promotions._form', ['promo' => $promotion, 'categories' => $categories, 'products' => $products])
                
                <hr class="my-4">
                <div class="text-end">
                    <a href="{{ route('admin.promotions.index') }}" class="btn btn-light me-2">Hủy</a>
                    <button type="submit" class="btn btn-warning text-white px-5"><i class="fas fa-save me-2"></i>Cập Nhật</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Pre-check for edit
const selectedItems = @json($promotion->items);
if(selectedItems && selectedItems.length > 0) {
    document.querySelectorAll('[name="categories[]"]').forEach(cb => {
        if (selectedItems.some(i => i.loai === 'category' && i.gia_tri === cb.value)) {
            cb.checked = true;
        }
    });
    document.querySelectorAll('[name="product_ids[]"]').forEach(cb => {
        if (selectedItems.some(i => i.loai === 'product' && i.gia_tri === String(cb.value))) {
            cb.checked = true;
        }
    });
}

function updateScopeUI(val) {
  const catSel  = document.querySelector('.categorySelector');
  const prodSel = document.querySelector('.productSelector');
  if (catSel)  catSel.style.display  = val === 'category' ? 'block' : 'none';
  if (prodSel) prodSel.style.display = val === 'product'  ? 'block' : 'none';
}

document.querySelectorAll('[name="pham_vi"]').forEach(r => {
  r.addEventListener('change', function() {
    document.querySelectorAll('.pham-vi-card').forEach(card => card.classList.remove('selected'));
    if (this.checked) {
      this.closest('.pham-vi-card').classList.add('selected');
    }
    updateScopeUI(this.value);
  });
});

function filterProducts(input) {
  const term = input.value.toLowerCase();
  document.querySelectorAll('#productList .product-checkbox-item').forEach(el => {
    if (el.dataset.name.includes(term)) {
      el.style.display = 'flex';
    } else {
      el.style.display = 'none';
    }
  });
}
</script>
@endpush
