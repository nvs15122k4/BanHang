@extends('layouts.app')

@section('title', 'Chính sách bảo mật - Sàn Tím Vi En')
@section('meta_description', 'Chính sách bảo mật Sàn Tím Vi En đang được hoàn thiện sau khi xác nhận phạm vi xử lý dữ liệu khách hàng.')
@section('robots', 'noindex, follow')
@section('canonical', 'https://santimvien.vn/chinh-sach/bao-mat')

@push('styles')
    @vite(['public/css/views/static_pages.css'])
@endpush

@section('content')
<section class="content-hero">
    <div class="container">
        <nav class="content-breadcrumb" aria-label="breadcrumb"><a href="{{ route('home') }}">Trang chủ</a> / Chính sách bảo mật</nav>
        <span class="content-eyebrow">Chính sách</span>
        <h1 class="page-title">Chính sách bảo mật</h1>
        <p class="content-intro">Nội dung bảo mật sẽ được công bố sau khi phạm vi xử lý dữ liệu được xác nhận đầy đủ.</p>
    </div>
</section>
<div class="container content-page">
    <div class="row justify-content-center"><article class="col-lg-9 content-shell">
        <div class="notice-card">Trang này chưa được lập chỉ mục vì chính sách thu thập, sử dụng, lưu trữ và yêu cầu cập nhật hoặc xóa dữ liệu cần được xác nhận trước khi công bố.</div>
        <p>Website có các luồng tài khoản, địa chỉ giao hàng và đặt hàng. Nội dung chính sách chi tiết sẽ được bổ sung sau khi phạm vi xử lý dữ liệu và kênh liên hệ chính thức được chốt.</p>
    </article></div>
</div>
@endsection
