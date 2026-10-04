<?php

namespace Tests\Feature;

use App\Models\Alumno;
use App\Models\CicloLectivo;
use App\Models\Curso;
use App\Models\InscripcionAcademica;
use App\Models\Nivel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AcademicEnrollmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_enroll_a_student_once_per_school_year(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        [$alumno, $ciclo, $primerCurso, $segundoCurso] = $this->createEnrollmentData();

        $data = [
            'alumno_id' => $alumno->id,
            'ciclo_lectivo_id' => $ciclo->id,
            'curso_id' => $primerCurso->id,
        ];

        $this->post(route('admin.inscripciones.store'), $data)
            ->assertRedirect(route('admin.inscripciones.index'));

        $this->assertDatabaseHas('inscripcion_academicas', $data + ['activo' => true]);

        $this->post(route('admin.inscripciones.store'), array_merge($data, ['curso_id' => $segundoCurso->id]))
            ->assertSessionHasErrors('alumno_id');

        $this->assertDatabaseCount('inscripcion_academicas', 1);
    }

    public function test_enrollment_can_be_updated_and_deactivated(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        [$alumno, $ciclo, $primerCurso, $segundoCurso] = $this->createEnrollmentData();
        $inscripcion = InscripcionAcademica::create([
            'alumno_id' => $alumno->id,
            'ciclo_lectivo_id' => $ciclo->id,
            'curso_id' => $primerCurso->id,
        ]);

        $this->put(route('admin.inscripciones.update', $inscripcion), [
            'alumno_id' => $alumno->id,
            'ciclo_lectivo_id' => $ciclo->id,
            'curso_id' => $segundoCurso->id,
        ])->assertRedirect(route('admin.inscripciones.index'));

        $this->delete(route('admin.inscripciones.destroy', $inscripcion))
            ->assertRedirect(route('admin.inscripciones.index'));

        $this->assertDatabaseHas('inscripcion_academicas', [
            'id' => $inscripcion->id,
            'curso_id' => $segundoCurso->id,
            'activo' => false,
        ]);
    }

    private function createEnrollmentData(): array
    {
        $alumno = Alumno::create([
            'legajo' => 'A-001', 'dni' => '40000000', 'nombre' => 'Lucía', 'apellido' => 'Gómez',
            'fecha_nacimiento' => '2012-05-10', 'domicilio' => 'Calle 1',
        ]);
        $ciclo = CicloLectivo::create([
            'anio' => 2026, 'fecha_inicio' => '2026-03-01', 'fecha_fin' => '2026-12-15',
        ]);
        $nivel = Nivel::create(['nombre' => 'Primario']);
        $primerCurso = Curso::create(['nivel_id' => $nivel->id, 'nombre' => '5° Año', 'division' => 'A']);
        $segundoCurso = Curso::create(['nivel_id' => $nivel->id, 'nombre' => '5° Año', 'division' => 'B']);

        return [$alumno, $ciclo, $primerCurso, $segundoCurso];
    }
}
