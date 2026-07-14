<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Đăng ký - Sàn Tím Vi En</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['public/css/auth.css', 'public/css/extracted-inline.css', 'public/js/app.js'])
</head>
<body>

    <div class="login-wrapper">
        <div class="login-img">
            <div class="uix-761c00d357">
                <div class="uix-1295b2a563">THAM GIA NGAY</div>
                <h2>JOIN<br>SÀN TÍM</h2>
                <p class="uix-d8c19c644b">Phong cách Việt — Sống đẹp mỗi ngày</p>
            </div>
        </div>
        
        <div class="login-form">
            <a href="{{ route('home') }}" class="back-link"><i class="fas fa-arrow-left me-2"></i>Về trang chủ</a>
            
            <h1 class="login-title">Tạo tài khoản</h1>
            <p class="text-muted mb-4">Bắt đầu hành trình thời trang của bạn.</p>
            
            <form method="POST" action="{{ route('register') }}">
                @csrf
                
                <div>
                    <label class="form-label">Họ và tên</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required autofocus>
                    @error('name')
                        <span class="error-msg">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="form-label">Địa chỉ Email</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                    @error('email')
                        <span class="error-msg">{{ $message }}</span>
                    @enderror
                </div>
                
                <div>
                    <label class="form-label">Mật khẩu</label>
                    <div class="password-field">
                        <input id="register-password" type="password" name="password" class="form-control @error('password') is-invalid @enderror" required autocomplete="new-password">
                        <button type="button" class="password-toggle js-password-toggle" data-target="register-password" aria-label="Hiện mật khẩu" aria-controls="register-password" aria-pressed="false">
                            <i class="fas fa-eye" aria-hidden="true"></i>
                        </button>
                    </div>
                    @error('password')
                        <span class="error-msg">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="form-label">Xác nhận mật khẩu</label>
                    <div class="password-field">
                        <input id="register-password-confirmation" type="password" name="password_confirmation" class="form-control" required autocomplete="new-password">
                        <button type="button" class="password-toggle js-password-toggle" data-target="register-password-confirmation" aria-label="Hiện mật khẩu" aria-controls="register-password-confirmation" aria-pressed="false">
                            <i class="fas fa-eye" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>
                
                <button type="submit" class="btn-ava-dark">ĐĂNG KÝ</button>
            </form>
            
            <div class="divider"><span>HOẶC</span></div>
            
            <div class="text-center uix-df67104f3b">
                Đã có tài khoản? <a class="uix-2b3997a8e4" href="{{ route('login') }}">Đăng nhập tại đây</a>
            </div>
        </div>
    </div>

    <script>
        document.querySelectorAll('.js-password-toggle').forEach(function (button) {
            button.addEventListener('click', function () {
                var input = document.getElementById(button.dataset.target);
                var isVisible = input.type === 'text';

                input.type = isVisible ? 'password' : 'text';
                button.setAttribute('aria-label', isVisible ? 'Hiện mật khẩu' : 'Ẩn mật khẩu');
                button.setAttribute('aria-pressed', isVisible ? 'false' : 'true');
                button.querySelector('i').classList.toggle('fa-eye', isVisible);
                button.querySelector('i').classList.toggle('fa-eye-slash', !isVisible);
            });
        });
    </script>
</body>
</html>
