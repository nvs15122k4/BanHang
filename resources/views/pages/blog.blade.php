@extends('layouts.app')

@section('title', 'Blog - Sàn Tím Vi En')

@push('styles')
<style>
    .blog-header {
        padding: 80px 0;
        border-bottom: 1px solid #EEE;
        margin-bottom: 60px;
        text-align: center;
    }

    .page-title {
        font-weight: 700;
        font-size: 42px;
        letter-spacing: 2px;
        margin-bottom: 15px;
        text-transform: uppercase;
    }

    .blog-card {
        margin-bottom: 60px;
        transition: transform 0.3s ease;
    }
    
    .blog-card:hover .blog-img-wrap img {
        transform: scale(1.05);
    }

    .blog-img-wrap {
        width: 100%;
        height: 450px;
        overflow: hidden;
        margin-bottom: 25px;
        background: #F6F6F6;
    }

    .blog-img-wrap img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.6s ease;
    }

    .blog-meta {
        font-size: 12px;
        color: var(--text-light);
        text-transform: uppercase;
        font-weight: 700;
        letter-spacing: 2px;
        margin-bottom: 15px;
        display: block;
    }

    .blog-title {
        font-weight: 700;
        font-size: 26px;
        margin-bottom: 15px;
        line-height: 1.3;
    }
    
    .blog-title a {
        color: var(--text-main);
        text-decoration: none;
    }
    
    .blog-title a:hover {
        color: var(--text-light);
    }

    .blog-excerpt {
        color: var(--text-light);
        font-size: 16px;
        line-height: 1.7;
        margin-bottom: 25px;
    }

    .btn-more {
        font-weight: 700;
        text-transform: uppercase;
        font-size: 12px;
        letter-spacing: 2px;
        color: var(--text-main);
        text-decoration: none;
        padding-bottom: 5px;
        border-bottom: 2px solid #000;
        transition: all 0.3s;
    }
    
    .btn-more:hover {
        padding-left: 10px;
        border-bottom-color: var(--text-light);
    }

    .pagination-wrap {
        margin: 50px 0 100px;
    }
</style>
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
                <div class="blog-img-wrap" style="height: 600px;">
                    <img src="https://res.cloudinary.com/dxvml3sji/image/upload/q_auto/f_auto/v1778634215/eecny8uiiwp9goo7zngu.jpg" alt="Featured Post">
                </div>
                <div class="row">
                    <div class="col-lg-8 offset-lg-2 text-center">
                        <span class="blog-meta">Thời trang • 12 Tháng 5, 2024</span>
                        <h2 class="blog-title" style="font-size: 36px;"><a href="#">Nghệ thuật của sự tối giản: Cách xây dựng tủ đồ bền vững</a></h2>
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
                <li class="page-item active"><span class="page-link" style="background:#000;border-color:#000;">1</span></li>
                <li class="page-item"><a class="page-link" href="#" style="color:#000;">2</a></li>
                <li class="page-item"><a class="page-link" href="#" style="color:#000;">3</a></li>
            </ul>
        </nav>
    </div>
</div>
@endsection
