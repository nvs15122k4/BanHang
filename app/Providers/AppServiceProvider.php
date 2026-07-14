<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (request()->getHost() === 'banhang.vnsang.io.vn') {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        Vite::prefetch(concurrency: 3);

        VerifyEmail::toMailUsing(function (object $notifiable, string $url): MailMessage {
            return (new MailMessage)
                ->from(config('mail.from.address'), 'Sàn Tím Vi En')
                ->subject('[Sàn Tím Vi En] Xác nhận đăng ký tài khoản')
                ->view('emails.auth-action', [
                    'preheader' => 'Xác nhận email để hoàn tất đăng ký tài khoản Sàn Tím Vi En.',
                    'name' => $notifiable->name,
                    'title' => 'Xác nhận đăng ký tài khoản',
                    'description' => 'Cảm ơn bạn đã đăng ký tại Sàn Tím Vi En. Tài khoản của bạn chỉ còn một bước để được kích hoạt.',
                    'instruction' => 'Vui lòng nhấn nút bên dưới để xác nhận địa chỉ email và hoàn tất đăng ký.',
                    'actionLabel' => 'CHẤP NHẬN',
                    'actionUrl' => $url,
                    'expires' => 'Liên kết xác nhận có hiệu lực trong 60 phút.',
                    'notice' => 'Nếu bạn không thực hiện đăng ký này, bạn có thể bỏ qua email. Không ai có thể sử dụng tài khoản cho đến khi email được xác nhận.',
                ])
                ->text('emails.auth-action-text', [
                    'name' => $notifiable->name,
                    'title' => 'Xác nhận đăng ký tài khoản',
                    'description' => 'Cảm ơn bạn đã đăng ký tại Sàn Tím Vi En. Tài khoản của bạn chỉ còn một bước để được kích hoạt.',
                    'instruction' => 'Truy cập liên kết bên dưới để xác nhận địa chỉ email và hoàn tất đăng ký:',
                    'actionUrl' => $url,
                    'expires' => 'Liên kết xác nhận có hiệu lực trong 60 phút.',
                    'notice' => 'Nếu bạn không thực hiện đăng ký này, bạn có thể bỏ qua email.',
                ]);
        });

        ResetPassword::toMailUsing(function (object $notifiable, string $token): MailMessage {
            $url = route('password.reset', [
                'token' => $token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ]);

            return (new MailMessage)
                ->from(config('mail.from.address'), 'Sàn Tím Vi En')
                ->subject('[Sàn Tím Vi En] Yêu cầu đặt lại mật khẩu')
                ->view('emails.auth-action', [
                    'preheader' => 'Mở trang đặt lại mật khẩu tài khoản Sàn Tím Vi En.',
                    'name' => $notifiable->name,
                    'title' => 'Đặt lại mật khẩu',
                    'description' => 'Chúng tôi nhận được yêu cầu đặt lại mật khẩu cho tài khoản Sàn Tím Vi En của bạn.',
                    'instruction' => 'Nhấn nút bên dưới để mở trang tạo mật khẩu mới an toàn.',
                    'actionLabel' => 'CHẤP NHẬN',
                    'actionUrl' => $url,
                    'expires' => 'Liên kết đặt lại mật khẩu có hiệu lực trong 60 phút.',
                    'notice' => 'Nếu bạn không yêu cầu thay đổi mật khẩu, hãy bỏ qua email này. Mật khẩu hiện tại của bạn vẫn được giữ nguyên.',
                ])
                ->text('emails.auth-action-text', [
                    'name' => $notifiable->name,
                    'title' => 'Đặt lại mật khẩu',
                    'description' => 'Chúng tôi nhận được yêu cầu đặt lại mật khẩu cho tài khoản Sàn Tím Vi En của bạn.',
                    'instruction' => 'Truy cập liên kết bên dưới để tạo mật khẩu mới:',
                    'actionUrl' => $url,
                    'expires' => 'Liên kết đặt lại mật khẩu có hiệu lực trong 60 phút.',
                    'notice' => 'Nếu bạn không yêu cầu thay đổi mật khẩu, hãy bỏ qua email này. Mật khẩu hiện tại của bạn vẫn được giữ nguyên.',
                ]);
        });
    }
}
