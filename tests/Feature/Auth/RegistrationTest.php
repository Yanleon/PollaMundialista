<?php

namespace Tests\Feature\Auth;

use App\Models\AppSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone_number' => '+57 300 123 4567',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_only_allowed_emails_can_register_when_list_is_configured(): void
    {
        AppSetting::setValue('allowed_registration_emails', "autorizado@empresa.com\notro@empresa.com");

        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'externo@example.com',
            'phone_number' => '+57 300 123 4567',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();

        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'autorizado@empresa.com',
            'phone_number' => '+57 300 123 4567',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_only_allowed_domains_can_register_when_domain_is_configured(): void
    {
        AppSetting::setValue('allowed_registration_domains', '@empresa.com.co');

        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'externo@example.com',
            'phone_number' => '+57 300 123 4567',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();

        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'persona@empresa.com.co',
            'phone_number' => '+57 300 123 4567',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }
}
