<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - Sàn Tím Vi En</title>
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
                <div class="uix-1295b2a563">ƯU ĐÃI ĐẶC BIỆT</div>
                <h2>Giảm đến<br>50% OFF</h2>
                <p class="uix-d8c19c644b">Sàn Tím Vi En — Phong cách Việt</p>
            </div>
        </div>
        
        <div class="login-form">
            <a href="{{ route('home') }}" class="back-link"><i class="fas fa-arrow-left me-2"></i>Về trang chủ</a>
            
            <h1 class="login-title">Đăng nhập</h1>
            <p class="text-muted mb-4">Chào mừng đến Sàn Tím Vi En! Vui lòng nhập thông tin của bạn.</p>
            
            @if(session('status'))
                <div class="alert alert-success rounded-0 uix-9b2859607a">
                    {{ session('status') }}
                </div>
            @endif
            
            <form method="POST" action="{{ route('login') }}">
                @csrf
                
                <div>
                    <label class="form-label">Địa chỉ Email</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required autofocus>
                    @error('email')
                        <div class="invalid-feedback mb-3 uix-dd7c389abd">{{ $message }}</div>
                    @enderror
                </div>
                
                <div>
                    <div class="d-flex justify-content-between">
                        <label class="form-label">Mật khẩu</label>
                        @if (Route::has('password.request'))
                            <a class="uix-e2cf23cc81" href="{{ route('password.request') }}">Quên mật khẩu?</a>
                        @endif
                    </div>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                    @error('password')
                        <div class="invalid-feedback mb-3 uix-dd7c389abd">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-check mb-4">
                    <input class="form-check-input uix-6b2862890a" type="checkbox" name="remember" id="remember">
                    <label class="form-check-label text-muted uix-ba62946b0e" for="remember">
                        Ghi nhớ đăng nhập
                    </label>
                </div>
                
                <button type="submit" class="btn-ava-dark">ĐĂNG NHẬP</button>
            </form>
            
            <div class="divider"><span>HOẶC</span></div>
            
            <div class="text-center uix-df67104f3b">
                Mới đến Sàn Tím? <a class="uix-2b3997a8e4" href="{{ route('register') }}">Đăng ký ngay</a>
            </div>
        </div>
    </div>

</body>
</html>
