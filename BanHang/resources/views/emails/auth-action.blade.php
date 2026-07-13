<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} - Sàn Tím Vi En</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f5f3ff; color: #2e1065; font-family: Arial, Helvetica, sans-serif;">
    <span style="display: none; max-height: 0; overflow: hidden; opacity: 0;">{{ $preheader }}</span>
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f5f3ff; padding: 34px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width: 600px; background-color: #ffffff; border-radius: 18px; overflow: hidden; box-shadow: 0 6px 26px rgba(76, 29, 149, 0.08);">
                    <tr>
                        <td style="background-color: #6d28d9; padding: 28px 38px;">
                            <div style="font-size: 24px; line-height: 30px; font-weight: bold; letter-spacing: 0.2px; color: #ffffff;">Sàn Tím Vi En</div>
                            <div style="font-size: 13px; line-height: 20px; margin-top: 4px; color: #ede9fe;">Thời trang Việt phong cách</div>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 38px 38px 20px;">
                            <div style="font-size: 13px; line-height: 18px; font-weight: bold; letter-spacing: 1px; color: #7c3aed;">TÀI KHOẢN CỦA BẠN</div>
                            <h1 style="font-size: 27px; line-height: 35px; margin: 12px 0 20px; color: #1f1535;">{{ $title }}</h1>
                            <p style="font-size: 16px; line-height: 26px; margin: 0 0 14px; color: #3f3a4b;">Xin chào <strong>{{ $name }}</strong>,</p>
                            <p style="font-size: 16px; line-height: 26px; margin: 0 0 14px; color: #3f3a4b;">{{ $description }}</p>
                            <p style="font-size: 16px; line-height: 26px; margin: 0 0 28px; color: #3f3a4b;">{{ $instruction }}</p>
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td align="center" style="padding-bottom: 28px;">
                                        <a href="{{ $actionUrl }}" target="_blank" rel="noopener" style="display: inline-block; padding: 16px 30px; border-radius: 9px; background-color: #6d28d9; color: #ffffff; font-size: 15px; line-height: 20px; font-weight: bold; text-decoration: none; letter-spacing: 0.4px;">{{ $actionLabel }}</a>
                                    </td>
                                </tr>
                            </table>
                            <div style="border-radius: 9px; background-color: #f5f3ff; border: 1px solid #ddd6fe; padding: 15px 17px; font-size: 14px; line-height: 22px; color: #5b21b6;">
                                {{ $expires }}
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 0 38px 34px;">
                            <p style="font-size: 13px; line-height: 21px; margin: 8px 0 22px; color: #6b6479;">{{ $notice }}</p>
                            <p style="font-size: 13px; line-height: 21px; margin: 0 0 7px; color: #6b6479;">Nếu nút phía trên không hoạt động, hãy sao chép liên kết sau vào trình duyệt:</p>
                            <p style="font-size: 12px; line-height: 20px; margin: 0; word-break: break-all;">
                                <a href="{{ $actionUrl }}" style="color: #6d28d9; text-decoration: underline;">{{ $actionUrl }}</a>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="background-color: #faf9ff; border-top: 1px solid #ede9fe; padding: 22px 38px; font-size: 12px; line-height: 20px; color: #7b7389;">
                            Email này được gửi tự động từ Sàn Tím Vi En. Vui lòng không trả lời email này.<br>
                            © {{ date('Y') }} Sàn Tím Vi En. Mọi quyền được bảo lưu.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
