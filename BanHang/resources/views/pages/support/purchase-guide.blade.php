@extends('layouts.app')

@section('title', 'Hướng dẫn mua hàng - Sàn Tím Vi En')
@section('meta_description', 'Các bước chọn sản phẩm, chọn size, đặt hàng bằng COD hoặc VietQR và theo dõi đơn mua trên website Sàn Tím Vi En.')
@section('canonical', 'https://santimvien.vn/ho-tro/huong-dan-mua-hang')
@section('og_title', 'Hướng dẫn mua hàng - Sàn Tím Vi En')
@section('og_description', 'Các bước đặt hàng và theo dõi đơn mua trên website.')

@push('styles')
    @vite(['public/css/views/static_pages.css'])
@endpush

@section('content')
<section class="content-hero">
    <div class="container">
        <nav class="content-breadcrumb" aria-label="breadcrumb"><a href="{{ route('home') }}">Trang chủ</a> / Hỗ trợ / Hướng dẫn mua hàng</nav>
        <span class="content-eyebrow">Mua sắm trực tuyến</span>
        <h1 class="page-title">Hướng dẫn mua hàng</h1>
        <p class="content-intro">Bốn bước đơn giản để tìm sản phẩm, chọn size, thanh toán và kiểm tra đơn mua trên website.</p>
    </div>
</section>
<div class="container content-page">
    <div class="step-grid">
        <article class="step-card">
            <span class="step-number">01</span>
            <h2>Chọn sản phẩm</h2>
            <p>Mở trang chi tiết để xem hình ảnh, mô tả, giá và trạng thái còn hàng trước khi thêm vào giỏ.</p>
        </article>
        <article class="step-card">
            <span class="step-number">02</span>
            <h2>Chọn kích cỡ</h2>
            <p>Nếu sản phẩm có size, hãy chọn kích cỡ trước khi thêm vào giỏ. Bạn có thể xem <a href="{{ route('guides.size') }}">hướng dẫn chọn size</a>.</p>
        </article>
        <article class="step-card">
            <span class="step-number">03</span>
            <h2>Đăng nhập và thanh toán</h2>
            <p>Tại checkout, bạn nhập địa chỉ giao hàng và lựa chọn VietQR hoặc thanh toán khi nhận hàng (COD).</p>
        </article>
        <article class="step-card">
            <span class="step-number">04</span>
            <h2>Xem lại đơn mua</h2>
            <p>Sau khi đặt hàng, đăng nhập vào tài khoản và mở mục đơn mua để xem trạng thái xử lý.</p>
        </article>
    </div>
    <div class="content-actions">
            <a class="btn-primary-content" href="{{ route('products.index') }}">Bắt đầu mua sắm</a>
            <a class="btn-outline-content" href="{{ route('policies.payment') }}">Xem thanh toán</a>
        </div>
</div>
@endsection
