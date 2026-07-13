@extends('layouts.app')

@section('title', 'Hướng dẫn chọn size quần áo - Sàn Tím Vi En')
@section('meta_description', 'Tham khảo cách đo chiều cao, cân nặng và bảng size XS đến XXL đang dùng cho tính năng gợi ý size tại Sàn Tím Vi En.')
@section('canonical', 'https://santimvien.vn/huong-dan/chon-size')
@section('og_title', 'Hướng dẫn chọn size quần áo')
@section('og_description', 'Cách đo và bảng size tham khảo đồng nhất với công cụ gợi ý size trên website.')

@php
    $breadcrumbSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => [
            ['@type' => 'ListItem', 'position' => 1, 'name' => 'Trang chủ', 'item' => 'https://santimvien.vn/'],
            ['@type' => 'ListItem', 'position' => 2, 'name' => 'Hướng dẫn chọn size', 'item' => 'https://santimvien.vn/huong-dan/chon-size'],
        ],
    ];
@endphp

@push('styles')
    @vite(['public/css/views/static_pages.css'])
@endpush

@section('content')
<script type="application/ld+json">{!! json_encode($breadcrumbSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
<section class="content-hero">
    <div class="container">
        <nav class="content-breadcrumb" aria-label="breadcrumb"><a href="{{ route('home') }}">Trang chủ</a> / Hướng dẫn chọn size</nav>
        <span class="content-eyebrow">Hướng dẫn mua sắm</span>
        <h1 class="page-title">Cách chọn size quần áo phù hợp</h1>
        <p class="content-intro">Đo đúng thông tin cơ bản và đối chiếu bảng size để chọn lựa thuận tiện hơn trước khi đặt hàng.</p>
    </div>
</section>
<div class="container content-page">
    <div class="row justify-content-center">
        <article class="col-lg-10 content-shell">
            <p class="lead mb-0">Bảng dưới đây phản ánh đúng quy tắc gợi ý size đang áp dụng trên website dựa trên chiều cao, cân nặng và BMI.</p>

            <h2>Cách chuẩn bị thông tin</h2>
            <ol class="mb-4">
                <li class="mb-2">Đo chiều cao theo centimet và cân nặng theo kilogram.</li>
                <li class="mb-2">Khi đăng nhập, cập nhật chiều cao và cân nặng trong hồ sơ để nhận gợi ý trên trang sản phẩm.</li>
                <li class="mb-2">Đối chiếu gợi ý với phom dáng bạn mong muốn trước khi chọn size.</li>
            </ol>

            <h2>Bảng size tham khảo đang dùng</h2>
            <div class="table-responsive">
                <table class="table size-table align-middle mb-0">
                    <thead class="table-light">
                        <tr><th>Size</th><th>Chiều cao (cm)</th><th>Cân nặng (kg)</th><th>BMI tham khảo</th></tr>
                    </thead>
                    <tbody>
                        <tr><th>XS</th><td>đến 155</td><td>đến 45</td><td>đến 18.5</td></tr>
                        <tr><th>S</th><td>155 - 165</td><td>45 - 55</td><td>18.5 - 21</td></tr>
                        <tr><th>M</th><td>165 - 175</td><td>55 - 68</td><td>21 - 24</td></tr>
                        <tr><th>L</th><td>175 - 185</td><td>68 - 82</td><td>24 - 27</td></tr>
                        <tr><th>XL</th><td>185 - 195</td><td>82 - 100</td><td>27 - 30</td></tr>
                        <tr><th>XXL</th><td>195 - 300</td><td>từ 100</td><td>từ 30</td></tr>
                    </tbody>
                </table>
            </div>
            <div class="notice-card">Ở ranh giới giữa hai khoảng, công cụ có thể so sánh nhiều tiêu chí và hiển thị các size gần phù hợp nhất. Bảng size mang tính tham khảo vì chất liệu và phom sản phẩm có thể ảnh hưởng cảm giác mặc.</div>

            <h2>Nếu bạn nằm giữa hai size</h2>
            <p>Chọn size nhỏ hơn khi muốn mặc gọn và sản phẩm có phom rộng; chọn size lớn hơn khi ưu tiên thoải mái hoặc muốn mặc thêm lớp bên trong.</p>
            <div class="content-actions">
                <a class="btn-primary-content" href="{{ route('products.index') }}">Xem sản phẩm</a>
                <a class="btn-outline-content" href="{{ route('blog.show', ['slug' => 'cach-chon-size-quan-ao-khi-mua-online']) }}">Đọc bài chọn size</a>
            </div>
        </article>
    </div>
</div>
@endsection
