<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác thực Email - AVA</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --text-main: #1A1A1A;
            --text-light: #666666;
            --bg-main: #FFFFFF;
            --accent: #000000;
        }
        
        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--bg-main);
            color: var(--text-main);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            overflow: hidden;
        }
        
        .bg-element {
            position: fixed;
            z-index: -1;
            background: #F9F9F9;
            border-radius: 50%;
        }
        .el-1 { width: 400px; height: 400px; top: -100px; right: -100px; }
        .el-2 { width: 300px; height: 300px; bottom: -50px; left: -50px; }

        .auth-card {
            width: 100%;
            max-width: 480px;
            padding: 50px;
            background: #fff;
            border: 1px solid #EEEEEE;
            text-align: center;
        }
        
        .brand-logo {
            font-weight: 700;
            font-size: 28px;
            letter-spacing: 4px;
            text-decoration: none;
            color: var(--text-main);
            display: block;
            margin-bottom: 40px;
        }
        
        .auth-title {
            font-weight: 700;
            font-size: 24px;
            margin-bottom: 20px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .auth-subtitle {
            color: var(--text-light);
            font-size: 15px;
            line-height: 1.6;
            margin-bottom: 35px;
        }
        
        .btn-ava-dark {
            background: var(--accent);
            color: #FFFFFF;
            border: 1px solid var(--accent);
            padding: 16px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 2px;
            width: 100%;
            font-size: 14px;
            transition: all 0.3s;
            cursor: pointer;
            margin-top: 10px;
            text-decoration: none;
            display: block;
        }
        
        .btn-ava-dark:hover {
            background: #FFFFFF;
            color: var(--accent);
        }
        
        .btn-logout {
            background: transparent;
            color: var(--text-light);
            border: none;
            margin-top: 25px;
            font-size: 14px;
            font-weight: 500;
            text-decoration: underline;
            cursor: pointer;
        }
    </style>
</head>
<body>

    <div class="bg-element el-1"></div>
    <div class="bg-element el-2"></div>

    <div class="auth-card">
        <a href="{{ route('home') }}" class="brand-logo">AVA</a>
        
        <h1 class="auth-title">Xác thực Email</h1>
        <p class="auth-subtitle">Cảm ơn bạn đã đăng ký! Trước khi bắt đầu, bạn có thể xác nhận địa chỉ email của mình bằng cách nhấp vào liên kết chúng tôi vừa gửi cho bạn không? Nếu bạn không nhận được email, chúng tôi sẽ vui lòng gửi lại cho bạn một email khác.</p>
        
        @if (session('status') == 'verification-link-sent')
            <div class="alert alert-success rounded-0 mb-4" style="background:#F0FDF4; color:#166534; border:none; font-size: 14px;">
                Một liên kết xác thực mới đã được gửi đến địa chỉ email bạn cung cấp khi đăng ký.
            </div>
        @endif

        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="btn-ava-dark">GỬI LẠI EMAIL XÁC THỰC</button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn-logout">Đăng xuất</button>
        </form>
    </div>

</body>
</html>