@extends('layouts.app')

@section('title', 'Giỏ hàng - AVA')
@section('robots', 'noindex, nofollow')

@push('styles')
    @vite(['public/css/views/cart.css'])
@endpush

@section('content')
<div class="container py-4">
    <h1 class="page-title">GIỎ HÀNG</h1>

    @if(session('success'))
        <div class="alert alert-success rounded-0 border-0 bg-success-light-custom text-success-custom">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger rounded-0 border-0 bg-danger-light-custom text-danger-custom">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        </div>
    @endif

    @if(count($items) > 0)
        <div class="row">
            <div class="col-lg-8 mb-4">
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th>Sản phẩm</th>
                            <th class="text-center">Kích cỡ</th>
                            <th class="text-center">Giá</th>
                            <th class="text-center">Số lượng</th>
                            <th class="text-end">Tổng cộng</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items as $item)
                            @php $product = $item['product']; @endphp
                            <tr>
                                <td class="td-product" data-label="Product">
                                    <div class="d-flex align-items-center gap-3">
                                        @if($product->anh)
                                            <img src="{{ $product->image_path }}" class="cart-item-img" alt="{{ $product->ten_sp }}">
                                        @else
                                            <div class="cart-item-img d-flex align-items-center justify-content-center">
                                                <i class="fas fa-image text-muted"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <a href="{{ route('products.show', ['product' => $product->slug]) }}" class="cart-item-title">
                                                {{ $product->ten_sp }}
                                            </a>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center" data-label="Kích cỡ">
                                    @if(isset($item['size']) && $item['size'] !== 'default')
                                        <span class="badge bg-secondary">{{ $item['size'] }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center" data-label="Giá">
                                    @if(isset($item['promo']) && $item['promo'])
                                        <div class="cart-item-price text-danger fw-bold mb-1">{{ number_format($item['gia_ban']) }}đ</div>
                                        <div class="cart-item-price text-muted text-decoration-line-through uix-46c1af49ee">{{ number_format($item['gia_goc']) }}đ</div>
                                        <div class="badge bg-danger mt-1 uix-c1bee9e2b2">-{{ number_format($item['gia_goc'] - $item['gia_ban']) }}đ</div>
                                    @else
                                        <span class="cart-item-price">{{ number_format($item['gia_goc']) }}đ</span>
                                    @endif
                                </td>
                                <td data-label="Số lượng">
                                    <div class="d-flex justify-content-lg-center">
                                        <form action="{{ route('cart.update', $item['cart_key']) }}" method="POST" class="qty-input-wrap">
                                            @csrf
                                            @method('PATCH')
                                            <input type="number" name="so_luong" value="{{ $item['so_luong'] }}" min="1" onchange="window.ST_SAVE_SCROLL && window.ST_SAVE_SCROLL(); this.form.submit()">
                                        </form>
                                    </div>
                                </td>
                                <td class="text-end" data-label="Tổng cộng">
                                    <span class="cart-item-subtotal">{{ number_format($item['subtotal']) }}đ</span>
                                </td>
                                <td class="text-end" data-label="Hành động">
                                    <form action="{{ route('cart.remove', $item['cart_key']) }}" method="POST" data-item-name="{{ $product->ten_sp }}" onsubmit="return confirmForm(this, 'Sản phẩm sẽ bị xóa vĩnh viễn khỏi giỏ hàng và không thể khôi phục.', 'XÓA SẢN PHẨM')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-remove" title="Xóa sản phẩm"><i class="fas fa-times"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                
                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('products.index') }}" class="btn btn-outline-dark rounded-0">TIẾP TỤC MUA SẮM</a>
                    <form action="{{ route('cart.clear') }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-link text-muted p-0 text-sm-custom no-underline-custom" onclick="return confirmForm(this.form, 'Bạn có chắc chắn muốn xóa tất cả sản phẩm khỏi giỏ hàng?', 'XÓA GIỎ HÀNG')">XÓA GIỎ HÀNG</button>
                    </form>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="summary-card">
                    <h2 class="summary-title">Tóm tắt đơn hàng</h2>
                    
                    @php
                        $tongTienGoc = 0;
                        foreach($items as $i) $tongTienGoc += $i['gia_goc'] * $i['so_luong'];
                        $giamGia = $tongTienGoc - $total;
                    @endphp
                    <div class="summary-row">
                        <span>Tạm tính</span>
                        <span>{{ number_format($tongTienGoc) }}đ</span>
                    </div>
                    @if($giamGia > 0)
                    <div class="summary-row text-danger">
                        <span>Khuyến mãi</span>
                        <span>-{{ number_format($giamGia) }}đ</span>
                    </div>
                    @endif
                    <div class="summary-row">
                        <span>Phí vận chuyển</span>
                        <span class="text-success">Miễn phí</span>
                    </div>
                    
                    <div class="summary-total">
                        <span>Tổng cộng</span>
                        <span>{{ number_format($total) }}đ</span>
                    </div>
                    
                    <a href="{{ route('checkout.index') }}" class="btn btn-ava-dark w-100 mt-4 py-3 bg-black text-white">TIẾN HÀNH THANH TOÁN</a>
                </div>
            </div>
        </div>
    @else
        <div class="empty-cart-msg">
            <i class="fas fa-shopping-bag fa-4x text-muted mb-4"></i>
            <h3 class="font-bold">GIỎ HÀNG CỦA BẠN ĐANG TRỐNG</h3>
            <p class="text-muted mb-4">Trước khi tiến hành thanh toán, bạn phải thêm sản phẩm vào giỏ hàng. Bạn sẽ tìm thấy nhiều sản phẩm thú vị trên trang "Cửa hàng" của chúng tôi.</p>
            <a href="{{ route('products.index') }}" class="btn btn-ava-dark px-5 py-3 bg-black text-white">BẮT ĐẦU MUA SẮM</a>
        </div>
    @endif
</div>
@endsection
