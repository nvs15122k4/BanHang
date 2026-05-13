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
            min-height: 600px;
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
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-align: center;
            padding: 40px;
            position: relative;
        }
        .login-img::after {
            content: '';
            position: absolute; top:0; left:0; width:100%; height:100%;
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
            padding: 60px;
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
            padding: 15px;
            margin-bottom: 20px;
        }
        
        .form-control:focus {
            border-color: var(--text-main);
            box-shadow: none;
        }
        
        .form-label {
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
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
        }
        
        .btn-ava-dark:hover {
            background: #6D28D9;
            color: #FFFFFF;
        }
        
        .divider {
            text-align: center;
            margin: 30px 0;
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
            margin-bottom: 30px;
            display: inline-block;
        }
        
        .back-link:hover {
            text-decoration: underline;
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
                <div style="font-size:12px; letter-spacing:4px; font-weight:700; text-transform:uppercase; margin-bottom:15px; color:rgba(255,255,255,0.8);">ƯU ĐÃI ĐẶC BIỆT</div>
                <h2>Giảm đến<br>50% OFF</h2>
                <p style="font-size:14px; color:rgba(255,255,255,0.8); margin-top:10px;">Sàn Tím Vi En — Phong cách Việt</p>
            </div>
        </div>
        
        <div class="login-form">
            <a href="{{ route('home') }}" class="back-link"><i class="fas fa-arrow-left me-2"></i>Về trang chủ</a>
            
            <h1 class="login-title">Đăng nhập</h1>
            <p class="text-muted mb-4">Chào mừng đến Sàn Tím Vi En! Vui lòng nhập thông tin của bạn.</p>
            
            @if(session('status'))
                <div class="alert alert-success rounded-0" style="background:#E8F5E9; color:#2E7D32; border:none;">
                    {{ session('status') }}
                </div>
            @endif
            
            <form method="POST" action="{{ route('login') }}">
                @csrf
                
                <div>
                    <label class="form-label">Địa chỉ Email</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required autofocus>
                    @error('email')
                        <div class="invalid-feedback mb-3" style="margin-top:-15px;">{{ $message }}</div>
                    @enderror
                </div>
                
                <div>
                    <div class="d-flex justify-content-between">
                        <label class="form-label">Mật khẩu</label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" style="color: var(--text-main); font-size: 13px;">Quên mật khẩu?</a>
                        @endif
                    </div>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                    @error('password')
                        <div class="invalid-feedback mb-3" style="margin-top:-15px;">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-check mb-4">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember" style="border-radius:0; accent-color:var(--text-main);">
                    <label class="form-check-label text-muted" for="remember" style="font-size:14px;">
                        Ghi nhớ đăng nhập
                    </label>
                </div>
                
                <button type="submit" class="btn-ava-dark">ĐĂNG NHẬP</button>
            </form>
            
            <div class="divider"><span>HOẶC</span></div>
            
            <div class="text-center" style="font-size: 14px;">
                Mới đến Sàn Tím? <a href="{{ route('register') }}" style="color: #7C3AED; font-weight: 700;">Đăng ký ngay</a>
            </div>
        </div>
    </div>

</body>
</html>
