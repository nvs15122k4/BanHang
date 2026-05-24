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
        <nav class="mb-4" aria-label="breadcrumb"><a href="{{ route('home') }}">Trang chủ</a> / Liên hệ</nav>
        <h1 class="page-title">Liên hệ</h1>
        <p class="opacity-70-custom">Thông tin liên hệ chính thức đang được xác nhận trước khi công bố.</p>
    </div>
</section>

<div class="container contact-wrapper py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="contact-form-panel">
                <h2 class="form-title">Kênh hỗ trợ chưa được công bố</h2>
                <p>Website chưa công bố email, hotline, địa chỉ hoặc giờ hỗ trợ vì các thông tin này cần được chủ shop xác nhận chính xác.</p>
                <p class="mb-4">Khách hàng đã đăng nhập vẫn có thể theo dõi trạng thái đơn trong khu vực đơn mua. Các chính sách giao hàng và đổi trả sẽ được cập nhật khi điều kiện vận hành được xác nhận.</p>
                <div class="d-flex flex-wrap gap-2">
                    <a class="btn btn-dark" href="{{ route('products.index') }}">Xem sản phẩm</a>
                    <a class="btn btn-outline-dark" href="{{ route('support.faq') }}">Câu hỏi thường gặp</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
