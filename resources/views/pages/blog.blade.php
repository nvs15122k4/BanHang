@extends('layouts.app')

@section('title', 'Blog thời trang và hướng dẫn chọn size - Sàn Tím Vi En')
@section('meta_description', 'Đọc hướng dẫn chọn size, phối áo thun và bảo quản trang phục để mua sắm thời trang trực tuyến tự tin hơn.')
@section('canonical', 'https://santimvien.vn/blog')
@section('og_title', 'Blog thời trang và hướng dẫn chọn size - Sàn Tím Vi En')
@section('og_description', 'Nội dung hữu ích về chọn size, phối đồ và chăm sóc trang phục.')

@php
    $posts = [
        [
            'slug' => 'cach-chon-size-quan-ao-khi-mua-online',
            'title' => 'Cách chọn size quần áo khi mua online',
            'topic' => 'Hướng dẫn chọn size',
            'date' => '24 tháng 5, 2026',
            'excerpt' => 'Các bước đo, đối chiếu size và sử dụng gợi ý chiều cao, cân nặng khi chọn sản phẩm trực tuyến.',
            'image' => 'https://res.cloudinary.com/dxvml3sji/image/upload/q_auto/f_auto/v1778634215/eecny8uiiwp9goo7zngu.jpg',
        ],
        [
            'slug' => 'cach-phoi-ao-thun-don-gian-hang-ngay',
            'title' => '5 cách phối áo thun đơn giản hằng ngày',
            'topic' => 'Phối đồ',
            'date' => '24 tháng 5, 2026',
            'excerpt' => 'Những công thức mặc dễ áp dụng với quần jeans, quần suông, lớp khoác nhẹ và phụ kiện vừa đủ.',
            'image' => 'https://res.cloudinary.com/dxvml3sji/image/upload/q_auto/f_auto/v1778634688/zhwiyovkz6zuhsn4nvnq.jpg',
        ],
        [
            'slug' => 'cach-bao-quan-trang-phuc-ben-mau',
            'title' => 'Cách bảo quản trang phục bền màu',
            'topic' => 'Chăm sóc trang phục',
            'date' => '24 tháng 5, 2026',
            'excerpt' => 'Tách màu, đọc nhãn chăm sóc và phơi đúng cách để trang phục giữ phom và màu lâu hơn.',
            'image' => 'https://res.cloudinary.com/dxvml3sji/image/upload/q_auto/f_auto/v1778634687/zgwqjwlgl7rdxr1vfxzv.jpg',
        ],
    ];
@endphp

@push('styles')
    @vite(['public/css/views/static_pages.css'])
@endpush

@section('content')
<div class="blog-header">
    <div class="container">
        <nav class="mb-4" aria-label="breadcrumb"><a href="{{ route('home') }}">Trang chủ</a> / Blog</nav>
        <h1 class="page-title">Góc thời trang</h1>
        <p class="text-muted">Hướng dẫn thực tế để chọn, phối và chăm sóc trang phục.</p>
    </div>
</div>

<div class="container pb-5">
    <div class="row g-4">
        @foreach($posts as $post)
        <article class="col-lg-4 col-md-6">
            <div class="blog-card h-100">
                <div class="blog-img-wrap">
                    <img src="{{ $post['image'] }}" alt="{{ $post['title'] }}" loading="lazy">
                </div>
                <span class="blog-meta">{{ $post['topic'] }} • {{ $post['date'] }}</span>
                <h2 class="blog-title"><a href="{{ route('blog.show', ['slug' => $post['slug']]) }}">{{ $post['title'] }}</a></h2>
                <p class="blog-excerpt">{{ $post['excerpt'] }}</p>
                <a href="{{ route('blog.show', ['slug' => $post['slug']]) }}" class="btn-more">Đọc bài viết</a>
            </div>
        </article>
        @endforeach
    </div>
</div>
@endsection
