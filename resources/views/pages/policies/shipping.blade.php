@extends('layouts.app')

@section('title', 'Thông tin giao hàng - Sàn Tím Vi En')
@section('meta_description', 'Thông tin giao hàng Sàn Tím Vi En đang chờ xác nhận phạm vi, thời gian và phí vận chuyển trước khi công bố.')
@section('robots', 'noindex, follow')
@section('canonical', 'https://santimvien.vn/chinh-sach/giao-hang')

@push('styles')
    @vite(['public/css/views/static_pages.css'])
@endpush

@section('content')
<section class="content-hero">
    <div class="container">
        <nav class="content-breadcrumb" aria-label="breadcrumb"><a href="{{ route('home') }}">Trang chủ</a> / Thông tin giao hàng</nav>
        <span class="content-eyebrow">Chính sách</span>
        <h1 class="page-title">Thông tin giao hàng</h1>
        <p class="content-intro">Nội dung vận chuyển sẽ được công bố khi phạm vi, thời gian và chi phí được xác nhận đầy đủ.</p>
    </div>
</section>
<div class="container content-page">
    <div class="row justify-content-center"><article class="col-lg-9 content-shell">
        <div class="notice-card">Trang này chưa được lập chỉ mục vì phạm vi giao hàng, thời gian xử lý, thời gian vận chuyển và cách tính phí cần được chủ shop xác nhận trước khi công bố.</div>
        <p>Trong luồng checkout hiện tại, hệ thống hiển thị phí vận chuyển là miễn phí. Thông tin này chỉ được xem là chính sách chính thức sau khi có xác nhận vận hành.</p>
        <div class="content-actions">
            <a class="btn-primary-content" href="{{ route('support.purchase-guide') }}">Hướng dẫn mua hàng</a>
            <a class="btn-outline-content" href="{{ route('pages.contact') }}">Thông tin liên hệ</a>
        </div>
    </article></div>
</div>
@endsection
