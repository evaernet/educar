<?php

namespace App\Services;

use App\Models\Alumno;
use App\Models\Factura;
use App\Models\FacturaItem;
use App\Models\Pago;
use App\Models\ValorCuota;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * ¿Qué hace?: Toda la lógica de facturación en un solo lugar:
 * generar facturas, registrar pagos y calcular fechas (último día hábil, vencimiento).
 */
class FacturacionService
{
    /** Día del mes en que vence la factura (y se manda el mail de deuda). */
    public const DIA_VENCIMIENTO = 20;

    /**
     * Genera la factura de un alumno para un periodo ("2026-11").
     * Si ya existe la devuelve sin tocarla. Si no tiene nada para cobrar, devuelve null.
     */
    public function generarParaAlumno(Alumno $alumno, string $periodo): ?Factura
    {
        $existente = Factura::where('alumno_id', $alumno->id)->where('periodo', $periodo)->first();
        if ($existente) {
            return $existente;
        }

        // PASO 1: armar los renglones según lo que el alumno tiene hoy activo
        $items = [];

        $cuota = ValorCuota::de('cuota');
        if ($cuota > 0 && $alumno->inscripciones()->where('activo', true)->exists()) {
            $items[] = ['tipo' => 'cuota', 'referencia_id' => null, 'descripcion' => 'Cuota mensual', 'importe' => $cuota];
        }

        foreach ($alumno->inscripcionesDeportivas()->where('activo', true)->with('deporte')->get() as $insc) {
            if ($insc->deporte && $insc->deporte->valor_mensual > 0) {
                $items[] = ['tipo' => 'deporte', 'referencia_id' => $insc->deporte_id,
                    'descripcion' => 'Deporte: ' . $insc->deporte->nombre, 'importe' => $insc->deporte->valor_mensual];
            }
        }

        $comedor = ValorCuota::de('comedor');
        if ($comedor > 0 && $alumno->inscripcionesComedor()->where('activo', true)->exists()) {
            $items[] = ['tipo' => 'comedor', 'referencia_id' => null, 'descripcion' => 'Comedor', 'importe' => $comedor];
        }

        foreach ($alumno->inscripcionesTransporte()->where('activo', true)->with('recorrido')->get() as $insc) {
            if ($insc->recorrido && $insc->recorrido->valor_mensual > 0) {
                $items[] = ['tipo' => 'transporte', 'referencia_id' => $insc->recorrido_transporte_id,
                    'descripcion' => 'Transporte: ' . $insc->recorrido->nombre, 'importe' => $insc->recorrido->valor_mensual];
            }
        }

        if (empty($items)) {
            return null;
        }

        // PASO 2: guardar factura + ítems juntos (si algo falla no queda nada a medias)
        return DB::transaction(function () use ($alumno, $periodo, $items) {
            $factura = Factura::create([
                'alumno_id' => $alumno->id,
                'periodo' => $periodo,
                'fecha_emision' => Carbon::today(),
                'fecha_vencimiento' => $this->vencimientoDe($periodo),
                'total' => collect($items)->sum('importe'),
                'estado' => 'pendiente',
            ]);
            foreach ($items as $item) {
                $factura->items()->create($item);
            }
            return $factura;
        });
    }

    /** Genera las facturas de todos los alumnos activos. Devuelve cuántas se crearon. */
    public function generarParaTodos(string $periodo): int
    {
        $creadas = 0;
        foreach (Alumno::where('activo', true)->get() as $alumno) {
            $yaTenia = Factura::where('alumno_id', $alumno->id)->where('periodo', $periodo)->exists();
            $factura = $this->generarParaAlumno($alumno, $periodo);
            if ($factura && !$yaTenia) {
                $creadas++;
            }
        }
        return $creadas;
    }

    /**
     * Registra el pago de los ítems elegidos y emite el comprobante de pago.
     *
     * @param  array<int>  $itemIds
     */
    public function pagar(Factura $factura, int $userId, array $itemIds): Pago
    {
        $items = $factura->items()->whereIn('id', $itemIds)->whereNull('pago_id')->get();

        if ($items->isEmpty() || $items->count() !== count(array_unique($itemIds))) {
            throw ValidationException::withMessages(['items' => 'Elegí ítems pendientes de esta factura.']);
        }

        return DB::transaction(function () use ($factura, $userId, $items) {
            $pago = Pago::create([
                'factura_id' => $factura->id,
                'user_id' => $userId,
                'numero' => 'TMP',
                'total' => $items->sum('importe'),
                'fecha_pago' => Carbon::today(),
                'medio' => 'transferencia',
            ]);
            $pago->update(['numero' => 'CP-' . str_pad((string) $pago->id, 6, '0', STR_PAD_LEFT)]);

            FacturaItem::whereIn('id', $items->pluck('id'))->update(['pago_id' => $pago->id, 'pagado_at' => now()]);
            $factura->recalcularEstado();
            return $pago;
        });
    }

    /** Último día hábil (lunes a viernes) del mes de la fecha dada. */
    public function ultimoDiaHabil(Carbon $fecha): Carbon
    {
        $dia = $fecha->copy()->endOfMonth()->startOfDay();
        while ($dia->isWeekend()) {
            $dia->subDay();
        }
        return $dia;
    }

    /** Vence el día 20 del mes del periodo. */
    public function vencimientoDe(string $periodo): Carbon
    {
        return Carbon::createFromFormat('Y-m-d', $periodo . '-' . self::DIA_VENCIMIENTO)->startOfDay();
    }

    /** Periodo del mes que viene respecto de una fecha (la factura se emite a fin de mes). */
    public function periodoSiguiente(Carbon $fecha): string
    {
        return $fecha->copy()->startOfMonth()->addMonth()->format('Y-m');
    }
}
