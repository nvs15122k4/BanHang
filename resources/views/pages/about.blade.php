@extends('layouts.app')

@section('title', 'Giới thiệu Sàn Tím Vi En - Mua sắm thời trang trực tuyến')
@section('meta_description', 'Tìm hiểu Sàn Tím Vi En, website mua sắm thời trang với danh mục sản phẩm, hướng dẫn chọn size và công cụ theo dõi đơn hàng.')
@section('canonical', 'https://santimvien.vn/about')
@section('og_title', 'Giới thiệu Sàn Tím Vi En')
@section('og_description', 'Website mua sắm thời trang với danh mục sản phẩm, hướng dẫn chọn size và công cụ theo dõi đơn hàng.')

@php
    $breadcrumbSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => [
            ['@type' => 'ListItem', 'position' => 1, 'name' => 'Trang chủ', 'item' => 'https://santimvien.vn/'],
            ['@type' => 'ListItem', 'position' => 2, 'name' => 'Giới thiệu', 'item' => 'https://santimvien.vn/about'],
        ],
    ];
@endphp

@push('styles')
    @vite(['public/css/views/static_pages.css'])
@endpush

@section('content')
<script type="application/ld+json">{!! json_encode($breadcrumbSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
<section class="about-hero">
    <div class="container">
        <nav class="mb-4" aria-label="breadcrumb"><a href="{{ route('home') }}">Trang chủ</a> / Giới thiệu</nav>
        <h1 class="page-title">Giới thiệu Sàn Tím Vi En</h1>
        <p class="text-muted mx-auto-custom max-w-600-px-custom">Không gian mua sắm thời trang trực tuyến giúp bạn khám phá sản phẩm và chọn trang phục phù hợp hơn.</p>
    </div>
</section>

<div class="container about-content pb-5">
    <div class="row align-items-center g-5">
        <div class="col-lg-7">
            <h2 class="section-title">Chúng tôi phục vụ điều gì?</h2>
            <p class="about-text">Sàn Tím Vi En trưng bày các sản phẩm thời trang trên website để khách hàng xem thông tin, lựa chọn kích cỡ và đặt hàng trực tuyến.</p>
            <p class="about-text mt-4">Website hiện có tính năng gợi ý size dựa trên chiều cao và cân nặng do người dùng cung cấp. Gợi ý là thông tin tham khảo; bạn vẫn nên xem mô tả sản phẩm và chọn size phù hợp với cách mặc mong muốn.</p>
        </div>
        <div class="col-lg-5">
            <div class="vision-card">
                <h2 class="h4 fw-bold mb-3">Bắt đầu mua sắm</h2>
                <p class="text-muted">Khám phá sản phẩm hoặc xem hướng dẫn chọn size trước khi đặt hàng.</p>
                <div class="d-flex flex-wrap gap-2">
                    <a class="btn btn-dark" href="{{ route('products.index') }}">Xem sản phẩm</a>
                    <a class="btn btn-outline-dark" href="{{ route('guides.size') }}">Hướng dẫn chọn size</a>
                </div>
            </div>
        </div>
    </div>
</div>

<section class="stat-container">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-4"><div class="vision-card h-100"><h2 class="h5 fw-bold">Chọn sản phẩm</h2><p class="mb-0">Duyệt danh mục và chi tiết từng sản phẩm trước khi mua.</p></div></div>
            <div class="col-md-4"><div class="vision-card h-100"><h2 class="h5 fw-bold">Chọn size</h2><p class="mb-0">Tham khảo hướng dẫn và tính năng gợi ý size của website.</p></div></div>
            <div class="col-md-4"><div class="vision-card h-100"><h2 class="h5 fw-bold">Theo dõi đơn</h2><p class="mb-0">Khách hàng đăng nhập có thể xem đơn mua trong tài khoản.</p></div></div>
        </div>
    </div>
</section>
@endsection
