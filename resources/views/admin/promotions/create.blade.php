@extends('layouts.admin')
@section('title', 'Tạo Khuyến Mãi Mới')

@push('styles')
@vite(['resources/css/admin_common.css'])
@endpush

@section('content')
<div class="px-4 py-4">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('admin.promotions.index') }}" class="btn btn-light me-3"><i class="fas fa-arrow-left"></i> Quay lại</a>
        <h4 class="fw-bold mb-0"><i class="fas fa-plus-circle text-primary me-2"></i>Tạo Khuyến Mãi Mới</h4>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('admin.promotions.store') }}">
                @csrf
                @include('admin.promotions._form', ['promo' => $promotion, 'categories' => $categories, 'products' => $products])
                
                <hr class="my-4">
                <div class="text-end">
                    <a href="{{ route('admin.promotions.index') }}" class="btn btn-light me-2">Hủy</a>
                    <button type="submit" class="btn btn-primary px-5"><i class="fas fa-save me-2"></i>Tạo Khuyến Mãi</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
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
