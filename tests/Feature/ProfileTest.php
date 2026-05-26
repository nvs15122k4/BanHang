<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/profile');

        $response
            ->assertOk()
            ->assertSee('profile-readonly-field', false)
            ->assertSee('Email đăng nhập chỉ có thể xem, không thể thay đổi tại thông tin cá nhân.', false);
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = User::factory()->create();
        $originalEmail = $user->email;

        $response = $this
            ->actingAs($user)
            ->put('/profile', [
                'name' => 'Test User Updated',
                'email' => 'test@example.com',
                'phone' => '0123456789',
                'gender' => 'male',
                'birthday' => '1990-01-01',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $user->refresh();

        $this->assertSame('Test User Updated', $user->name);
        $this->assertSame($originalEmail, $user->email);
        $this->assertSame('0123456789', $user->phone);
        $this->assertSame('male', $user->gender);
        $this->assertSame('1990-01-01', $user->birthday->format('Y-m-d'));
    }

    public function test_profile_email_cannot_be_changed_by_submitted_request(): void
    {
        $user = User::factory()->create([
            'email' => 'original@example.com',
            'is_active' => true,
        ]);

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->put('/profile', [
                'name' => $user->name,
                'email' => 'changed@example.com',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $this->assertSame('original@example.com', $user->fresh()->email);
    }

    public function test_user_can_add_address(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post('/profile/addresses', [
                'recipient_name' => 'Nguyen Van A',
                'phone' => '0987654321',
                'province' => 'Ho Chi Minh',
                'district' => 'District 1',
                'ward' => 'Ward 1',
                'detail' => '123 Main Street',
                'is_default' => true,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $this->assertDatabaseHas('addresses', [
            'user_id' => $user->id,
            'recipient_name' => 'Nguyen Van A',
            'phone' => '0987654321',
            'is_default' => true,
        ]);
    }

    public function test_user_can_update_address(): void
    {
        $user = User::factory()->create();
        $address = Address::factory()->create(['user_id' => $user->id]);

        $response = $this
            ->actingAs($user)
            ->put("/profile/addresses/{$address->id}", [
                'recipient_name' => 'Updated Name',
                'phone' => '0111222333',
                'province' => 'Hanoi',
                'district' => 'Ba Dinh',
                'ward' => 'Cong Vi',
                'detail' => '456 Updated Street',
                'is_default' => false,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $address->refresh();

        $this->assertSame('Updated Name', $address->recipient_name);
        $this->assertSame('0111222333', $address->phone);
    }

    public function test_user_can_delete_address(): void
    {
        $user = User::factory()->create();
        $address = Address::factory()->create(['user_id' => $user->id]);

        $response = $this
            ->actingAs($user)
            ->delete("/profile/addresses/{$address->id}");

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $this->assertDatabaseMissing('addresses', [
            'id' => $address->id,
        ]);
    }

    public function test_user_cannot_access_others_address(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $address = Address::factory()->create(['user_id' => $user2->id]);

        $response = $this
            ->actingAs($user1)
            ->delete("/profile/addresses/{$address->id}");

        $response->assertForbidden();
    }

    public function test_correct_password_must_be_provided_to_update_password(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->put('/profile/password', [
                'current_password' => 'wrong-password',
                'password' => 'NewPassword123',
                'password_confirmation' => 'NewPassword123',
            ]);

        $response->assertSessionHasErrors('current_password');
    }
}
