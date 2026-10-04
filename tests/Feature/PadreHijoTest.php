<?php
namespace Tests\Feature;
use App\Models\Alumno; use App\Models\User; use Illuminate\Foundation\Testing\RefreshDatabase; use Tests\TestCase;
class PadreHijoTest extends TestCase { use RefreshDatabase;
 public function test_parent_only_sees_linked_children(): void { $padre=User::factory()->create(['role'=>'padre','name'=>'Responsable Uno']); $otro=User::factory()->create(['role'=>'padre']); $hijo=$this->alumno('001','Hijo Propio'); $ajeno=$this->alumno('002','Alumno Ajeno'); $padre->hijos()->attach($hijo); $otro->hijos()->attach($ajeno); $this->actingAs($padre)->get(route('padre.dashboard'))->assertOk()->assertSee('Hijo Propio')->assertDontSee('Alumno Ajeno'); }
 public function test_admin_can_create_family_link(): void { $this->actingAs(User::factory()->create(['role'=>'admin'])); $padre=User::factory()->create(['role'=>'padre']); $hijo=$this->alumno('001','Hijo'); $this->post(route('admin.familias.store'),['padre_id'=>$padre->id,'alumno_id'=>$hijo->id])->assertRedirect(route('admin.familias.index')); $this->assertDatabaseHas('alumno_padre',['padre_id'=>$padre->id,'alumno_id'=>$hijo->id]); }
 private function alumno(string $id,string $nombre): Alumno { return Alumno::create(['legajo'=>"P-$id",'dni'=>"440000$id",'nombre'=>$nombre,'apellido'=>'Familia','fecha_nacimiento'=>'2012-01-10','domicilio'=>'Calle 1']); }
}
