@extends('layouts.app')

@section('title', 'Hướng dẫn mua hàng - Sàn Tím Vi En')
@section('meta_description', 'Các bước chọn sản phẩm, chọn size, đặt hàng bằng COD hoặc VietQR và theo dõi đơn mua trên website Sàn Tím Vi En.')
@section('canonical', 'https://santimvien.vn/ho-tro/huong-dan-mua-hang')
@section('og_title', 'Hướng dẫn mua hàng - Sàn Tím Vi En')
@section('og_description', 'Các bước đặt hàng và theo dõi đơn mua trên website.')

@section('content')
<div class="container py-5">
    <nav class="mb-4" aria-label="breadcrumb"><a href="{{ route('home') }}">Trang chủ</a> / Hướng dẫn mua hàng</nav>
    <div class="row justify-content-center"><article class="col-lg-9">
        <h1 class="page-title text-start">Hướng dẫn mua hàng</h1>
        <h2>1. Chọn sản phẩm</h2>
        <p>Truy cập danh sách sản phẩm, mở trang chi tiết để xem hình ảnh, mô tả, giá và tình trạng còn hàng.</p>
        <h2>2. Chọn size nếu sản phẩm yêu cầu</h2>
        <p>Với sản phẩm có lựa chọn kích cỡ, hãy chọn size trước khi thêm vào giỏ hàng. Bạn có thể tham khảo <a href="{{ route('guides.size') }}">hướng dẫn chọn size</a>.</p>
        <h2>3. Đăng nhập và thanh toán</h2>
        <p>Giỏ hàng và bước thanh toán yêu cầu đăng nhập. Tại checkout, bạn nhập địa chỉ giao hàng và chọn VietQR hoặc COD.</p>
        <h2>4. Theo dõi đơn</h2>
        <p>Sau khi đặt hàng, vào mục đơn mua trong tài khoản để xem trạng thái xử lý của đơn.</p>
        <div class="d-flex flex-wrap gap-2 mt-4">
            <a class="btn btn-dark" href="{{ route('products.index') }}">Bắt đầu chọn sản phẩm</a>
            <a class="btn btn-outline-dark" href="{{ route('policies.payment') }}">Xem thanh toán</a>
        </div>
    </article></div>
</div>
@endsection
