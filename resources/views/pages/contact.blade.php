@extends('layouts.app')

@section('title', 'Liên hệ - Sàn Tím Vi En')

@push('styles')
<style>
    .contact-hero {
        background: #000;
        color: #fff;
        padding: 100px 0;
        text-align: center;
        margin-bottom: 80px;
    }

    .page-title {
        font-weight: 700;
        font-size: 48px;
        letter-spacing: 4px;
        margin-bottom: 20px;
        text-transform: uppercase;
    }

    .contact-wrapper {
        margin-bottom: 100px;
    }

    .contact-info-panel {
        padding: 60px;
        background: #F9F9F9;
        height: 100%;
    }

    .info-item {
        margin-bottom: 40px;
    }

    .info-label {
        font-weight: 700;
        text-transform: uppercase;
        font-size: 12px;
        letter-spacing: 2px;
        color: var(--text-light);
        margin-bottom: 10px;
        display: block;
    }

    .info-value {
        font-size: 18px;
        font-weight: 500;
        color: var(--text-main);
    }

    .contact-form-panel {
        padding: 60px;
        border: 1px solid #EEEEEE;
        height: 100%;
    }

    .form-title {
        font-weight: 700;
        font-size: 24px;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 40px;
    }

    .form-label {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 12px;
        letter-spacing: 1px;
        margin-bottom: 10px;
    }

    .form-control {
        border-radius: 0;
        border: 1px solid #E5E5E5;
        padding: 15px;
        font-size: 15px;
        margin-bottom: 25px;
        transition: all 0.3s;
    }

    .form-control:focus {
        border-color: #000;
        box-shadow: none;
        background: #FAFAFA;
    }

    .btn-ava-dark {
        background: #000;
        color: #FFFFFF;
        border: 1px solid #000;
        padding: 18px 50px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 2px;
        transition: all 0.3s;
        width: 100%;
    }

    .btn-ava-dark:hover {
        background: #FFFFFF;
        color: #000;
    }

    .social-links {
        display: flex;
        gap: 20px;
        margin-top: 40px;
    }

    .social-link {
        width: 45px;
        height: 45px;
        border: 1px solid #DDD;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--text-main);
        text-decoration: none;
        transition: all 0.3s;
    }

    .social-link:hover {
        background: #000;
        color: #fff;
        border-color: #000;
    }

    /* Map Placeholder */
    .map-container {
        width: 100%;
        height: 450px;
        background: #EEE;
        margin-bottom: 100px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #999;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 2px;
    }
</style>
@endpush

@section('content')
<section class="contact-hero">
    <div class="container">
        <h1 class="page-title">Liên hệ</h1>
        <p style="opacity: 0.7;">Chúng tôi luôn sẵn sàng lắng nghe ý kiến của bạn.</p>
    </div>
</section>

<div class="container contact-wrapper">
    <div class="row g-0">
        <div class="col-lg-5">
            <div class="contact-info-panel">
                <div class="info-item">
                    <span class="info-label">Địa chỉ cửa hàng</span>
                    <span class="info-value">123 Đường Thời Trang, Quận 1, TP. Hồ Chí Minh</span>
                </div>

                <div class="info-item">
                    <span class="info-label">Số điện thoại</span>
                    <span class="info-value">+84 (28) 3822-4242</span>
                </div>

                <div class="info-item">
                    <span class="info-label">Địa chỉ Email</span>
                    <span class="info-value">support@santimvien.vn</span>
                </div>

                <div class="info-item">
                    <span class="info-label">Giờ mở cửa</span>
                    <span class="info-value">Thứ 2 - Thứ 7: 9:00 - 21:00<br>Chủ Nhật: 10:00 - 18:00</span>
                </div>

                <div class="social-links">
                    <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-tiktok"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-pinterest"></i></a>
                </div>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="contact-form-panel">
                <h2 class="form-title">Gửi tin nhắn cho chúng tôi</h2>
                <form action="#" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">Họ và tên</label>
                            <input type="text" class="form-control" placeholder="Nguyễn Văn A" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Địa chỉ Email</label>
                            <input type="email" class="form-control" placeholder="example@email.com" required>
                        </div>
                    </div>
                    <div>
                        <label class="form-label">Tiêu đề</label>
                        <input type="text" class="form-control" placeholder="Tôi cần hỗ trợ về..." required>
                    </div>
                    <div>
                        <label class="form-label">Nội dung tin nhắn</label>
                        <textarea class="form-control" rows="6" placeholder="Viết tin nhắn của bạn tại đây..." required></textarea>
                    </div>
                    <button type="submit" class="btn-ava-dark">GỬI TIN NHẮN</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid p-0">
    <div class="map-container">
        <i class="fas fa-map-marked-alt me-3 fa-2x"></i> Google Maps Placeholder
    </div>
</div>
@endsection
