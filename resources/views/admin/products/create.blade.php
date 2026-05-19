@extends('layouts.admin')
@section('title', 'Thêm Sản phẩm mới')

@push('styles')
@vite(['resources/css/admin_common.css'])
@endpush

@section('content')
<div class="px-4 py-4">

  {{-- Header --}}
  <div class="d-flex align-items-center justify-content-between mb-4">
    <div>
      <a href="{{ route('admin.products') }}" class="text-decoration-none text-muted small mb-2 d-inline-block">
        <i class="fas fa-arrow-left me-1"></i>Quay lại danh sách
      </a>
      <h4 class="fw-bold mb-0 text-dark">Thêm Sản phẩm mới</h4>
    </div>
  </div>

  <div class="row">
    <div class="col-12">
      <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
          <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="_ref" value="{{ old('_ref', request()->headers->get('referer')) }}">

            @include('admin.products._form', ['product' => new \App\Models\Product()])

            <div class="d-flex gap-2 mt-4 pt-3 border-top">
              <button type="submit" class="btn btn-primary px-4">
                <i class="fas fa-save me-2"></i>Lưu Sản phẩm
              </button>
              <a href="{{ old('_ref', request()->headers->get('referer')) ?: route('admin.products') }}" class="btn btn-light px-4">Hủy</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

</div>
@endsection
