<?php

namespace Tests\Feature;

use App\Models\Alumno;
use App\Models\InscripcionComedor;
use App\Models\TurnoComedor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ComedorTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_cannot_have_more_than_one_comedor_enrollment(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        $alumno = $this->createAlumno('001');
        $almuerzo = TurnoComedor::create([
            'nombre' => 'Almuerzo', 'hora_inicio' => '12:00', 'hora_fin' => '13:00', 'cupo' => 20,
        ]);
        $merienda = TurnoComedor::create([
            'nombre' => 'Merienda', 'hora_inicio' => '15:00', 'hora_fin' => '16:00', 'cupo' => 20,
        ]);

        $this->post(route('admin.comedor.inscripciones.store'), [
            'alumno_id' => $alumno->id, 'turno_comedor_id' => $almuerzo->id,
        ])->assertRedirect(route('admin.comedor.index'));

        $this->post(route('admin.comedor.inscripciones.store'), [
            'alumno_id' => $alumno->id, 'turno_comedor_id' => $merienda->id,
        ])->assertSessionHasErrors('alumno_id');

        $this->assertDatabaseCount('inscripcion_comedors', 1);
    }

    public function test_comedor_turn_cannot_exceed_its_capacity(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        $turno = TurnoComedor::create([
            'nombre' => 'Almuerzo', 'hora_inicio' => '12:00', 'hora_fin' => '13:00', 'cupo' => 1,
        ]);

        $this->post(route('admin.comedor.inscripciones.store'), [
            'alumno_id' => $this->createAlumno('001')->id, 'turno_comedor_id' => $turno->id,
        ])->assertRedirect(route('admin.comedor.index'));

        $this->post(route('admin.comedor.inscripciones.store'), [
            'alumno_id' => $this->createAlumno('002')->id, 'turno_comedor_id' => $turno->id,
        ])->assertSessionHasErrors('turno_comedor_id');

        $this->assertDatabaseCount('inscripcion_comedors', 1);
    }

    public function test_comedor_enrollment_is_deactivated_instead_of_deleted(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        $inscripcion = InscripcionComedor::create([
            'alumno_id' => $this->createAlumno('001')->id,
            'turno_comedor_id' => TurnoComedor::create([
                'nombre' => 'Almuerzo', 'hora_inicio' => '12:00', 'hora_fin' => '13:00', 'cupo' => 20,
            ])->id,
        ]);

        $this->delete(route('admin.comedor.inscripciones.destroy', $inscripcion))
            ->assertRedirect(route('admin.comedor.index'));

        $this->assertDatabaseHas('inscripcion_comedors', ['id' => $inscripcion->id, 'activo' => false]);
    }

    private function createAlumno(string $suffix): Alumno
    {
        return Alumno::create([
            'legajo' => "C-{$suffix}", 'dni' => "420000{$suffix}", 'nombre' => 'Alumno',
            'apellido' => "Comedor {$suffix}", 'fecha_nacimiento' => '2012-01-10', 'domicilio' => 'Calle 1',
        ]);
    }
}
