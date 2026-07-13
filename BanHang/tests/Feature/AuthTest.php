<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTruncation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class AuthTest extends TestCase
{
    use RefreshDatabase;
    use DatabaseTruncation;

    // TC01 - Đăng ký thành công
    public function test_dang_ky_thanh_cong()
    {
        $du_lieu = [
            'name' => 'admin',
            'email' => 'admin@gmail.com',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
        ];

        $response = $this->post('/register', $du_lieu);

        $response->assertRedirect(route('verification.notice', ['email' => 'admin@gmail.com'], false));
        $this->assertGuest();
        $this->assertDatabaseHas('users', [
            'email' => 'admin@gmail.com',
            'email_verified_at' => null,
        ]);
    }

    // TC02 - Email thiếu @
    public function test_email_khong_hop_le()
    {
        $du_lieu = [
            'name' => 'Test User',
            'email' => 'testgmail.com',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
        ];

        $response = $this->post('/register', $du_lieu);

        $response->assertSessionHasErrors('email');
    }

    // TC03 - Email sai định dạng
    public function test_email_sai_dinh_dang()
    {
        $du_lieu = [
            'name' => 'Test User',
            'email' => 'invalid-email-format',  // Email không có @ và domain
            'password' => 'Pass123456Abc',
            'password_confirmation' => 'Pass123456Abc',
        ];

        $response = $this->post('/register', $du_lieu);

        $response->assertSessionHasErrors('email');
    }

    // TC04 - Email đã tồn tại
    public function test_email_da_ton_tai()
    {
        User::factory()->create([
            'email' => 'admin@gmail.com'
        ]);

        $du_lieu = [
            'name' => 'Test User',
            'email' => 'admin@gmail.com',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
        ];

        $response = $this->post('/register', $du_lieu);

        $response->assertSessionHasErrors('email');
    }

    // TC05 - Mật khẩu không khớp
    public function test_mat_khau_khong_khop()
    {
        $du_lieu = [
            'name' => 'Test User',
            'email' => 'new@gmail.com',
            'password' => 'Password123',
            'password_confirmation' => 'DifferentPass456',
        ];

        $response = $this->post('/register', $du_lieu);

        $response->assertSessionHasErrors('password');
    }

    // TC06 - Mật khẩu yếu
    public function test_mat_khau_yeu()
    {
        $du_lieu = [
            'name' => 'admin',
            'email' => 'new@gmail.com',
            'password' => 'pass',
            'password_confirmation' => 'pass1234',
        ];

        $response = $this->post('/register', $du_lieu);

        $response->assertSessionHasErrors('password');
    }

    // TC07 - Đăng nhập thành công
    public function test_dang_nhap_thanh_cong()
    {
        $user = User::factory()->create([
            'email' => 'admin@gmail.com',
            'password' => bcrypt('Password123')
        ]);

        $du_lieu = [
            'email' => 'admin@gmail.com',
            'password' => 'Password123',
        ];

        $response = $this->post('/login', $du_lieu);

        $response->assertRedirect(route('home', absolute: false));
        $this->assertAuthenticatedAs($user);
    }

    // TC08 - Sai mật khẩu
    public function test_dang_nhap_that_bai()
    {
        User::factory()->create([
            'email' => 'admin@gmail.com',
            'password' => bcrypt('Password123')
        ]);

        $du_lieu = [
            'email' => 'admin@gmail.com',
            'password' => 'WrongPassword999',
        ];

        $response = $this->post('/login', $du_lieu);

        $response->assertSessionHasErrors();
        $this->assertGuest();
    }
}
