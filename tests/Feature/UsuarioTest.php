<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Profesor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UsuarioTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_cannot_deactivate_own_account(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->patch(route('admin.usuarios.estado', $admin))
            ->assertSessionHasErrors('usuario');

        $this->assertTrue($admin->fresh()->activo);
    }

    public function test_inactive_user_cannot_login(): void
    {
        $user = User::factory()->create([
            'role' => 'padre',
            'password' => 'Padre1234',
            'activo' => false,
        ]);

        $this->post(route('login.procesar'), [
            'email' => $user->email,
            'password' => 'Padre1234',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_admin_can_create_user(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']))
            ->post(route('admin.usuarios.store'), [
                'name' => 'Nuevo Padre',
                'email' => 'nuevo.padre@educar.com',
                'role' => 'padre',
                'password' => 'Padre1234',
                'password_confirmation' => 'Padre1234',
            ])
            ->assertRedirect(route('admin.usuarios.index'));

        $this->assertDatabaseHas('users', [
            'email' => 'nuevo.padre@educar.com',
            'role' => 'padre',
            'activo' => true,
        ]);
    }

    public function test_user_administration_does_not_create_academic_roles(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->post(route('admin.usuarios.store'), [
                'name' => 'Alumno Incorrecto',
                'email' => 'alumno.incorrecto@educar.com',
                'role' => 'alumno',
                'password' => 'Alumno123',
                'password_confirmation' => 'Alumno123',
            ])
            ->assertSessionHasErrors('role');

        $this->assertDatabaseMissing('users', ['email' => 'alumno.incorrecto@educar.com']);
    }

    public function test_registering_professor_creates_a_linked_access_account(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->post(route('admin.profesores.store'), [
                'legajo' => 'P-100',
                'dni' => '30123456',
                'nombre' => 'Lucía',
                'apellido' => 'Gómez',
                'especialidad' => 'Historia',
                'email' => 'lucia.gomez@example.test',
                'telefono' => '3794000000',
            ])
            ->assertRedirect(route('admin.profesores.index'));

        $profesor = Profesor::where('legajo', 'P-100')->firstOrFail();
        $this->assertNotNull($profesor->user_id);
        $this->assertDatabaseHas('users', [
            'id' => $profesor->user_id,
            'email' => 'P-100@profesor.educar',
            'role' => 'profesor',
        ]);
    }

    public function test_professor_dni_must_contain_seven_or_eight_digits_only(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->post(route('admin.profesores.store'), [
                'legajo' => 'P-101',
                'dni' => '30.123.456',
                'nombre' => 'DNI',
                'apellido' => 'Inválido',
                'especialidad' => 'Historia',
            ])
            ->assertSessionHasErrors([
                'dni' => 'El formato de DNI no es válido.',
            ]);

        $this->assertDatabaseMissing('profesores', ['legajo' => 'P-101']);
    }

    public function test_admin_sees_registered_accounts(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'name' => 'Administrador']);
        $account = User::factory()->create([
            'role' => 'padre',
            'name' => 'Cuenta Visible',
            'email' => 'visible@educar.com',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.usuarios.index'))
            ->assertOk()
            ->assertSee('Cuentas registradas')
            ->assertSee('Cuenta Visible')
            ->assertSee($account->email);
    }
}
