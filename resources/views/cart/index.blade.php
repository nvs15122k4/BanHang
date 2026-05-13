@extends('layouts.app')

@section('title', 'Giỏ hàng - AVA')

@push('styles')
<style>
    /* =========================================
       CART PAGE - AVA STYLE
       ========================================= */
    .page-title {
        font-weight: 700;
        font-size: 32px;
        color: var(--text-main);
        text-align: center;
        margin: 40px 0;
        text-transform: uppercase;
    }

    .empty-cart-msg {
        text-align: center;
        padding: 80px 0;
    }

    .cart-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 40px;
    }
    
    .cart-table th {
        border-bottom: 2px solid #EEEEEE;
        padding: 15px;
        font-weight: 600;
        color: var(--text-main);
        text-transform: uppercase;
        font-size: 14px;
    }
    
    .cart-table td {
        padding: 20px 15px;
        border-bottom: 1px solid #EEEEEE;
        vertical-align: middle;
    }

    .cart-item-img {
        width: 80px;
        height: 80px;
        object-fit: cover;
        background: #F6F6F6;
    }

    .cart-item-title {
        font-weight: 600;
        color: var(--text-main);
        text-decoration: none;
    }
    
    .cart-item-title:hover {
        color: var(--text-light);
    }

    .cart-item-price, .cart-item-subtotal {
        font-weight: 700;
        color: var(--text-main);
    }

    .qty-input-wrap {
        display: flex;
        align-items: center;
        border: 1px solid #DDDDDD;
        width: max-content;
    }
    
    .qty-input-wrap input {
        border: none;
        text-align: center;
        width: 50px;
        padding: 10px 0;
        font-weight: 600;
        outline: none;
    }
    
    .qty-input-wrap input::-webkit-outer-spin-button, .qty-input-wrap input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
    
    .btn-remove {
        background: transparent;
        color: #E74C3C;
        border: none;
        font-size: 18px;
    }

    .summary-card {
        background: #F6F6F6;
        padding: 40px;
    }
    
    .summary-title {
        font-weight: 700;
        font-size: 20px;
        margin-bottom: 30px;
        text-transform: uppercase;
        border-bottom: 1px solid #DDDDDD;
        padding-bottom: 15px;
    }
    
    .summary-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 15px;
        color: var(--text-main);
        font-weight: 500;
    }
    
    .summary-total {
        display: flex;
        justify-content: space-between;
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid #DDDDDD;
        font-weight: 700;
        font-size: 24px;
        color: var(--text-main);
    }

    @media (max-width: 991px) {
        .cart-table thead { display: none; }
        .cart-table, .cart-table tbody, .cart-table tr, .cart-table td { display: block; width: 100%; }
        .cart-table tr { border-bottom: 1px solid #EEEEEE; padding-bottom: 20px; margin-bottom: 20px; }
        .cart-table td { padding: 10px 0; border: none; text-align: right; display: flex; justify-content: space-between; align-items: center; }
        .cart-table td::before { content: attr(data-label); font-weight: 600; font-size: 14px; text-transform: uppercase; }
        .td-product { flex-direction: column !important; align-items: flex-start !important; }
        .td-product::before { display: none; }
    }
</style>
@endpush

@section('content')
<div class="container py-4">
    <h1 class="page-title">GIỎ HÀNG</h1>

    @if(session('success'))
        <div class="alert alert-success rounded-0 border-0" style="background-color: #E8F5E9; color: #2E7D32;">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger rounded-0 border-0" style="background-color: #FFEBEE; color: #C62828;">
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
                                            <a href="{{ route('products.show', $product->id) }}" class="cart-item-title">
                                                {{ $product->ten_sp }}
                                            </a>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center" data-label="Giá">
                                    <span class="cart-item-price">{{ number_format($product->gia) }}đ</span>
                                </td>
                                <td data-label="Số lượng">
                                    <div class="d-flex justify-content-lg-center">
                                        <form action="{{ route('cart.update', $product->id) }}" method="POST" class="qty-input-wrap">
                                            @csrf
                                            @method('PATCH')
                                            <input type="number" name="so_luong" value="{{ $item['so_luong'] }}" min="1" onchange="this.form.submit()">
                                        </form>
                                    </div>
                                </td>
                                <td class="text-end" data-label="Tổng cộng">
                                    <span class="cart-item-subtotal">{{ number_format($item['subtotal']) }}đ</span>
                                </td>
                                <td class="text-end" data-label="Hành động">
                                    <form action="{{ route('cart.remove', $product->id) }}" method="POST">
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
                    <a href="{{ route('products.index') }}" class="btn btn-outline-dark" style="border-radius: 0;">TIẾP TỤC MUA SẮM</a>
                    <form action="{{ route('cart.clear') }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-link text-muted p-0" style="font-size: 14px; text-decoration: none;" onclick="return confirm('Xóa tất cả sản phẩm khỏi giỏ hàng?')">XÓA GIỎ HÀNG</button>
                    </form>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="summary-card">
                    <h2 class="summary-title">Tóm tắt đơn hàng</h2>
                    
                    <div class="summary-row">
                        <span>Tạm tính</span>
                        <span>{{ number_format($total) }}đ</span>
                    </div>
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
            <h3 style="font-weight: 700;">GIỎ HÀNG CỦA BẠN ĐANG TRỐNG</h3>
            <p class="text-muted mb-4">Trước khi tiến hành thanh toán, bạn phải thêm sản phẩm vào giỏ hàng. Bạn sẽ tìm thấy nhiều sản phẩm thú vị trên trang "Cửa hàng" của chúng tôi.</p>
            <a href="{{ route('products.index') }}" class="btn btn-ava-dark px-5 py-3 bg-black text-white">BẮT ĐẦU MUA SẮM</a>
        </div>
    @endif
</div>
@endsection
