<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký - Sàn Tím Vi En</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --text-main: #333333;
            --bg-main: #FFFFFF;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-main);
            color: var(--text-main);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .login-wrapper {
            display: flex;
            width: 100%;
            max-width: 1000px;
            min-height: 700px;
            background: #fff;
            border: 1px solid #EEEEEE;
            box-shadow: 0 20px 50px rgba(0,0,0,0.05);
        }
        
        .login-img {
            flex: 1;
            background-image: url('https://res.cloudinary.com/dxvml3sji/image/upload/q_auto/f_auto/v1778634216/wug2aeesprt4ghjksljq.png');
            background-size: cover;
            background-position: center;
            background-color: #F5F3FF;
            display: flex; align-items: center; justify-content: center;
            color: white; text-align: center; padding: 40px;
            position: relative;
        }
        .login-img::after {
            content: ''; position: absolute; top:0; left:0; width:100%; height:100%;
            background: rgba(124,58,237,0.4);
        }
        
        .login-img h2 {
            font-size: 40px;
            font-weight: 700;
            text-shadow: 0 2px 10px rgba(0,0,0,0.3);
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        
        .login-form {
            flex: 1;
            padding: 40px 60px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .login-title {
            font-weight: 700;
            font-size: 30px;
            margin-bottom: 10px;
            text-transform: uppercase;
        }
        
        .form-control {
            border-radius: 0;
            border: 1px solid #DDDDDD;
            padding: 12px;
            margin-bottom: 15px;
        }
        
        .form-control:focus {
            border-color: var(--text-main);
            box-shadow: none;
        }
        
        .form-label {
            font-weight: 600;
            font-size: 13px;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        
        .btn-ava-dark {
            background: #7C3AED;
            color: #FFFFFF;
            border: none;
            padding: 15px;
            font-weight: 700;
            text-transform: uppercase;
            width: 100%;
            transition: all 0.3s;
            margin-top: 10px;
        }
        
        .btn-ava-dark:hover {
            background: #6D28D9;
            color: #FFFFFF;
        }
        
        .divider {
            text-align: center;
            margin: 20px 0;
            position: relative;
        }
        
        .divider::before {
            content: "";
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            border-top: 1px solid #EEEEEE;
            z-index: 1;
        }
        
        .divider span {
            background: #fff;
            padding: 0 15px;
            position: relative;
            z-index: 2;
            color: #999;
            font-size: 14px;
        }

        .back-link {
            color: var(--text-main);
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 20px;
            display: inline-block;
        }
        
        .back-link:hover {
            text-decoration: underline;
        }
        
        .error-msg {
            color: #dc3545;
            font-size: 12px;
            margin-top: -10px;
            margin-bottom: 10px;
            display: block;
        }

        @media (max-width: 768px) {
            .login-wrapper { flex-direction: column; }
            .login-img { min-height: 200px; display: none; }
            .login-form { padding: 40px 20px; }
        }
    </style>
</head>
<body>

    <div class="login-wrapper">
        <div class="login-img">
            <div style="position:relative; z-index:1;">
                <div style="font-size:12px; letter-spacing:4px; font-weight:700; text-transform:uppercase; margin-bottom:15px; color:rgba(255,255,255,0.8);">THAM GIA NGAY</div>
                <h2>JOIN<br>SÀN TÍM</h2>
                <p style="font-size:14px; color:rgba(255,255,255,0.8); margin-top:10px;">Phong cách Việt — Sống đẹp mỗi ngày</p>
            </div>
        </div>
        
        <div class="login-form">
            <a href="{{ route('home') }}" class="back-link"><i class="fas fa-arrow-left me-2"></i>Về trang chủ</a>
            
            <h1 class="login-title">Tạo tài khoản</h1>
            <p class="text-muted mb-4">Bắt đầu hành trình thời trang của bạn tại Sàn Tím Vi En.</p>
            
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
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                    @error('password')
                        <span class="error-msg">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="form-label">Xác nhận mật khẩu</label>
                    <input type="password" name="password_confirmation" class="form-control" required>
                </div>
                
                <button type="submit" class="btn-ava-dark">ĐĂNG KÝ</button>
            </form>
            
            <div class="divider"><span>HOẶC</span></div>
            
            <div class="text-center" style="font-size: 14px;">
                Đã có tài khoản? <a href="{{ route('login') }}" style="color: #7C3AED; font-weight: 700;">Đăng nhập tại đây</a>
            </div>
        </div>
    </div>

</body>
</html>
