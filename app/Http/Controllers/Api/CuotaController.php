<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Alumno;
use App\Models\Comprobante;
use App\Models\Factura;
use App\Models\FacturaItem;
use App\Models\Pago;
use App\Services\FacturacionService;
use App\Services\FacturaPdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * ¿Qué hace?: Endpoints de cuotas para la app móvil (solo padres/tutores).
 * Cada método revisa que el alumno / la factura sea de un hijo del padre logueado.
 */
class CuotaController extends Controller
{
    private const MSG_ROL = 'Este recurso está disponible solo para padres o tutores.';
    private const MSG_ACCESO = 'No tenés acceso a la información de este alumno.';

    /** Lista de facturas de un hijo, con filtro opcional de fechas (?desde=AAAA-MM-DD&hasta=AAAA-MM-DD). */
    public function facturas(Request $request, Alumno $alumno): JsonResponse
    {
        if ($error = $this->validarHijo($request, $alumno)) {
            return $error;
        }
        $datos = $request->validate(['desde' => 'nullable|date', 'hasta' => 'nullable|date|after_or_equal:desde']);

        $facturas = $alumno->facturas()->with('items')
            ->when($datos['desde'] ?? null, fn ($q, $d) => $q->whereDate('fecha_emision', '>=', $d))
            ->when($datos['hasta'] ?? null, fn ($q, $h) => $q->whereDate('fecha_emision', '<=', $h))
            ->orderByDesc('periodo')->get();

        return response()->json(['facturas' => $facturas->map(fn ($f) => $this->formatearFactura($f))]);
    }

    /** Comprobantes de pago emitidos y comprobantes de transferencia subidos, en un rango de fechas. */
    public function comprobantes(Request $request, Alumno $alumno): JsonResponse
    {
        if ($error = $this->validarHijo($request, $alumno)) {
            return $error;
        }
        $datos = $request->validate(['desde' => 'nullable|date', 'hasta' => 'nullable|date|after_or_equal:desde']);
        $facturaIds = $alumno->facturas()->pluck('id');

        $pagos = Pago::with('items', 'factura')->whereIn('factura_id', $facturaIds)
            ->when($datos['desde'] ?? null, fn ($q, $d) => $q->whereDate('fecha_pago', '>=', $d))
            ->when($datos['hasta'] ?? null, fn ($q, $h) => $q->whereDate('fecha_pago', '<=', $h))
            ->orderByDesc('fecha_pago')->orderByDesc('id')->get();

        $subidos = Comprobante::whereIn('factura_id', $facturaIds)
            ->when($datos['desde'] ?? null, fn ($q, $d) => $q->whereDate('created_at', '>=', $d))
            ->when($datos['hasta'] ?? null, fn ($q, $h) => $q->whereDate('created_at', '<=', $h))
            ->latest()->get();

        return response()->json([
            'pagos' => $pagos->map(fn ($p) => [
                'id' => $p->id, 'numero' => $p->numero, 'factura_id' => $p->factura_id,
                'periodo' => $p->factura->periodo, 'total' => (float) $p->total,
                'fecha_pago' => $p->fecha_pago->toDateString(),
                'items' => $p->items->map(fn ($i) => ['descripcion' => $i->descripcion, 'importe' => (float) $i->importe]),
            ]),
            'comprobantes_subidos' => $subidos->map(fn ($c) => [
                'id' => $c->id, 'factura_id' => $c->factura_id, 'nombre' => $c->nombre_original,
                'observacion' => $c->observacion, 'fecha' => $c->created_at->toDateString(),
            ]),
        ]);
    }

    /** Deuda actual del hijo y su historial agrupado por ítem (cuota, deporte, comedor, transporte). */
    public function deuda(Request $request, Alumno $alumno): JsonResponse
    {
        if ($error = $this->validarHijo($request, $alumno)) {
            return $error;
        }
        $items = FacturaItem::with('factura')
            ->whereIn('factura_id', $alumno->facturas()->pluck('id'))
            ->get()->sortByDesc(fn ($i) => $i->factura->periodo)->values();

        $pendientes = $items->whereNull('pago_id');

        return response()->json([
            'deuda_total' => (float) $pendientes->sum('importe'),
            'pendientes' => $pendientes->values()->map(fn ($i) => $this->formatearItem($i)),
            // historial: para cada concepto, todos los meses con su estado
            'historial' => $items->groupBy('descripcion')->map(fn ($grupo, $nombre) => [
                'item' => $nombre,
                'deuda' => (float) $grupo->whereNull('pago_id')->sum('importe'),
                'meses' => $grupo->map(fn ($i) => $this->formatearItem($i))->values(),
            ])->values(),
        ]);
    }

    /** Detalle de una factura. */
    public function mostrar(Request $request, Factura $factura): JsonResponse
    {
        if ($error = $this->validarFactura($request, $factura)) {
            return $error;
        }
        $factura->load('items', 'comprobantes');
        $data = $this->formatearFactura($factura);
        $data['comprobantes'] = $factura->comprobantes->map(fn ($c) => ['id' => $c->id, 'nombre' => $c->nombre_original, 'observacion' => $c->observacion]);
        return response()->json(['factura' => $data]);
    }

