<?php

namespace Tests\Feature;

use App\Mail\DeudaMail;
use App\Mail\FacturaMensualMail;
use App\Models\Alumno;
use App\Models\CicloLectivo;
use App\Models\Curso;
use App\Models\Deporte;
use App\Models\Factura;
use App\Models\InscripcionAcademica;
use App\Models\InscripcionComedor;
use App\Models\InscripcionDeportiva;
use App\Models\InscripcionTransporte;
use App\Models\Nivel;
use App\Models\RecorridoTransporte;
use App\Models\TurnoComedor;
use App\Models\User;
use App\Models\ValorCuota;
use App\Services\FacturacionService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * ¿Qué prueba?: la Parte 3 del backend (valores, facturas, pagos, comprobantes,
 * mails y reportes). No toca los tests anteriores.
 */
class CuotasTest extends TestCase
{
    use RefreshDatabase;

    private Alumno $hijo;
    private User $padre;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');

        $this->hijo = Alumno::create(['legajo' => 'C-1', 'dni' => '46000001', 'nombre' => 'Hijo', 'apellido' => 'Cuota',
            'fecha_nacimiento' => '2012-01-01', 'domicilio' => 'Calle']);
        $this->padre = User::factory()->create(['role' => 'padre']);
        $this->padre->hijos()->attach($this->hijo);

