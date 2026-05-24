@extends('layouts.app')

@section('title', 'Câu hỏi thường gặp - Sàn Tím Vi En')
@section('meta_description', 'Giải đáp về chọn size, đặt hàng, thanh toán và theo dõi đơn mua trên website Sàn Tím Vi En.')
@section('canonical', 'https://santimvien.vn/ho-tro/cau-hoi-thuong-gap')
@section('og_title', 'Câu hỏi thường gặp - Sàn Tím Vi En')
@section('og_description', 'Thông tin hỗ trợ cho các thao tác đang có trên website.')

@section('content')
<div class="container py-5">
    <nav class="mb-4" aria-label="breadcrumb"><a href="{{ route('home') }}">Trang chủ</a> / Câu hỏi thường gặp</nav>
    <div class="row justify-content-center"><article class="col-lg-9">
        <h1 class="page-title text-start">Câu hỏi thường gặp</h1>
        <h2>Làm sao chọn đúng size?</h2>
        <p>Xem <a href="{{ route('guides.size') }}">hướng dẫn chọn size</a>. Khi đăng nhập và cập nhật chiều cao, cân nặng, bạn có thể nhận gợi ý size trên trang sản phẩm.</p>
        <h2>Website hỗ trợ thanh toán nào?</h2>
        <p>Tại bước thanh toán, website hiện hỗ trợ VietQR và thanh toán khi nhận hàng (COD). Xem thêm tại <a href="{{ route('policies.payment') }}">chính sách thanh toán</a>.</p>
        <h2>Làm sao theo dõi đơn?</h2>
        <p>Khách hàng đã đăng nhập có thể mở mục đơn mua trong tài khoản để xem trạng thái đơn đã đặt.</p>
        <h2>Bao lâu nhận được hàng và điều kiện đổi trả là gì?</h2>
        <p>Thời gian giao hàng và điều kiện đổi trả chưa được công bố do đang chờ chủ shop xác nhận thông tin vận hành chính thức.</p>
    </article></div>
</div>
@endsection
