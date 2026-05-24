@extends('layouts.app')

@section('title', 'Cách chọn size quần áo khi mua online - Sàn Tím Vi En')
@section('meta_description', 'Hướng dẫn đo cơ thể, đối chiếu size và dùng gợi ý size để giảm rủi ro chọn nhầm kích cỡ khi mua quần áo online.')
@section('canonical', 'https://santimvien.vn/blog/cach-chon-size-quan-ao-khi-mua-online')
@section('og_type', 'article')
@section('og_title', 'Cách chọn size quần áo khi mua online')
@section('og_description', 'Các bước đơn giản giúp bạn chọn kích cỡ phù hợp hơn khi mua trang phục trực tuyến.')

@php
    $canonical = 'https://santimvien.vn/blog/cach-chon-size-quan-ao-khi-mua-online';
    $articleSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'BlogPosting',
        'headline' => 'Cách chọn size quần áo khi mua online',
        'datePublished' => '2026-05-24',
        'dateModified' => '2026-05-24',
        'author' => ['@type' => 'Organization', 'name' => 'Sàn Tím Vi En'],
        'publisher' => ['@type' => 'Organization', 'name' => 'Sàn Tím Vi En'],
        'mainEntityOfPage' => $canonical,
        'description' => 'Hướng dẫn đo cơ thể, đối chiếu size và dùng gợi ý size khi mua quần áo online.',
    ];
    $breadcrumbSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => [
            ['@type' => 'ListItem', 'position' => 1, 'name' => 'Trang chủ', 'item' => 'https://santimvien.vn/'],
            ['@type' => 'ListItem', 'position' => 2, 'name' => 'Blog', 'item' => 'https://santimvien.vn/blog'],
            ['@type' => 'ListItem', 'position' => 3, 'name' => 'Cách chọn size quần áo khi mua online', 'item' => $canonical],
        ],
    ];
@endphp

@push('styles')
    @vite(['public/css/views/static_pages.css'])
@endpush

@section('content')
<script type="application/ld+json">{!! json_encode($articleSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
<script type="application/ld+json">{!! json_encode($breadcrumbSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
<article class="container py-5 max-w-900-px-custom">
    <nav class="mb-4" aria-label="breadcrumb"><a href="{{ route('home') }}">Trang chủ</a> / <a href="{{ route('pages.blog') }}">Blog</a> / Chọn size khi mua online</nav>
    <header class="mb-5">
        <p class="blog-meta">Hướng dẫn chọn size • Xuất bản: 24 tháng 5, 2026</p>
        <h1 class="page-title text-start">Cách chọn size quần áo khi mua online</h1>
        <p class="lead">Chọn size tốt hơn bắt đầu từ số đo đúng và việc hiểu kiểu mặc bạn muốn: vừa vặn, thoải mái hay rộng hơn.</p>
    </header>
    <h2>1. Chuẩn bị số đo cơ bản</h2>
    <p>Đo chiều cao và cân nặng ở trạng thái tự nhiên. Khi sản phẩm cần quan tâm đến vòng ngực, eo hoặc hông, hãy đo bằng thước dây và giữ thước vừa sát cơ thể, không siết chặt.</p>
    <h2>2. Đối chiếu với hướng dẫn size</h2>
    <p>Website có gợi ý size dựa trên chiều cao, cân nặng và BMI. Đây là gợi ý tham khảo để rút ngắn bước lựa chọn, không thay thế cảm nhận về phom dáng hay thông tin cụ thể của từng sản phẩm.</p>
    <h2>3. Cân nhắc form mặc</h2>
    <p>Nếu muốn mặc thoải mái hoặc kết hợp nhiều lớp, bạn có thể cân nhắc size rộng hơn. Nếu đứng giữa hai lựa chọn, hãy ưu tiên kiểu mặc mong muốn và đọc mô tả sản phẩm trước khi đặt hàng.</p>
    <h2>Đi tiếp từ đây</h2>
    <p><a href="{{ route('guides.size') }}">Xem bảng hướng dẫn chọn size của website</a> hoặc <a href="{{ route('products.index') }}">khám phá sản phẩm đang có</a>.</p>
</article>
@endsection
