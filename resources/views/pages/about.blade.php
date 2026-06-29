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
<section class="about-hero" style="background: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)), url('https://res.cloudinary.com/dqfqgzrgx/image/upload/v1782184439/santimvien/assets/ykyqkp8drljs2k8nurqm.jpg') center/cover no-repeat;">
    <div class="container">
        <nav class="content-breadcrumb" aria-label="breadcrumb"><a href="{{ route('home') }}">Trang chủ</a> / Về chúng tôi</nav>
        <span class="content-eyebrow">Sàn Tím Vi En</span>
        <h1 class="page-title">Giới thiệu Sàn Tím Vi En</h1>
        <p class="content-intro">Nơi bạn khám phá sản phẩm thời trang, tham khảo cách phối đồ và chọn size thuận tiện trước khi đặt mua.</p>
    </div>
</section>

<div class="container about-content pb-5">
    <div class="row align-items-center g-5">
        <div class="col-lg-7">
            <h2 class="section-title">Mua sắm rõ ràng, chọn lựa dễ hơn</h2>
            <p class="about-text">Sàn Tím Vi En xây dựng trải nghiệm mua sắm trực tuyến gọn gàng: xem danh mục, đọc thông tin sản phẩm, chọn kích cỡ và hoàn tất đơn hàng trên website.</p>
            <p class="about-text mt-4">Công cụ gợi ý size sử dụng chiều cao và cân nặng do bạn cung cấp để đưa ra lựa chọn tham khảo. Trước khi mua, bạn nên kết hợp gợi ý này với mô tả sản phẩm và kiểu mặc mong muốn.</p>
        </div>
        <div class="col-lg-5">
            <div class="vision-card">
                <span class="content-eyebrow">Khám phá</span>
                <h2 class="h4 fw-bold mb-3">Bắt đầu với lựa chọn phù hợp</h2>
                <p class="text-muted">Xem sản phẩm mới nhất trên website hoặc đọc hướng dẫn chọn size trước khi đặt hàng.</p>
                <div class="content-actions">
                    <a class="btn-primary-content" href="{{ route('products.index') }}">Xem sản phẩm</a>
                    <a class="btn-outline-content" href="{{ route('guides.size') }}">Chọn size</a>
                </div>
            </div>
        </div>
    </div>
</div>

<section class="stat-container">
    <div class="container">
        <div class="feature-grid">
            <div class="feature-card"><h3>Khám phá sản phẩm</h3><p>Duyệt danh mục và xem chi tiết từng thiết kế trước khi lựa chọn.</p></div>
            <div class="feature-card"><h3>Tham khảo size</h3><p>Đọc bảng hướng dẫn và nhận gợi ý size khi đã cập nhật thông tin phù hợp.</p></div>
            <div class="feature-card"><h3>Quản lý đơn mua</h3><p>Khách hàng đăng nhập có thể xem lại đơn hàng trong khu vực tài khoản.</p></div>
        </div>
    </div>
</section>
@endsection
