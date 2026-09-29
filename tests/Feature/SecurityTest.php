<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_logout_requires_a_post_request_and_ends_the_session(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        $this->actingAs($user)
            ->get(route('logout'))
            ->assertStatus(405);

        $this->post(route('logout'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function test_registration_requires_a_stronger_password(): void
    {
        $this->post(route('register.procesar'), [
            'name' => 'Cuenta de prueba',
            'email' => 'cuenta@example.test',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'alumno',
        ])->assertSessionHasErrors('password');

        $this->assertDatabaseMissing('users', ['email' => 'cuenta@example.test']);
    }

    public function test_login_is_rate_limited_after_five_failed_attempts(): void
    {
        $credentials = [
            'email' => 'intentos@example.test',
            'password' => 'contrasena-invalida',
        ];

        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->post(route('login.procesar'), $credentials)
                ->assertSessionHasErrors('email');
        }

        $this->post(route('login.procesar'), $credentials)
            ->assertStatus(429);
    }
}
