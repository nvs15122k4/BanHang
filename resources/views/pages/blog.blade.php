@extends('layouts.app')

@section('title', 'Blog thời trang - Sàn Tím Vi En')
@section('meta_description', 'Đọc blog thời trang Sàn Tím Vi En để cập nhật xu hướng, cảm hứng phối đồ và bí quyết xây dựng phong cách Việt hiện đại.')
@section('canonical', 'https://santimvien.vn/blog')
@section('og_title', 'Blog thời trang - Sàn Tím Vi En')
@section('og_description', 'Đọc blog thời trang Sàn Tím Vi En để cập nhật xu hướng, cảm hứng phối đồ và bí quyết xây dựng phong cách Việt hiện đại.')

@push('styles')
    @vite(['public/css/views/static_pages.css'])
@endpush

@section('content')
<div class="blog-header">
    <div class="container">
        <h1 class="page-title">Góc Thời Trang</h1>
        <p class="text-muted">Khám phá phong cách sống, xu hướng và cảm hứng thời trang mới nhất.</p>
    </div>
</div>

<div class="container">
    <div class="row">
        <!-- Featured Post -->
        <div class="col-12 mb-5">
            <div class="blog-card">
                <div class="blog-img-wrap h-600-px-custom">
                    <img src="https://res.cloudinary.com/dxvml3sji/image/upload/q_auto/f_auto/v1778634215/eecny8uiiwp9goo7zngu.jpg" alt="Featured Post">
                </div>
                <div class="row">
                    <div class="col-lg-8 offset-lg-2 text-center">
                        <span class="blog-meta">Thời trang • 12 Tháng 5, 2024</span>
                        <h2 class="blog-title text-36-px-custom"><a href="#">Nghệ thuật của sự tối giản: Cách xây dựng tủ đồ bền vững</a></h2>
                        <p class="blog-excerpt">Sống tối giản không có nghĩa là sở hữu ít hơn, mà là làm cho mỗi món đồ bạn sở hữu trở nên có giá trị hơn. Cùng Sàn Tím Vi En khám phá cách xây dựng phong cách vượt thời gian.</p>
                        <a href="#" class="btn-more">ĐỌC TIẾP</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grid Posts -->
        <div class="col-md-6">
            <div class="blog-card">
                <div class="blog-img-wrap">
                    <img src="https://res.cloudinary.com/dxvml3sji/image/upload/q_auto/f_auto/v1778634688/zhwiyovkz6zuhsn4nvnq.jpg" alt="Post">
                </div>
                <span class="blog-meta">Xu hướng • 10 Tháng 5, 2024</span>
                <h2 class="blog-title"><a href="#">Những món đồ không thể thiếu trong mùa Hè 2024</a></h2>
                <p class="blog-excerpt">Từ chất liệu linen thoáng mát đến những gam màu pastel dịu nhẹ, hãy cùng điểm qua những xu hướng sẽ thống trị mùa hè này.</p>
                <a href="#" class="btn-more">ĐỌC TIẾP</a>
            </div>
        </div>

        <div class="col-md-6">
            <div class="blog-card">
                <div class="blog-img-wrap">
                    <img src="https://res.cloudinary.com/dxvml3sji/image/upload/q_auto/f_auto/v1778634687/zgwqjwlgl7rdxr1vfxzv.jpg" alt="Post">
                </div>
                <span class="blog-meta">Phong cách sống • 05 Tháng 5, 2024</span>
                <h2 class="blog-title"><a href="#">Cách phối đồ nam tính nhưng vẫn thanh lịch</a></h2>
                <p class="blog-excerpt">Thời trang nam đang chuyển mình mạnh mẽ. Làm thế nào để vừa giữ được vẻ nam tính, vừa toát lên sự tinh tế?</p>
                <a href="#" class="btn-more">ĐỌC TIẾP</a>
            </div>
        </div>
    </div>

    <div class="pagination-wrap d-flex justify-content-center">
        <!-- Simple numeric pagination example -->
        <nav>
            <ul class="pagination pagination-dark">
                <li class="page-item active"><span class="page-link bg-black-custom border-black-custom">1</span></li>
                <li class="page-item"><a class="page-link text-black-custom" href="#">2</a></li>
                <li class="page-item"><a class="page-link text-black-custom" href="#">3</a></li>
            </ul>
        </nav>
    </div>
</div>
@endsection
