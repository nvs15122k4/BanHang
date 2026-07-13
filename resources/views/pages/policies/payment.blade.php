@extends('layouts.app')

@section('title', 'Chính sách thanh toán - Sàn Tím Vi En')
@section('meta_description', 'Thông tin các phương thức thanh toán hiện có trên website Sàn Tím Vi En: VietQR và thanh toán khi nhận hàng (COD).')
@section('canonical', 'https://santimvien.vn/chinh-sach/thanh-toan')
@section('og_title', 'Chính sách thanh toán - Sàn Tím Vi En')
@section('og_description', 'Website hiện hỗ trợ thanh toán VietQR và COD tại bước đặt hàng.')

@push('styles')
    @vite(['public/css/views/static_pages.css'])
@endpush

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
<section class="content-hero">
    <div class="container">
        <nav class="content-breadcrumb" aria-label="breadcrumb"><a href="{{ route('home') }}">Trang chủ</a> / Chính sách thanh toán</nav>
        <span class="content-eyebrow">Thanh toán</span>
        <h1 class="page-title">Chính sách thanh toán</h1>
        <p class="content-intro">Thông tin về các phương thức thanh toán đang có tại bước đặt hàng trên website.</p>
    </div>
</section>
<div class="container content-page">
    <div class="row justify-content-center"><article class="col-lg-9 content-shell">
        <p class="lead">Tại bước thanh toán, website hiện cho phép khách hàng chọn một trong hai phương thức dưới đây.</p>
        <h2>Chuyển khoản VietQR</h2>
        <p>Khách hàng chọn VietQR tại trang thanh toán và thực hiện chuyển khoản bằng ứng dụng ngân hàng theo thông tin hiển thị trong luồng đặt hàng.</p>
        <h2>Thanh toán khi nhận hàng (COD)</h2>
        <p>Khách hàng chọn COD tại trang thanh toán và thanh toán khi nhận hàng theo đơn đã đặt.</p>
        <h2>Kiểm tra đơn hàng</h2>
        <p>Sau khi đặt hàng, khách hàng đăng nhập có thể truy cập danh sách đơn mua để xem trạng thái đơn và trạng thái thanh toán đang được ghi nhận trên website.</p>
        <div class="content-actions">
            <a class="btn-primary-content" href="{{ route('support.purchase-guide') }}">Hướng dẫn mua hàng</a>
            <a class="btn-outline-content" href="{{ route('products.index') }}">Chọn sản phẩm</a>
        </div>
    </article></div>
</div>
@endsection
