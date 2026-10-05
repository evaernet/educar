<?php

namespace Tests\Feature;

use App\Models\Alumno;
use App\Models\AsignacionAcademica;
use App\Models\CicloLectivo;
use App\Models\Curso;
use App\Models\Deporte;
use App\Models\HorarioClase;
use App\Models\HorarioDeporte;
use App\Models\InscripcionAcademica;
use App\Models\InscripcionComedor;
use App\Models\InscripcionDeportiva;
use App\Models\InscripcionTransporte;
use App\Models\Materia;
use App\Models\Nivel;
use App\Models\Profesor;
use App\Models\RecorridoTransporte;
use App\Models\TurnoComedor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_login_returns_token_and_protects_profile(): void
    {
        $user = User::factory()->create(['role' => 'padre', 'password' => 'Padre1234']);
        $this->getJson('/api/v1/me')->assertUnauthorized();
        $login = $this->postJson('/api/v1/login', ['email' => $user->email, 'password' => 'Padre1234'])->assertOk()->json();
        $this->withToken($login['token'])->getJson('/api/v1/me')->assertOk()->assertJsonPath('email', $user->email);
    }

    public function test_inactive_account_cannot_get_an_api_token(): void
    {
        $user = User::factory()->create(['role' => 'padre', 'password' => 'Padre1234', 'activo' => false]);
        $this->postJson('/api/v1/login', ['email' => $user->email, 'password' => 'Padre1234'])
            ->assertUnprocessable()->assertJsonPath('message', 'Credenciales inválidas.');
    }

    public function test_parent_api_profile_only_returns_linked_children(): void
    {
        $padre = User::factory()->create(['role' => 'padre']);
        $hijo = $this->alumno('API-1', '45000001', 'Hijo', 'Propio');
        $ajeno = $this->alumno('API-2', '45000002', 'Alumno', 'Ajeno');
        $padre->hijos()->attach($hijo);
        $token = $padre->createToken('test')->plainTextToken;
        $this->withToken($token)->getJson('/api/v1/me')->assertJsonFragment(['nombre' => 'Hijo'])->assertJsonMissing(['nombre' => 'Alumno']);
    }

    public function test_parent_can_view_services_of_own_child_only(): void
    {
        $padre = User::factory()->create(['role' => 'padre']);
        $hijo = $this->alumno('API-3', '45000003', 'Hijo', 'Servicios');
        $ajeno = $this->alumno('API-4', '45000004', 'Alumno', 'Ajeno');
        $padre->hijos()->attach($hijo);
        $this->createServicesFor($hijo);
        $token = $padre->createToken('test')->plainTextToken;

        $this->withToken($token)->getJson('/api/v1/hijos')->assertOk()->assertJsonPath('hijos.0.nombre', 'Hijo');
        $this->withToken($token)->getJson("/api/v1/hijos/{$hijo->id}")->assertOk()
            ->assertJsonPath('alumno.legajo', 'API-3')->assertJsonPath('academico.0.curso', '5° Año')
            ->assertJsonPath('horarios.0.materia', 'Matemática')->assertJsonPath('horarios.0.dia', 'Lunes')
            ->assertJsonPath('deportes.0.nombre', 'Fútbol')->assertJsonPath('comedor.turno', 'Almuerzo')
            ->assertJsonPath('transporte.recorrido', 'Norte');
        $this->withToken($token)->getJson("/api/v1/hijos/{$ajeno->id}")->assertNotFound()
            ->assertJsonPath('message', 'No tenés acceso a la información de este alumno.');
    }

    public function test_non_parent_cannot_list_children_in_api(): void
    {
        $alumno = User::factory()->create(['role' => 'alumno']);
        $token = $alumno->createToken('test')->plainTextToken;
        $this->withToken($token)->getJson('/api/v1/hijos')->assertForbidden()
            ->assertJsonPath('message', 'Este recurso está disponible solo para padres o tutores.');
    }

    private function alumno(string $legajo, string $dni, string $nombre, string $apellido): Alumno
    {
        return Alumno::create(['legajo' => $legajo, 'dni' => $dni, 'nombre' => $nombre, 'apellido' => $apellido, 'fecha_nacimiento' => '2012-01-01', 'domicilio' => 'Calle']);
    }

    private function createServicesFor(Alumno $alumno): void
    {
        $ciclo = CicloLectivo::create(['anio' => 2026, 'fecha_inicio' => '2026-03-01', 'fecha_fin' => '2026-12-15']);
        $nivel = Nivel::create(['nombre' => 'Secundario']);
        $curso = Curso::create(['nivel_id' => $nivel->id, 'nombre' => '5° Año', 'division' => 'A', 'turno' => 'Mañana']);
        $materia = Materia::create(['nombre' => 'Matemática']);
        $profesor = Profesor::create(['legajo' => 'P-API', 'dni' => '30111222', 'nombre' => 'Ana', 'apellido' => 'Docente', 'especialidad' => 'Matemática']);
        $asignacion = AsignacionAcademica::create(['ciclo_lectivo_id' => $ciclo->id, 'curso_id' => $curso->id, 'materia_id' => $materia->id, 'profesor_id' => $profesor->id]);
        HorarioClase::create(['asignacion_academica_id' => $asignacion->id, 'dia_semana' => 1, 'hora_inicio' => '08:00', 'hora_fin' => '09:00']);
        InscripcionAcademica::create(['alumno_id' => $alumno->id, 'ciclo_lectivo_id' => $ciclo->id, 'curso_id' => $curso->id]);
        $deporte = Deporte::create(['nombre' => 'Fútbol']);
        HorarioDeporte::create(['deporte_id' => $deporte->id, 'dia_semana' => 3, 'hora_inicio' => '15:00', 'hora_fin' => '16:00']);
        InscripcionDeportiva::create(['alumno_id' => $alumno->id, 'deporte_id' => $deporte->id]);
        $turno = TurnoComedor::create(['nombre' => 'Almuerzo', 'hora_inicio' => '12:00', 'hora_fin' => '13:00', 'cupo' => 30]);
        InscripcionComedor::create(['alumno_id' => $alumno->id, 'turno_comedor_id' => $turno->id]);
        $recorrido = RecorridoTransporte::create(['nombre' => 'Norte', 'zona' => 'Zona norte', 'cupo' => 30]);
        InscripcionTransporte::create(['alumno_id' => $alumno->id, 'recorrido_transporte_id' => $recorrido->id]);
    }
}
