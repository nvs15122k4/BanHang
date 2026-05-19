@extends('layouts.admin')
@section('title', 'Chỉnh sửa Sản phẩm')

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
      <h4 class="fw-bold mb-0 text-dark">Chỉnh sửa Sản phẩm: <span class="text-primary">#{{ $product->id }}</span></h4>
    </div>
  </div>

  <div class="row">
    <div class="col-12">
      <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
          <form action="{{ route('products.update', $product) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <input type="hidden" name="_ref" value="{{ old('_ref', request()->headers->get('referer')) }}">

            @include('admin.products._form', ['product' => $product])

            <div class="d-flex gap-2 mt-4 pt-3 border-top">
              <button type="submit" class="btn btn-primary px-4">
                <i class="fas fa-save me-2"></i>Cập nhật Sản phẩm
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
