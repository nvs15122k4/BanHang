@props(['product'])

<div class="col-md-6 col-lg-3 mb-4">
    <div class="card product-card h-100">
        <div class="image-container">
            <img src="{{ $product->image_path }}" 
                 alt="{{ $product->ten_sp }}" 
                 class="product-card-image">
        </div>
        <div class="card-body d-flex flex-column">
            <h5 class="card-title">{{ $product->ten_sp }}</h5>
            <p class="card-text text-muted flex-grow-1">
                {{ Str::limit($product->mo_ta, 80) }}
            </p>
            <div class="mt-auto">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="h5 text-primary mb-0">
                        {{ number_format($product->getGiaFormatted(), 0, ',', '.') }}đ
                    </span>
                    @if($product->trang_thai == 'con')
                        <span class="badge bg-success">Còn hàng</span>
                    @else
                        <span class="badge bg-danger">Hết hàng</span>
                    @endif
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted">
                        <i class="fas fa-box"></i> {{ $product->so_luong }}
                    </small>
                    <div class="btn-group" role="group">
                        <a href="{{ route('products.show', $product) }}" 
                           class="btn btn-sm btn-outline-info" 
                           title="Xem chi tiết">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('products.edit', $product) }}" 
                           class="btn btn-sm btn-outline-warning" 
                           title="Chỉnh sửa">
                            <i class="fas fa-edit"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>