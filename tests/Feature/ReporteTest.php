<?php
namespace Tests\Feature;
use App\Models\User; use Illuminate\Foundation\Testing\RefreshDatabase; use Tests\TestCase;
class ReporteTest extends TestCase { use RefreshDatabase; public function test_reports_require_an_administrator(): void { $this->get(route('admin.reportes.index'))->assertRedirect(route('login')); $this->actingAs(User::factory()->create(['role'=>'alumno']))->get(route('admin.reportes.index'))->assertRedirect(route('login')); $this->actingAs(User::factory()->create(['role'=>'admin']))->get(route('admin.reportes.index'))->assertOk()->assertSee('Reportes'); } }