        // Servicios del hijo y valores fijados por el admin
        $ciclo = CicloLectivo::create(['anio' => 2026, 'fecha_inicio' => '2026-03-01', 'fecha_fin' => '2026-12-15']);
        $nivel = Nivel::create(['nombre' => 'Secundario']);
        $curso = Curso::create(['nivel_id' => $nivel->id, 'nombre' => '5° Año', 'division' => 'A', 'turno' => 'Mañana']);
        InscripcionAcademica::create(['alumno_id' => $this->hijo->id, 'ciclo_lectivo_id' => $ciclo->id, 'curso_id' => $curso->id]);
        $deporte = Deporte::create(['nombre' => 'Fútbol', 'valor_mensual' => 3000]);
        InscripcionDeportiva::create(['alumno_id' => $this->hijo->id, 'deporte_id' => $deporte->id]);
        $turno = TurnoComedor::create(['nombre' => 'Almuerzo', 'hora_inicio' => '12:00', 'hora_fin' => '13:00', 'cupo' => 30]);
        InscripcionComedor::create(['alumno_id' => $this->hijo->id, 'turno_comedor_id' => $turno->id]);
        $rec = RecorridoTransporte::create(['nombre' => 'Norte', 'zona' => 'Zona norte', 'cupo' => 30, 'valor_mensual' => 2000]);
        InscripcionTransporte::create(['alumno_id' => $this->hijo->id, 'recorrido_transporte_id' => $rec->id]);
        ValorCuota::create(['concepto' => 'cuota', 'importe' => 10000]);
        ValorCuota::create(['concepto' => 'comedor', 'importe' => 5000]);
    }

    private function factura(string $periodo = '2026-11'): Factura
    {
        return app(FacturacionService::class)->generarParaAlumno($this->hijo, $periodo);
    }

    private function token(?User $u = null): string
    {
        return ($u ?? $this->padre)->createToken('test')->plainTextToken;
    }

    public function test_factura_incluye_cuota_deporte_comedor_y_transporte(): void
    {
        $f = $this->factura();
        $this->assertCount(4, $f->items);
        $this->assertEquals(20000, (float) $f->total);
        $this->assertEquals('2026-11-20', $f->fecha_vencimiento->toDateString());
        // generar de nuevo no duplica
        $this->factura();
        $this->assertSame(1, Factura::count());
    }

    public function test_ultimo_dia_habil_cae_en_viernes_si_el_mes_termina_en_fin_de_semana(): void
    {
        $s = app(FacturacionService::class);
        $this->assertSame('2026-10-30', $s->ultimoDiaHabil(Carbon::parse('2026-10-06'))->toDateString()); // 31/10 es sábado
        $this->assertSame('2026-11-30', $s->ultimoDiaHabil(Carbon::parse('2026-11-10'))->toDateString()); // lunes
    }

    public function test_pago_parcial_y_total_con_comprobante(): void
    {
        $f = $this->factura();
        $token = $this->token();
        $ids = $f->items->pluck('id');

        // sin comprobante no se puede pagar (solo transferencia)
        $this->withToken($token)->postJson("/api/v1/facturas/{$f->id}/pagar", ['items' => [$ids[0]]])->assertStatus(422);

        // sube comprobante y paga solo un ítem
        $this->withToken($token)->postJson("/api/v1/facturas/{$f->id}/comprobantes", ['archivo' => UploadedFile::fake()->create('t.jpg', 10, 'image/jpeg')])->assertCreated();
        $this->withToken($token)->postJson("/api/v1/facturas/{$f->id}/pagar", ['items' => [$ids[0]]])
            ->assertCreated()->assertJsonPath('factura_estado', 'parcial');

        // una factura puede tener varios comprobantes
        $this->withToken($token)->postJson("/api/v1/facturas/{$f->id}/comprobantes", ['archivo' => UploadedFile::fake()->create('t2.pdf', 10, 'application/pdf')])->assertCreated();
        $this->assertSame(2, $f->comprobantes()->count());

        // no se puede pagar dos veces el mismo ítem
        $this->withToken($token)->postJson("/api/v1/facturas/{$f->id}/pagar", ['items' => [$ids[0]]])->assertStatus(422);

        // paga el resto
        $this->withToken($token)->postJson("/api/v1/facturas/{$f->id}/pagar", ['items' => [$ids[1], $ids[2], $ids[3]]])
            ->assertCreated()->assertJsonPath('factura_estado', 'pagada');

        $this->withToken($token)->getJson("/api/v1/hijos/{$this->hijo->id}/deuda")->assertOk()->assertJsonPath('deuda_total', 0);
        $this->withToken($token)->getJson("/api/v1/hijos/{$this->hijo->id}/comprobantes")->assertOk()->assertJsonCount(2, 'pagos');
    }

    public function test_listas_de_facturas_deuda_y_pdfs(): void
    {
        $f = $this->factura();
        $token = $this->token();

        $this->withToken($token)->getJson("/api/v1/hijos/{$this->hijo->id}/facturas?desde=2000-01-01&hasta=2100-01-01")
            ->assertOk()->assertJsonCount(1, 'facturas')->assertJsonPath('facturas.0.total', 20000);
        $this->withToken($token)->getJson("/api/v1/hijos/{$this->hijo->id}/facturas?desde=2100-01-01")->assertOk()->assertJsonCount(0, 'facturas');
        $this->withToken($token)->getJson("/api/v1/hijos/{$this->hijo->id}/deuda")
            ->assertOk()->assertJsonPath('deuda_total', 20000)->assertJsonCount(4, 'historial');
        $this->withToken($token)->get("/api/v1/facturas/{$f->id}/pdf")->assertOk()->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_un_padre_no_ve_facturas_de_hijos_ajenos_ni_un_no_padre(): void
    {
        $f = $this->factura();
        $otro = User::factory()->create(['role' => 'padre']);
        $this->withToken($this->token($otro))->getJson("/api/v1/facturas/{$f->id}")->assertNotFound();
        $this->withToken($this->token($otro))->getJson("/api/v1/hijos/{$this->hijo->id}/deuda")->assertNotFound();

        // Sanctum recuerda al usuario entre pedidos dentro de un mismo test: se reinicia
        $this->app['auth']->forgetGuards();
        $profe = User::factory()->create(['role' => 'profesor']);
        $this->withToken($this->token($profe))->getJson("/api/v1/facturas/{$f->id}")->assertForbidden();
        $this->app['auth']->forgetGuards();
        $this->flushHeaders();
        $this->getJson("/api/v1/facturas/{$f->id}")->assertUnauthorized();
    }

    public function test_cambiar_password_desde_la_api(): void
    {
        $user = User::factory()->create(['role' => 'padre', 'password' => 'Padre1234']);
        $token = $this->token($user);
        $this->withToken($token)->postJson('/api/v1/cambiar-password', ['password_actual' => 'mala', 'password' => 'Nueva1234', 'password_confirmation' => 'Nueva1234'])->assertStatus(422);
        $this->withToken($token)->postJson('/api/v1/cambiar-password', ['password_actual' => 'Padre1234', 'password' => 'Nueva1234', 'password_confirmation' => 'Nueva1234'])->assertOk();
        $this->postJson('/api/v1/login', ['email' => $user->email, 'password' => 'Nueva1234'])->assertOk();
    }

    public function test_comando_de_fin_de_mes_manda_mail_con_pdf_adjunto(): void
    {
        Mail::fake();
        $this->artisan('facturas:recordatorio --forzar')->assertSuccessful();
        Mail::assertSent(FacturaMensualMail::class, fn ($m) => $m->hasTo($this->padre->email) && count($m->attachments()) === 1);
    }

    public function test_comando_dia_20_avisa_solo_a_quien_debe(): void
    {
        Mail::fake();
        $periodo = now()->format('Y-m');
        $f = $this->factura($periodo);
        $this->artisan('facturas:deuda --forzar')->assertSuccessful();
        Mail::assertSent(DeudaMail::class, 1);

        // si paga todo, ya no recibe el aviso
        Mail::fake();
        $this->padre->createToken('x');
        app(FacturacionService::class)->pagar($f, $this->padre->id, $f->items->pluck('id')->all());
        $this->artisan('facturas:deuda --forzar')->assertSuccessful();
        Mail::assertNothingSent();
    }

    public function test_admin_fija_valores_y_ve_reportes_pero_el_padre_no(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $deporte = Deporte::first();
        $rec = RecorridoTransporte::first();

        $this->actingAs($admin)->post(route('admin.cuotas.valores'), [
            'cuota' => 12000, 'comedor' => 6000,
            'deportes' => [$deporte->id => ['valor_mensual' => 3500, 'profesor_id' => null]],
            'recorridos' => [$rec->id => ['valor_mensual' => 2500]],
        ])->assertRedirect();
        $this->assertEquals(12000, ValorCuota::de('cuota'));
        $this->assertEquals(3500, (float) $deporte->fresh()->valor_mensual);

        $this->actingAs($admin)->post(route('admin.cuotas.generar'), ['periodo' => '2026-12'])->assertRedirect();
        $this->assertSame(1, Factura::where('periodo', '2026-12')->count());

        $this->actingAs($admin)->get(route('admin.cuotas.index'))->assertOk();
        $this->actingAs($admin)->get(route('admin.reportes.pagos', ['anio' => 2026]))->assertOk()->assertSee('Ingresos por período');

        // el middleware de roles del proyecto redirige a quien no es admin
        $this->actingAs($this->padre)->get(route('admin.cuotas.index'))->assertRedirect();
        $this->actingAs($this->padre)->post(route('admin.cuotas.valores'), ['cuota' => 1, 'comedor' => 1])->assertRedirect();
        $this->actingAs($this->padre)->get(route('admin.reportes.pagos'))->assertRedirect();
        $this->assertEquals(12000, ValorCuota::de('cuota'));
    }

    public function test_reporte_de_pagos_muestra_lo_cobrado_por_deporte_y_recorrido(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $f = $this->factura(now()->format('Y-m'));
        app(FacturacionService::class)->pagar($f, $this->padre->id, $f->items->pluck('id')->all());

        $this->actingAs($admin)->get(route('admin.reportes.pagos'))->assertOk()
            ->assertSee('Fútbol')->assertSee('Norte')->assertSee('Secundario')->assertSee('20.000,00');
    }
}
