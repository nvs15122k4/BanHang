@extends('layouts.app')

@section('title', 'Liên hệ Sàn Tím Vi En')
@section('meta_description', 'Trang liên hệ Sàn Tím Vi En đang chờ xác nhận kênh hỗ trợ chính thức trước khi công bố cho khách hàng.')
@section('robots', 'noindex, follow')
@section('canonical', 'https://santimvien.vn/contact')
@section('og_title', 'Liên hệ Sàn Tím Vi En')
@section('og_description', 'Thông tin liên hệ chính thức đang được xác nhận trước khi công bố.')

@push('styles')
    @vite(['public/css/views/static_pages.css'])
@endpush

@section('content')
<section class="contact-hero">
    <div class="container">
        <nav class="content-breadcrumb" aria-label="breadcrumb"><a href="{{ route('home') }}">Trang chủ</a> / Liên hệ</nav>
        <span class="content-eyebrow">Hỗ trợ khách hàng</span>
        <h1 class="page-title">Liên hệ</h1>
        <p class="content-intro">Kênh liên hệ chính thức đang được hoàn thiện để thông tin gửi đến bạn luôn chính xác và nhất quán.</p>
    </div>
</section>

<div class="container contact-wrapper py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="contact-form-panel">
                <span class="content-eyebrow">Thông báo</span>
                <h2 class="form-title">Thông tin hỗ trợ đang được cập nhật</h2>
                <p>Website chưa công bố email, hotline, địa chỉ hoặc thời gian làm việc vì những thông tin này cần được xác nhận chính thức trước khi sử dụng.</p>
                <p class="mb-0">Trong thời gian này, bạn có thể xem hướng dẫn mua hàng, câu hỏi thường gặp và đăng nhập để theo dõi đơn mua đã tạo trên website.</p>
                <div class="content-actions">
                    <a class="btn-primary-content" href="{{ route('support.purchase-guide') }}">Hướng dẫn mua hàng</a>
                    <a class="btn-outline-content" href="{{ route('support.faq') }}">Câu hỏi thường gặp</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