    /** PDF de la factura. */
    public function pdfFactura(Request $request, Factura $factura)
    {
        if ($error = $this->validarFactura($request, $factura)) {
            return $error;
        }
        return response(FacturaPdf::factura($factura), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="factura-' . $factura->periodo . '.pdf"',
        ]);
    }

    /** Sube el comprobante de transferencia (imagen o PDF) atado a la factura. */
    public function subirComprobante(Request $request, Factura $factura): JsonResponse
    {
        if ($error = $this->validarFactura($request, $factura)) {
            return $error;
        }
        $datos = $request->validate([
            'archivo' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'observacion' => 'nullable|string|max:255',
        ]);

        $comprobante = $this->guardarArchivo($request, $factura, $datos['observacion'] ?? null);
        return response()->json(['message' => 'Comprobante subido correctamente.', 'comprobante_id' => $comprobante->id], 201);
    }

    /**
     * Paga los ítems elegidos. Como el pago es solo por transferencia, la factura debe
     * tener al menos un comprobante subido (o se adjunta uno en este mismo pedido).
     */
    public function pagar(Request $request, Factura $factura, FacturacionService $servicio): JsonResponse
    {
        if ($error = $this->validarFactura($request, $factura)) {
            return $error;
        }
        $datos = $request->validate([
            'items' => 'required|array|min:1',
            'items.*' => 'integer',
            'archivo' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'observacion' => 'nullable|string|max:255',
        ]);

        if (! $request->hasFile('archivo') && ! $factura->comprobantes()->exists()) {
            return response()->json(['message' => 'Primero subí el comprobante de la transferencia.'], 422);
        }
        // Se valida el pago antes de guardar el archivo para no dejar comprobantes sueltos
        $pago = $servicio->pagar($factura, $request->user()->id, $datos['items']);
        if ($request->hasFile('archivo')) {
            $this->guardarArchivo($request, $factura, $datos['observacion'] ?? null);
        }

        return response()->json([
            'message' => 'Pago registrado correctamente.',
            'pago' => ['id' => $pago->id, 'numero' => $pago->numero, 'total' => (float) $pago->total],
            'factura_estado' => $factura->fresh()->estado,
        ], 201);
    }

    /** PDF del comprobante de pago emitido por el sistema. */
    public function pdfPago(Request $request, Pago $pago)
    {
        if ($error = $this->validarFactura($request, $pago->factura)) {
            return $error;
        }
        return response(FacturaPdf::comprobante($pago), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $pago->numero . '.pdf"',
        ]);
    }

    /** Descarga un comprobante de transferencia subido. */
    public function archivo(Request $request, Comprobante $comprobante)
    {
        if ($error = $this->validarFactura($request, $comprobante->factura)) {
            return $error;
        }
        abort_unless(Storage::disk('local')->exists($comprobante->ruta), 404);
        return Storage::disk('local')->download($comprobante->ruta, $comprobante->nombre_original);
    }

    // ---------------------------------------------------------------- ayudas internas

    private function guardarArchivo(Request $request, Factura $factura, ?string $observacion): Comprobante
    {
        $archivo = $request->file('archivo');
        return Comprobante::create([
            'factura_id' => $factura->id,
            'user_id' => $request->user()->id,
            'ruta' => $archivo->store('comprobantes/' . $factura->id, 'local'),
            'nombre_original' => $archivo->getClientOriginalName(),
            'observacion' => $observacion,
        ]);
    }

    private function validarHijo(Request $request, Alumno $alumno): ?JsonResponse
    {
        if ($request->user()->role !== 'padre') {
            return response()->json(['message' => self::MSG_ROL], 403);
        }
        $esHijo = $request->user()->hijos()->whereKey($alumno->id)->where('alumnos.activo', true)->exists();
        return $esHijo ? null : response()->json(['message' => self::MSG_ACCESO], 404);
    }

    private function validarFactura(Request $request, Factura $factura): ?JsonResponse
    {
        return $this->validarHijo($request, $factura->alumno);
    }

    private function formatearFactura(Factura $f): array
    {
        return [
            'id' => $f->id, 'alumno_id' => $f->alumno_id, 'periodo' => $f->periodo,
            'fecha_emision' => $f->fecha_emision->toDateString(),
            'fecha_vencimiento' => $f->fecha_vencimiento->toDateString(),
            'total' => (float) $f->total, 'estado' => $f->estado,
            'vencida' => $f->estado !== 'pagada' && $f->fecha_vencimiento->isPast(),
            'saldo_pendiente' => (float) $f->items->whereNull('pago_id')->sum('importe'),
            'items' => $f->items->map(fn ($i) => $this->formatearItem($i, false))->values(),
        ];
    }

    private function formatearItem(FacturaItem $i, bool $conPeriodo = true): array
    {
        $fila = [
            'id' => $i->id, 'tipo' => $i->tipo, 'descripcion' => $i->descripcion,
            'importe' => (float) $i->importe, 'pagado' => $i->pago_id !== null,
            'pagado_el' => $i->pagado_at?->toDateString(),
        ];
        if ($conPeriodo) {
            $fila['factura_id'] = $i->factura_id;
            $fila['periodo'] = $i->factura->periodo;
        }
        return $fila;
    }
}
