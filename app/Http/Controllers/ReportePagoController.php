<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Models\Deporte;
use App\Models\Factura;
use App\Models\FacturaItem;
use App\Models\Nivel;
use App\Models\Pago;
use App\Models\Profesor;
use App\Models\RecorridoTransporte;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * ¿Qué hace?: Reportes de pagos para el administrador:
 * ingresos por período, pagos completos e incompletos, pagos por deporte
 * (con nivel / horario / profesor) y pagos por recorrido de transporte.
 * Filtros (todos opcionales): desde, hasta, anio, alumno_id, deporte_id, nivel_id,
 * profesor_id, dia_semana, recorrido_id.
 */
class ReportePagoController extends Controller
{
    public const DIAS = [1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado', 7 => 'Domingo'];

    public function index(Request $request)
    {
        $f = $request->validate([
            'desde' => 'nullable|date', 'hasta' => 'nullable|date|after_or_equal:desde',
            'anio' => 'nullable|integer|min:2000|max:2100',
            'alumno_id' => 'nullable|exists:alumnos,id', 'deporte_id' => 'nullable|exists:deportes,id',
            'nivel_id' => 'nullable|exists:niveles,id', 'profesor_id' => 'nullable|exists:profesores,id',
            'dia_semana' => 'nullable|integer|min:1|max:7', 'recorrido_id' => 'nullable|exists:recorridos_transporte,id',
        ]);

        // PASO 1: rango de fechas (si solo hay año, es todo ese año)
        $desde = $f['desde'] ?? (isset($f['anio']) ? $f['anio'] . '-01-01' : null);
        $hasta = $f['hasta'] ?? (isset($f['anio']) ? $f['anio'] . '-12-31' : null);
        $alumnoId = $f['alumno_id'] ?? null;

        // PASO 2: ingresos por mes (según fecha de pago)
        $pagos = Pago::query()
            ->when($desde, fn ($q) => $q->whereDate('fecha_pago', '>=', $desde))
            ->when($hasta, fn ($q) => $q->whereDate('fecha_pago', '<=', $hasta))
            ->when($alumnoId, fn ($q) => $q->whereHas('factura', fn ($x) => $x->where('alumno_id', $alumnoId)))
            ->get();
        $ingresos = $pagos->groupBy(fn ($p) => $p->fecha_pago->format('Y-m'))->sortKeys()
            ->map(fn ($g, $mes) => ['mes' => $mes, 'cantidad' => $g->count(), 'total' => (float) $g->sum('total')])->values();

        // PASO 3: facturas completas e incompletas (según fecha de emisión)
        $facturas = Factura::with('alumno', 'items')
            ->when($desde, fn ($q) => $q->whereDate('fecha_emision', '>=', $desde))
            ->when($hasta, fn ($q) => $q->whereDate('fecha_emision', '<=', $hasta))
            ->when($alumnoId, fn ($q) => $q->where('alumno_id', $alumnoId))
            ->orderByDesc('periodo')->get();
        $completas = $facturas->where('estado', 'pagada')->values();
        $incompletas = $facturas->where('estado', '!=', 'pagada')->values();

        // PASO 4: ítems pagados de deporte y de transporte
        $pagados = fn (string $tipo) => FacturaItem::with('factura.alumno.inscripciones.curso.nivel')
            ->where('tipo', $tipo)->whereNotNull('pago_id')
            ->when($desde, fn ($q) => $q->whereDate('pagado_at', '>=', $desde))
            ->when($hasta, fn ($q) => $q->whereDate('pagado_at', '<=', $hasta))
            ->when($alumnoId, fn ($q) => $q->whereHas('factura', fn ($x) => $x->where('alumno_id', $alumnoId)));

        $porDeporte = $pagados('deporte')->with('deporte.profesor', 'deporte.horarios')
            ->when($f['deporte_id'] ?? null, fn ($q, $v) => $q->where('referencia_id', $v))
            ->when($f['profesor_id'] ?? null, fn ($q, $v) => $q->whereHas('deporte', fn ($x) => $x->where('profesor_id', $v)))
            ->when($f['dia_semana'] ?? null, fn ($q, $v) => $q->whereHas('deporte.horarios', fn ($x) => $x->where('dia_semana', $v)))
            ->when($f['nivel_id'] ?? null, fn ($q, $v) => $q->whereHas('factura.alumno.inscripciones.curso', fn ($x) => $x->where('nivel_id', $v)))
            ->get();

        $porRecorrido = $pagados('transporte')->with('recorrido')
            ->when($f['recorrido_id'] ?? null, fn ($q, $v) => $q->where('referencia_id', $v))
            ->get();

        return view('reportes.pagos', [
            'f' => $f,
            'ingresos' => $ingresos, 'totalIngresos' => (float) $pagos->sum('total'),
            'completas' => $completas, 'incompletas' => $incompletas,
            'porDeporte' => $porDeporte,
            'porRecorrido' => $porRecorrido->groupBy('referencia_id')->map(fn ($g) => [
                'recorrido' => $g->first()->recorrido?->nombre ?? 'Recorrido eliminado',
                'cantidad' => $g->count(), 'total' => (float) $g->sum('importe'),
            ])->values(),
            'alumnos' => Alumno::orderBy('apellido')->orderBy('nombre')->get(['id', 'apellido', 'nombre']),
            'deportes' => Deporte::orderBy('nombre')->get(['id', 'nombre']),
            'niveles' => Nivel::orderBy('nombre')->get(['id', 'nombre']),
            'profesores' => Profesor::orderBy('apellido')->get(['id', 'apellido', 'nombre']),
            'recorridos' => RecorridoTransporte::orderBy('nombre')->get(['id', 'nombre']),
            'dias' => self::DIAS,
        ]);
    }
}
