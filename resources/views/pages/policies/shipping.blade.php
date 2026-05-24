@extends('layouts.app')

@section('title', 'Thông tin giao hàng - Sàn Tím Vi En')
@section('meta_description', 'Thông tin giao hàng Sàn Tím Vi En đang chờ xác nhận phạm vi, thời gian và phí vận chuyển trước khi công bố.')
@section('robots', 'noindex, follow')
@section('canonical', 'https://santimvien.vn/chinh-sach/giao-hang')

@section('content')
<div class="container py-5">
    <nav class="mb-4" aria-label="breadcrumb"><a href="{{ route('home') }}">Trang chủ</a> / Thông tin giao hàng</nav>
    <div class="row justify-content-center"><article class="col-lg-9">
        <h1 class="page-title text-start">Thông tin giao hàng</h1>
        <div class="alert alert-info">Trang này chưa được lập chỉ mục vì phạm vi giao hàng, thời gian xử lý, thời gian vận chuyển và cách tính phí cần được chủ shop xác nhận trước khi công bố.</div>
        <p>Trong luồng checkout hiện tại, hệ thống hiển thị phí vận chuyển là miễn phí. Thông tin này chỉ nên được coi là chính sách chính thức sau khi được xác nhận về mặt vận hành.</p>
        <p><a href="{{ route('support.purchase-guide') }}">Xem hướng dẫn mua hàng</a> hoặc <a href="{{ route('pages.contact') }}">xem trạng thái thông tin liên hệ</a>.</p>
    </article></div>
</div>
@endsection
