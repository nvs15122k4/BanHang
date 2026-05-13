@extends('layouts.app')

@section('title', 'Về chúng tôi - Sàn Tím Vi En')

@push('styles')
<style>
    .about-hero {
        background: #F9F9F9;
        padding: 100px 0;
        text-align: center;
        margin-bottom: 80px;
    }

    .page-title {
        font-weight: 700;
        font-size: 48px;
        letter-spacing: 2px;
        margin-bottom: 20px;
        text-transform: uppercase;
    }

    .about-content {
        margin-bottom: 100px;
    }

    .section-title {
        font-weight: 700;
        font-size: 28px;
        margin-bottom: 30px;
        text-transform: uppercase;
        letter-spacing: 1px;
        position: relative;
        padding-bottom: 15px;
    }
    
    .section-title::after {
        content: '';
        position: absolute; bottom:0; left:0;
        width: 60px; height: 3px;
        background: #7C3AED;
    }

    .about-text {
        font-size: 18px;
        line-height: 1.8;
        color: var(--text-light);
    }

    .about-img-wrapper {
        position: relative;
        overflow: hidden;
    }

    .about-img {
        width: 100%;
        height: 600px;
        object-fit: cover;
        transition: transform 0.5s ease;
    }
    
    .about-img:hover {
        transform: scale(1.05);
    }

    .stat-container {
        background: #000;
        color: #fff;
        padding: 80px 0;
        margin-bottom: 100px;
    }

    .stat-box {
        text-align: center;
        padding: 20px;
    }

    .stat-number {
        font-size: 48px;
        font-weight: 700;
        margin-bottom: 10px;
        display: block;
    }

    .stat-label {
        font-weight: 500;
        text-transform: uppercase;
        font-size: 14px;
        letter-spacing: 2px;
        color: #999;
    }

    .vision-card {
        padding: 40px;
        border: 1px solid #EEE;
        height: 100%;
        transition: all 0.3s;
    }
    
    .vision-card:hover {
        border-color: #000;
    }

    .vision-icon {
        font-size: 32px;
        margin-bottom: 25px;
        color: var(--text-main);
    }
</style>
@endpush

@section('content')
<section class="about-hero">
    <div class="container">
        <h1 class="page-title">Về Sàn Tím Vi En</h1>
        <p class="text-muted" style="max-width: 600px; margin: 0 auto;">Thương hiệu thời trang Việt — Phong cách hiện đại, thuần Việt.</p>
    </div>
</section>

<div class="container about-content">
    <div class="row align-items-center">
        <div class="col-lg-6 mb-5 mb-lg-0">
            <div class="about-img-wrapper">
                <img src="https://images.unsplash.com/photo-1441984904996-e0b6ba687e04?auto=format&fit=crop&w=1200&q=80" alt="AVA Studio" class="about-img">
            </div>
        </div>
        <div class="col-lg-5 offset-lg-1">
            <h2 class="section-title">Câu chuyện của chúng tôi</h2>
            <p class="about-text">
                Được thành lập vào năm 2024, Sàn Tím Vi En đã phát triển từ một cửa hàng boutique nhỏ thành một thương hiệu thời trang thuần Việt được yêu thích. Sứ mệnh của chúng tôi là cung cấp trang phục chất lượng cao, phong cách và giá cả phải chăng cho tất cả người Việt muốn thể hiện bản sắc riêng.
            </p>
            <p class="about-text mt-4">
                Chúng tôi tin rằng thời trang không chỉ là quần áo; đó là về sự tự tin và cá tính Việt. Mỗi sản phẩm trong bộ sưu tập của chúng tôi đều được lựa chọn kỹ lưỡng để đảm bảo chất lượng vượt trội và phong cách thuần Việt.
            </p>
        </div>
    </div>
</div>

<section class="stat-container">
    <div class="container">
        <div class="row">
            <div class="col-md-3 col-6 mb-4 mb-md-0">
                <div class="stat-box">
                    <span class="stat-number">10K+</span>
                    <span class="stat-label">Khách hàng hài lòng</span>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-4 mb-md-0">
                <div class="stat-box">
                    <span class="stat-number">500+</span>
                    <span class="stat-label">Mẫu thiết kế mới</span>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-box">
                    <span class="stat-number">24/7</span>
                    <span class="stat-label">Hỗ trợ tận tâm</span>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-box">
                    <span class="stat-number">100%</span>
                    <span class="stat-label">Cam kết chất lượng</span>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="container mb-5 pb-5">
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="vision-card">
                <div class="vision-icon"><i class="fas fa-gem"></i></div>
                <h4 class="fw-bold mb-3">Chất lượng hàng đầu</h4>
                <p class="text-muted">Chúng tôi sử dụng những nguyên liệu tốt nhất để đảm bảo mỗi sản phẩm bền bỉ theo thời gian.</p>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="vision-card">
                <div class="vision-icon"><i class="fas fa-leaf"></i></div>
                <h4 class="fw-bold mb-3">Bền vững</h4>
                <p class="text-muted">Sàn Tím Vi En cam kết giảm thiểu tác động đến môi trường thông qua các quy trình sản xuất có trách nhiệm.</p>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="vision-card">
                <div class="vision-icon"><i class="fas fa-bolt"></i></div>
                <h4 class="fw-bold mb-3">Đổi mới</h4>
                <p class="text-muted">Luôn dẫn đầu xu hướng với những thiết kế sáng tạo và hiện đại nhất.</p>
            </div>
        </div>
    </div>
</div>
@endsection
