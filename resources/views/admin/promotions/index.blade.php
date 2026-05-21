@extends('layouts.admin')
@section('title', 'Quản lý Khuyến Mãi')

@push('styles')
@vite(['public/css/admin_common.css'])
@endpush

@section('content')
<div class="px-4 py-4">

  {{-- Header --}}
  <div class="d-flex align-items-center justify-content-between mb-4">
    <div>
      <h4 class="fw-bold mb-0"><i class="fas fa-tags text-primary me-2"></i>Quản lý Khuyến Mãi</h4>
      <p class="text-muted small mb-0">Tạo và quản lý các chương trình khuyến mãi tự động</p>
    </div>
    <a href="{{ route('admin.promotions.create') }}" class="btn btn-primary">
      <i class="fas fa-plus me-2"></i>Tạo Khuyến Mãi
    </a>
  </div>

  {{-- Stats Cards --}}
  @php
    $now = now();
    $running = $promotions->getCollection()->filter(fn($p) => $p->status_label === 'Đang chạy')->count();
  @endphp
  <div class="row g-3 mb-4">
    <div class="col-md-3 col-6">
      <div class="card border-0 shadow-sm text-center py-3">
        <div class="fw-bold fs-3 text-primary">{{ $promotions->total() }}</div>
        <div class="text-muted small">Tổng KM</div>
      </div>
    </div>
    <div class="col-md-3 col-6">
      <div class="card border-0 shadow-sm text-center py-3">
        <div class="fw-bold fs-3 text-success">{{ $running }}</div>
        <div class="text-muted small">Đang chạy</div>
      </div>
    </div>
    <div class="col-md-3 col-6">
      <div class="card border-0 shadow-sm text-center py-3">
        <div class="fw-bold fs-3 text-warning">{{ \App\Models\Promotion::where('trang_thai','active')->where('ngay_bat_dau','>',now())->count() }}</div>
        <div class="text-muted small">Sắp diễn ra</div>
      </div>
    </div>
    <div class="col-md-3 col-6">
      <div class="card border-0 shadow-sm text-center py-3">
        <div class="fw-bold fs-3 text-secondary">{{ \App\Models\Promotion::where('trang_thai','inactive')->count() }}</div>
        <div class="text-muted small">Đã tắt</div>
      </div>
    </div>
  </div>

  {{-- Filter --}}
  <div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
      <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-4">
          <input type="text" name="search" class="form-control" placeholder="Tìm tên khuyến mãi..." value="{{ request('search') }}">
        </div>
        <div class="col-md-3">
          <select name="trang_thai" class="form-select">
            <option value="">-- Trạng thái --</option>
            <option value="running" {{ request('trang_thai')=='running'?'selected':'' }}>Đang chạy</option>
            <option value="active" {{ request('trang_thai')=='active'?'selected':'' }}>Active</option>
            <option value="inactive" {{ request('trang_thai')=='inactive'?'selected':'' }}>Đã tắt</option>
          </select>
        </div>
        <div class="col-md-3">
          <select name="pham_vi" class="form-select">
            <option value="">-- Phạm vi --</option>
            <option value="all" {{ request('pham_vi')=='all'?'selected':'' }}>Toàn bộ</option>
            <option value="category" {{ request('pham_vi')=='category'?'selected':'' }}>Theo danh mục</option>
            <option value="product" {{ request('pham_vi')=='product'?'selected':'' }}>Sản phẩm cụ thể</option>
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
        <table class="table table-promo table-hover mb-0">
          <thead class="bg-light">
            <tr>
              <th class="ps-4">Khuyến Mãi</th>
              <th>Giá Trị</th>
              <th>Phạm Vi</th>
              <th>Thời Gian</th>
              <th>Trạng Thái</th>
              <th class="text-end pe-4">Thao Tác</th>
            </tr>
          </thead>
          <tbody>
            @forelse($promotions as $promo)
            <tr>
              <td class="ps-4">
                <div class="d-flex align-items-center gap-2">
                  @if($promo->tag)
                    <span class="promo-tag">{{ $promo->tag }}</span>
                  @endif
                  <div>
                    <div class="fw-semibold">{{ $promo->ten }}</div>
                    <div class="text-muted small">{{ Str::limit($promo->mo_ta, 50) }}</div>
                  </div>
                </div>
              </td>
              <td>
                <span class="discount-val">{{ $promo->formatted_value }}</span>
                @if($promo->loai_km === 'percent' && $promo->gia_tri_toi_da)
                  <div class="text-muted small">Tối đa {{ number_format($promo->gia_tri_toi_da,0,',','.') }}đ</div>
                @endif
              </td>
              <td>
                @if($promo->pham_vi === 'all')
                  <span class="scope-pill uix-0947da2265">🌐 Toàn bộ</span>
                @elseif($promo->pham_vi === 'category')
                  <span class="scope-pill">📂 Danh mục</span>
                  <div class="text-muted small">{{ $promo->items->pluck('gia_tri')->join(', ') }}</div>
                @else
                  <span class="scope-pill uix-ea3ce52629">📦 {{ $promo->items->count() }} sản phẩm</span>
                @endif
              </td>
              <td>
                <div class="small"><i class="fas fa-calendar-alt text-muted me-1"></i>{{ $promo->ngay_bat_dau->format('d/m/Y') }}</div>
                <div class="small text-muted">→ {{ $promo->ngay_ket_thuc->format('d/m/Y') }}</div>
              </td>
              <td>
                <span class="promo-status-badge badge bg-{{ $promo->status_class }}">{{ $promo->status_label }}</span>
              </td>
              <td class="text-end pe-4">
                <div class="d-flex gap-2 justify-content-end">
                  {{-- Toggle --}}
                  <form method="POST" action="{{ route('admin.promotions.toggle', $promo) }}">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn btn-sm {{ $promo->trang_thai === 'active' ? 'btn-outline-warning' : 'btn-outline-success' }}"
                      title="{{ $promo->trang_thai === 'active' ? 'Tắt' : 'Bật' }}">
                      <i class="fas {{ $promo->trang_thai === 'active' ? 'fa-toggle-on' : 'fa-toggle-off' }}"></i>
                    </button>
                  </form>
                  {{-- Edit --}}
                  <a href="{{ route('admin.promotions.edit', $promo->id) }}" class="btn btn-sm btn-outline-primary" title="Sửa">
                    <i class="fas fa-edit"></i>
                  </a>
                  {{-- Delete --}}
                  <form method="POST" action="{{ route('admin.promotions.destroy', $promo) }}" id="del-{{ $promo->id }}">
                    @csrf @method('DELETE')
                    <button type="button" class="btn btn-sm btn-outline-danger"
                      onclick="stConfirmDelete({title:'Xóa khuyến mãi',pill:'{{ addslashes($promo->ten) }}',message:'KM này và toàn bộ phạm vi áp dụng sẽ bị xóa vĩnh viễn.',onConfirm:()=>document.getElementById('del-{{ $promo->id }}').submit()})">
                      <i class="fas fa-trash"></i>
                    </button>
                  </form>
                </div>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="6" class="text-center py-5 text-muted">
                <i class="fas fa-tags fa-3x mb-3 d-block"></i>
                Chưa có khuyến mãi nào. <a href="{{ route('admin.promotions.create') }}">Tạo ngay</a>
              </td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
    @if($promotions->hasPages())
    <div class="card-footer bg-transparent">
      {{ $promotions->links() }}
    </div>
    @endif
  </div>
</div>

@endsection
