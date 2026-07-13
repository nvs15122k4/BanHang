@extends('layouts.app')

@section('title', 'Câu hỏi thường gặp - Sàn Tím Vi En')
@section('meta_description', 'Giải đáp về chọn size, đặt hàng, thanh toán và theo dõi đơn mua trên website Sàn Tím Vi En.')
@section('canonical', 'https://santimvien.vn/ho-tro/cau-hoi-thuong-gap')
@section('og_title', 'Câu hỏi thường gặp - Sàn Tím Vi En')
@section('og_description', 'Thông tin hỗ trợ cho các thao tác đang có trên website.')

@push('styles')
    @vite(['public/css/views/static_pages.css'])
@endpush

@section('content')
<section class="content-hero">
    <div class="container">
        <nav class="content-breadcrumb" aria-label="breadcrumb"><a href="{{ route('home') }}">Trang chủ</a> / Hỗ trợ / Câu hỏi thường gặp</nav>
        <span class="content-eyebrow">Trung tâm hỗ trợ</span>
        <h1 class="page-title">Câu hỏi thường gặp</h1>
        <p class="content-intro">Những thông tin cần thiết khi chọn size, đặt hàng, thanh toán và xem lại đơn mua.</p>
    </div>
</section>
<div class="container content-page">
    <div class="faq-grid">
        <article class="faq-card">
            <h2>Làm sao chọn đúng size?</h2>
            <p>Xem <a href="{{ route('guides.size') }}">hướng dẫn chọn size</a>. Khi đăng nhập và cập nhật chiều cao, cân nặng, bạn có thể nhận gợi ý size trên trang sản phẩm.</p>
        </article>
        <article class="faq-card">
            <h2>Website hỗ trợ thanh toán nào?</h2>
            <p>Tại bước thanh toán, website hiện hỗ trợ VietQR và thanh toán khi nhận hàng (COD). Xem <a href="{{ route('policies.payment') }}">chính sách thanh toán</a>.</p>
        </article>
        <article class="faq-card">
            <h2>Làm sao theo dõi đơn mua?</h2>
            <p>Khách hàng đã đăng nhập có thể mở mục đơn mua trong tài khoản để xem trạng thái đơn đã đặt.</p>
        </article>
        <article class="faq-card">
            <h2>Thông tin giao hàng và đổi trả ở đâu?</h2>
            <p>Thời gian giao hàng và điều kiện đổi trả đang chờ xác nhận vận hành chính thức; thông tin sẽ được cập nhật tại trang chính sách tương ứng.</p>
        </article>
    </div>
</div>
@endsection
