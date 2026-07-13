@extends('layouts.app')

@section('title', '5 cách phối áo thun đơn giản hằng ngày - Sàn Tím Vi En')
@section('meta_description', 'Gợi ý phối áo thun với quần jeans, quần suông, chân váy và lớp khoác nhẹ để mặc đẹp mỗi ngày.')
@section('canonical', 'https://santimvien.vn/blog/cach-phoi-ao-thun-don-gian-hang-ngay')
@section('og_type', 'article')
@section('og_title', '5 cách phối áo thun đơn giản hằng ngày')
@section('og_description', 'Các công thức phối áo thun dễ áp dụng cho sinh hoạt hằng ngày.')

@php
    $canonical = 'https://santimvien.vn/blog/cach-phoi-ao-thun-don-gian-hang-ngay';
    $articleSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'BlogPosting',
        'headline' => '5 cách phối áo thun đơn giản hằng ngày',
        'datePublished' => '2026-05-24',
        'dateModified' => '2026-05-24',
        'author' => ['@type' => 'Organization', 'name' => 'Sàn Tím Vi En'],
        'publisher' => ['@type' => 'Organization', 'name' => 'Sàn Tím Vi En'],
        'mainEntityOfPage' => $canonical,
        'description' => 'Gợi ý phối áo thun dễ áp dụng trong sinh hoạt hằng ngày.',
    ];
@endphp

@push('styles')
    @vite(['public/css/views/static_pages.css'])
@endpush

@section('content')
<script type="application/ld+json">{!! json_encode($articleSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
<article class="container article-page article-body">
    <nav class="content-breadcrumb" aria-label="breadcrumb"><a href="{{ route('home') }}">Trang chủ</a> / <a href="{{ route('pages.blog') }}">Blog</a> / Phối áo thun</nav>
    <header class="article-header">
        <p class="blog-meta">Phối đồ • Xuất bản: 24 tháng 5, 2026</p>
        <h1 class="page-title text-start">5 cách phối áo thun đơn giản hằng ngày</h1>
        <p class="lead">Một chiếc áo thun dễ trở thành nền tảng cho nhiều bộ trang phục khi màu sắc và phom dáng được cân bằng.</p>
    </header>
    <h2>Áo thun và quần jeans</h2>
    <p>Công thức quen thuộc phù hợp cho ngày thường. Áo trơn phối jeans xanh hoặc đen tạo tổng thể gọn gàng và dễ thêm giày thể thao.</p>
    <h2>Áo thun và quần suông</h2>
    <p>Quần suông giúp bộ đồ thanh lịch hơn. Bạn có thể sơ vin một phần để tạo tỉ lệ cân đối mà vẫn thoải mái.</p>
    <h2>Áo thun và chân váy</h2>
    <p>Kết hợp áo thun màu cơ bản với chân váy tạo cảm giác nhẹ nhàng; phụ kiện nhỏ là đủ để hoàn thiện trang phục.</p>
    <h2>Áo thun cùng lớp khoác mỏng</h2>
    <p>Sơ mi khoác ngoài hoặc cardigan mỏng hữu ích khi di chuyển giữa không gian nóng và có điều hòa.</p>
    <h2>Chọn màu theo tủ đồ sẵn có</h2>
    <p>Nếu muốn áo mặc được nhiều lần, hãy bắt đầu từ màu dễ kết hợp với quần và giày bạn đang sở hữu.</p>
    <div class="content-actions">
        <a class="btn-primary-content" href="{{ route('products.index') }}">Xem sản phẩm</a>
        <a class="btn-outline-content" href="{{ route('guides.size') }}">Tham khảo size</a>
    </div>
</article>
@endsection
