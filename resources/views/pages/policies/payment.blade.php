@extends('layouts.app')

@section('title', 'Chính sách thanh toán - Sàn Tím Vi En')
@section('meta_description', 'Thông tin các phương thức thanh toán hiện có trên website Sàn Tím Vi En: VietQR và thanh toán khi nhận hàng (COD).')
@section('canonical', 'https://santimvien.vn/chinh-sach/thanh-toan')
@section('og_title', 'Chính sách thanh toán - Sàn Tím Vi En')
@section('og_description', 'Website hiện hỗ trợ thanh toán VietQR và COD tại bước đặt hàng.')

@php
    $breadcrumbSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => [
            ['@type' => 'ListItem', 'position' => 1, 'name' => 'Trang chủ', 'item' => 'https://santimvien.vn/'],
            ['@type' => 'ListItem', 'position' => 2, 'name' => 'Chính sách thanh toán', 'item' => 'https://santimvien.vn/chinh-sach/thanh-toan'],
        ],
    ];
@endphp

@section('content')
<script type="application/ld+json">{!! json_encode($breadcrumbSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
<div class="container py-5">
    <nav class="mb-4" aria-label="breadcrumb"><a href="{{ route('home') }}">Trang chủ</a> / Chính sách thanh toán</nav>
    <div class="row justify-content-center"><article class="col-lg-9">
        <h1 class="page-title text-start">Chính sách thanh toán</h1>
        <p class="lead">Tại bước thanh toán, website hiện cho phép khách hàng chọn một trong hai phương thức dưới đây.</p>
        <h2>Chuyển khoản VietQR</h2>
        <p>Khách hàng chọn VietQR tại trang thanh toán và thực hiện chuyển khoản bằng ứng dụng ngân hàng theo thông tin hiển thị trong luồng đặt hàng.</p>
        <h2>Thanh toán khi nhận hàng (COD)</h2>
        <p>Khách hàng chọn COD tại trang thanh toán và thanh toán khi nhận hàng theo đơn đã đặt.</p>
        <h2>Kiểm tra đơn hàng</h2>
        <p>Sau khi đặt hàng, khách hàng đăng nhập có thể truy cập danh sách đơn mua để xem trạng thái đơn và trạng thái thanh toán đang được ghi nhận trên website.</p>
        <p class="mt-4"><a href="{{ route('support.purchase-guide') }}">Xem hướng dẫn mua hàng</a> hoặc <a href="{{ route('products.index') }}">tiếp tục chọn sản phẩm</a>.</p>
    </article></div>
</div>
@endsection
