<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Xác thực Email - AVA</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
@vite(['public/css/auth.css', 'public/css/extracted-inline.css', 'public/js/app.js'])
</head>
<body>

    <div class="bg-element el-1"></div>
    <div class="bg-element el-2"></div>

    <div class="auth-card">
        <a href="{{ route('home') }}" class="brand-logo">AVA</a>
        
        <h1 class="auth-title">Xác nhận Email</h1>
        <p class="auth-subtitle">Chúng tôi đã gửi email xác nhận đăng ký. Hãy nhấn <strong>Chấp nhận</strong> trong email để hoàn tất đăng ký, sau đó bạn có thể đăng nhập.</p>
        
        @if (session('status'))
            <div class="alert alert-success rounded-0 mb-4 uix-063c78b9e9">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <div class="mb-4">
                <label class="form-label">Địa chỉ Email</label>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email', $email ?? '') }}" required>
                @error('email')
                    <div class="invalid-feedback uix-4fc05cceb3">{{ $message }}</div>
                @enderror
            </div>
            <button type="submit" class="btn-ava-dark">GỬI LẠI EMAIL XÁC THỰC</button>
        </form>

        <a href="{{ route('login') }}" class="back-to-login">
            <i class="fas fa-arrow-left me-2"></i> Quay lại Đăng nhập
        </a>
    </div>

</body>
</html>
