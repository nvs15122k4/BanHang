<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt lại mật khẩu - AVA</title>
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
        
        <h1 class="auth-title">Đặt lại mật khẩu</h1>
        
        <form method="POST" action="{{ route('password.store') }}">
            @csrf
            
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <div class="mb-3">
                <label class="form-label">Địa chỉ Email</label>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                       value="{{ old('email', $request->email) }}" required readonly>
                @error('email')
                    <div class="invalid-feedback uix-4fc05cceb3">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Mật khẩu mới</label>
                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required autofocus>
                @error('password')
                    <div class="invalid-feedback uix-4fc05cceb3">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label class="form-label">Xác nhận mật khẩu</label>
                <input type="password" name="password_confirmation" class="form-control" required>
            </div>
            
            <button type="submit" class="btn-ava-dark">CẬP NHẬT MẬT KHẨU</button>
        </form>
    </div>

</body>
</html>
