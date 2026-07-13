@extends('layouts.app')

@section('title', 'Điều khoản sử dụng - Sàn Tím Vi En')
@section('meta_description', 'Điều khoản sử dụng website Sàn Tím Vi En đang chờ xác nhận trước khi công bố chính thức.')
@section('robots', 'noindex, follow')
@section('canonical', 'https://santimvien.vn/chinh-sach/dieu-khoan')

@push('styles')
    @vite(['public/css/views/static_pages.css'])
@endpush

@section('content')
<section class="content-hero">
    <div class="container">
        <nav class="content-breadcrumb" aria-label="breadcrumb"><a href="{{ route('home') }}">Trang chủ</a> / Điều khoản sử dụng</nav>
        <span class="content-eyebrow">Chính sách</span>
        <h1 class="page-title">Điều khoản sử dụng</h1>
        <p class="content-intro">Điều khoản giao dịch chính thức sẽ được đăng tải khi nội dung đã hoàn tất xác nhận.</p>
    </div>
</section>
<div class="container content-page">
    <div class="row justify-content-center"><article class="col-lg-9 content-shell">
        <div class="notice-card">Trang này chưa được lập chỉ mục vì điều khoản giao dịch, quyền và nghĩa vụ của các bên cần được chủ shop xác nhận trước khi áp dụng.</div>
        <p>Khách hàng có thể xem sản phẩm và sử dụng các luồng mua sắm hiện có trên website. Điều khoản chính thức sẽ được đăng tải tại đây sau khi hoàn tất xác nhận.</p>
    </article></div>
</div>
@endsection
