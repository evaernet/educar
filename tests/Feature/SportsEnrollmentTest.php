<?php

namespace Tests\Feature;

use App\Models\Alumno;
use App\Models\Deporte;
use App\Models\HorarioDeporte;
use App\Models\InscripcionDeportiva;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SportsEnrollmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_cannot_have_more_than_two_active_sports(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        $alumno = $this->createAlumno();
        $deportes = Deporte::factory()->count(3)->sequence(
            ['nombre' => 'Fútbol'], ['nombre' => 'Vóley'], ['nombre' => 'Ajedrez']
        )->create();

        foreach ($deportes->take(2) as $deporte) {
            $this->post(route('admin.deportes.inscripciones.store'), ['alumno_id' => $alumno->id, 'deporte_id' => $deporte->id])
                ->assertRedirect(route('admin.deportes.inscripciones.index'));
        }

        $this->post(route('admin.deportes.inscripciones.store'), ['alumno_id' => $alumno->id, 'deporte_id' => $deportes[2]->id])
            ->assertSessionHasErrors('alumno_id');

        $this->assertDatabaseCount('inscripcion_deportivas', 2);
    }

    public function test_student_cannot_enroll_in_sports_with_overlapping_schedules(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        $alumno = $this->createAlumno();
        $futbol = Deporte::factory()->create(['nombre' => 'Fútbol']);
        $voley = Deporte::factory()->create(['nombre' => 'Vóley']);
        HorarioDeporte::create(['deporte_id' => $futbol->id, 'dia_semana' => 1, 'hora_inicio' => '16:00', 'hora_fin' => '17:00']);
        HorarioDeporte::create(['deporte_id' => $voley->id, 'dia_semana' => 1, 'hora_inicio' => '16:30', 'hora_fin' => '17:30']);

        InscripcionDeportiva::create(['alumno_id' => $alumno->id, 'deporte_id' => $futbol->id]);

        $this->post(route('admin.deportes.inscripciones.store'), ['alumno_id' => $alumno->id, 'deporte_id' => $voley->id])
            ->assertSessionHasErrors('deporte_id');

        $this->assertDatabaseCount('inscripcion_deportivas', 1);
    }

    private function createAlumno(): Alumno
    {
        return Alumno::create([
            'legajo' => 'D-001', 'dni' => '41000000', 'nombre' => 'Mateo', 'apellido' => 'López',
            'fecha_nacimiento' => '2012-01-10', 'domicilio' => 'Calle 2',
        ]);
    }
}
