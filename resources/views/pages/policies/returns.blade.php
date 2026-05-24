@extends('layouts.app')

@section('title', 'Thông tin đổi trả - Sàn Tím Vi En')
@section('meta_description', 'Điều kiện đổi trả và hoàn tiền Sàn Tím Vi En đang chờ xác nhận trước khi công bố.')
@section('robots', 'noindex, follow')
@section('canonical', 'https://santimvien.vn/chinh-sach/doi-tra')

@section('content')
<div class="container py-5">
    <nav class="mb-4" aria-label="breadcrumb"><a href="{{ route('home') }}">Trang chủ</a> / Thông tin đổi trả</nav>
    <div class="row justify-content-center"><article class="col-lg-9">
        <h1 class="page-title text-start">Thông tin đổi trả</h1>
        <div class="alert alert-info">Trang này chưa được lập chỉ mục vì thời hạn đổi trả, điều kiện sản phẩm và phương thức hoàn tiền chưa được chủ shop xác nhận.</div>
        <p>Website sẽ công bố quy trình đổi trả tại URL này sau khi chính sách vận hành được chốt. Không có cam kết thời hạn đổi trả nào được áp dụng từ nội dung trang hiện tại.</p>
        <p>Để hạn chế chọn nhầm kích cỡ trước khi chính sách hoàn thiện, hãy tham khảo <a href="{{ route('guides.size') }}">hướng dẫn chọn size</a>.</p>
    </article></div>
</div>
@endsection
