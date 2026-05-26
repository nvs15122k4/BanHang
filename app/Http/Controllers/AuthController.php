<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Show login form
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Handle login
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            $user = Auth::user();

            if (! $user->hasVerifiedEmail()) {
                Auth::logout();
                $request->session()->regenerateToken();

                return redirect()
                    ->route('verification.notice', ['email' => $user->email])
                    ->with('status', 'Vui lòng xác nhận email trước khi đăng nhập.');
            }

            $message = $user->isAdmin() ? 'Chào mừng Admin!' : 'Đăng nhập thành công!';

            $redirect = Auth::user()->isAdmin() ? route('home') : route('home');
            return redirect()->intended($redirect)->with('success', $message);
        }

        return back()->withErrors([
            'email' => 'Thông tin đăng nhập không chính xác.',
        ])->onlyInput('email');
    }

    /**
     * Show register form
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Handle registration
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => [
                'required', 
                'confirmed', 
                'min:8',
                'regex:/[a-z]/',      // phải có chữ thường
                'regex:/[A-Z]/',      // phải có chữ hoa
            ],
        ], [
            'name.required' => 'Vui lòng nhập họ tên',
            'name.max' => 'Họ tên không được vượt quá 255 ký tự',
            'email.required' => 'Vui lòng nhập email',
            'email.email' => 'Email phải đúng định dạng (ví dụ: example@email.com)',
            'email.unique' => 'Email này đã được sử dụng',
            'password.required' => 'Vui lòng nhập mật khẩu',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp',
            'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự và có chữ hoa, chữ thường',
            'password.regex' => 'Mật khẩu phải có ít nhất 8 ký tự và có chữ hoa, chữ thường',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'user', // Mặc định là user
        ]);

        $user->sendEmailVerificationNotification();

        return redirect()
            ->route('verification.notice', ['email' => $user->email])
            ->with('status', 'Vui lòng kiểm tra email và nhấn "Chấp nhận" để hoàn tất đăng ký.');
    }

    /**
     * Show the email verification notice and resend form.
     */
    public function showVerificationNotice(Request $request)
    {
        return view('auth.verify-email', [
            'email' => $request->query('email', old('email')),
        ]);
    }

    /**
     * Send another registration confirmation email.
     */
    public function sendVerificationEmail(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|string|email|max:255',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if ($user && ! $user->hasVerifiedEmail()) {
            $user->sendEmailVerificationNotification();
        }

        return back()
            ->withInput()
            ->with('status', 'Nếu email đang chờ xác nhận, một liên kết mới đã được gửi.');
    }

    /**
     * Complete registration after a signed email confirmation link is opened.
     */
    public function verifyEmail(Request $request, string $id, string $hash)
    {
        $user = User::findOrFail($id);

        abort_unless(
            hash_equals(sha1($user->getEmailForVerification()), $hash),
            403
        );

        if (! $user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
            event(new Verified($user));
        }

        return redirect()
            ->route('login')
            ->with('status', 'Xác nhận email thành công. Bạn có thể đăng nhập ngay bây giờ.');
    }

    /**
     * Show the forgot password form.
     */
    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    /**
     * Email a password reset link.
     */
    public function sendPasswordResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $status = Password::sendResetLink($request->only('email'));

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('status', 'Email xác nhận đổi mật khẩu đã được gửi. Vui lòng nhấn "Chấp nhận" trong email.');
        }

        return back()->withErrors([
            'email' => __($status),
        ])->onlyInput('email');
    }

    /**
     * Show the password reset form opened from an email link.
     */
    public function showResetPassword(Request $request, string $token)
    {
        return view('auth.reset-password', [
            'request' => $request,
            'token' => $token,
        ]);
    }

    /**
     * Store a new password for a valid password reset token.
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => [
                'required',
                'confirmed',
                'min:8',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
            ],
        ], [
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
            'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự và có chữ hoa, chữ thường.',
            'password.regex' => 'Mật khẩu phải có ít nhất 8 ký tự và có chữ hoa, chữ thường.',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()
                ->route('login')
                ->with('status', 'Đổi mật khẩu thành công. Vui lòng đăng nhập bằng mật khẩu mới.');
        }

        return back()->withErrors([
            'email' => __($status),
        ])->onlyInput('email');
    }

    /**
     * Handle logout
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Đăng xuất thành công!');
    }
}
