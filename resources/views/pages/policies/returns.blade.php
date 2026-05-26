@extends('layouts.app')

@section('title', 'Thông tin đổi trả - Sàn Tím Vi En')
@section('meta_description', 'Điều kiện đổi trả và hoàn tiền Sàn Tím Vi En đang chờ xác nhận trước khi công bố.')
@section('robots', 'noindex, follow')
@section('canonical', 'https://santimvien.vn/chinh-sach/doi-tra')

@push('styles')
    @vite(['public/css/views/static_pages.css'])
@endpush

@section('content')
<section class="content-hero">
    <div class="container">
        <nav class="content-breadcrumb" aria-label="breadcrumb"><a href="{{ route('home') }}">Trang chủ</a> / Thông tin đổi trả</nav>
        <span class="content-eyebrow">Chính sách</span>
        <h1 class="page-title">Thông tin đổi trả</h1>
        <p class="content-intro">Điều kiện và quy trình đổi trả sẽ được cập nhật rõ ràng sau khi được xác nhận chính thức.</p>
    </div>
</section>
<div class="container content-page">
    <div class="row justify-content-center"><article class="col-lg-9 content-shell">
        <div class="notice-card">Trang này chưa được lập chỉ mục vì thời hạn đổi trả, điều kiện sản phẩm và phương thức hoàn tiền chưa được chủ shop xác nhận.</div>
        <p>Website sẽ công bố quy trình đổi trả tại URL này sau khi chính sách vận hành được chốt. Không có cam kết thời hạn đổi trả nào được áp dụng từ nội dung trang hiện tại.</p>
        <div class="content-actions">
            <a class="btn-primary-content" href="{{ route('guides.size') }}">Hướng dẫn chọn size</a>
            <a class="btn-outline-content" href="{{ route('support.faq') }}">Câu hỏi thường gặp</a>
        </div>
    </article></div>
</div>
@endsection
