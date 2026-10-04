<?php

namespace Tests\Feature;

use App\Models\AsignacionAcademica;
use App\Models\CicloLectivo;
use App\Models\Curso;
use App\Models\HorarioClase;
use App\Models\Materia;
use App\Models\Nivel;
use App\Models\Profesor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AcademicConfigurationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_requires_an_authenticated_administrator(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));

        $alumno = User::factory()->create(['role' => 'alumno']);
        $this->actingAs($alumno)
            ->get(route('admin.dashboard'))
            ->assertRedirect(route('login'));

        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk();
    }

    public function test_assignment_cannot_be_duplicated_and_can_be_deactivated(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $assignment = $this->createAssignment();
        $data = $assignment->only(['ciclo_lectivo_id', 'curso_id', 'materia_id', 'profesor_id']);

        $this->post(route('admin.asignaciones.store'), $data)
            ->assertSessionHasErrors('materia_id');

        $this->delete(route('admin.asignaciones.destroy', $assignment))
            ->assertRedirect(route('admin.asignaciones.index'));

        $this->assertDatabaseHas('asignacion_academicas', [
            'id' => $assignment->id,
            'activo' => false,
        ]);
    }

    public function test_schedule_rejects_overlap_for_the_same_professor_or_course(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $first = $this->createAssignment();
        $secondMateria = Materia::create(['nombre' => 'Lengua']);
        $second = AsignacionAcademica::create([
            'ciclo_lectivo_id' => $first->ciclo_lectivo_id,
            'curso_id' => $first->curso_id,
            'materia_id' => $secondMateria->id,
            'profesor_id' => $first->profesor_id,
        ]);

        HorarioClase::create([
            'asignacion_academica_id' => $first->id,
            'dia_semana' => 1,
            'hora_inicio' => '08:00',
            'hora_fin' => '09:00',
        ]);

        $this->post(route('admin.horarios.store'), [
            'asignacion_academica_id' => $second->id,
            'dia_semana' => 1,
            'hora_inicio' => '08:30',
            'hora_fin' => '09:30',
        ])->assertSessionHasErrors('hora_inicio');

        $this->assertDatabaseCount('horario_clases', 1);
    }

    private function createAssignment(): AsignacionAcademica
    {
        $ciclo = CicloLectivo::create([
            'anio' => 2026,
            'fecha_inicio' => '2026-03-01',
            'fecha_fin' => '2026-12-15',
        ]);
        $nivel = Nivel::create(['nombre' => 'Secundario']);
        $curso = Curso::create(['nivel_id' => $nivel->id, 'nombre' => '1° Año', 'division' => 'A']);
        $materia = Materia::create(['nombre' => 'Matemática']);
        $profesor = Profesor::create([
            'legajo' => 'P-001',
            'dni' => '30000000',
            'nombre' => 'Ana',
            'apellido' => 'Pérez',
            'email' => 'ana.perez@example.test',
            'telefono' => '3794000000',
            'especialidad' => 'Matemática',
        ]);

        return AsignacionAcademica::create([
            'ciclo_lectivo_id' => $ciclo->id,
            'curso_id' => $curso->id,
            'materia_id' => $materia->id,
            'profesor_id' => $profesor->id,
        ]);
    }
}
