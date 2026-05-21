@extends('layouts.app')

@section('title', 'Liên hệ - Sàn Tím Vi En')

@push('styles')
    @vite(['public/css/views/static_pages.css'])
@endpush

@section('content')
<section class="contact-hero">
    <div class="container">
        <h1 class="page-title">Liên hệ</h1>
        <p class="opacity-70-custom">Chúng tôi luôn sẵn sàng lắng nghe ý kiến của bạn.</p>
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
                    <button type="submit" class="btn btn-ava-dark rounded-15-px-custom">GỬI TIN NHẮN</button>
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
