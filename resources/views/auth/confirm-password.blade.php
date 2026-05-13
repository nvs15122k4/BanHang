<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác nhận mật khẩu - AVA</title>
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
        }
        
        .brand-logo {
            font-weight: 700;
            font-size: 28px;
            letter-spacing: 4px;
            text-decoration: none;
            color: var(--text-main);
            display: block;
            margin-bottom: 40px;
            text-align: center;
        }
        
        .auth-title {
            font-weight: 700;
            font-size: 24px;
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
            text-align: center;
        }
        
        .auth-subtitle {
            color: var(--text-light);
            font-size: 15px;
            line-height: 1.6;
            margin-bottom: 35px;
            text-align: center;
        }
        
        .form-label {
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
            color: var(--text-main);
        }
        
        .form-control {
            border-radius: 0;
            border: 1px solid #E5E5E5;
            padding: 14px 18px;
            font-size: 15px;
            margin-bottom: 25px;
            transition: all 0.3s;
        }
        
        .form-control:focus {
            border-color: var(--accent);
            box-shadow: none;
            background-color: #FAFAFA;
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
        }
        
        .btn-ava-dark:hover {
            background: #FFFFFF;
            color: var(--accent);
        }
    </style>
</head>
<body>

    <div class="bg-element el-1"></div>
    <div class="bg-element el-2"></div>

    <div class="auth-card">
        <a href="{{ route('home') }}" class="brand-logo">AVA</a>
        
        <h1 class="auth-title">Xác nhận mật khẩu</h1>
        <p class="auth-subtitle">Đây là khu vực an toàn của ứng dụng. Vui lòng xác nhận mật khẩu của bạn trước khi tiếp tục.</p>
        
        <form method="POST" action="{{ route('password.confirm') }}">
            @csrf
            
            <div class="mb-4">
                <label class="form-label">Mật khẩu</label>
                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" 
                       placeholder="Nhập mật khẩu của bạn" required autocomplete="current-password">
                @error('password')
                    <div class="invalid-feedback" style="margin-top:-20px; margin-bottom: 20px;">{{ $message }}</div>
                @enderror
            </div>
            
            <button type="submit" class="btn-ava-dark">XÁC NHẬN</button>
        </form>
    </div>

</body>
</html>