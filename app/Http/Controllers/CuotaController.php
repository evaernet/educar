<?php

namespace App\Http\Controllers;

use App\Models\Deporte;
use App\Models\Factura;
use App\Models\Profesor;
use App\Models\RecorridoTransporte;
use App\Models\ValorCuota;
use App\Services\FacturacionService;
use Illuminate\Http\Request;

/**
 * ¿Qué hace?: Pantalla del administrador para fijar los valores de cuota, deportes,
 * transporte y comedor, y para generar las facturas del mes.
 * Conexión: rutas admin.cuotas.* (solo rol admin).
 */
class CuotaController extends Controller
{
    public function index(Request $request)
    {
        $periodo = $request->query('periodo', now()->format('Y-m'));

        return view('cuotas.index', [
            'cuota' => ValorCuota::de('cuota'),
            'comedor' => ValorCuota::de('comedor'),
            'deportes' => Deporte::with('profesor')->where('activo', true)->orderBy('nombre')->get(),
            'recorridos' => RecorridoTransporte::where('activo', true)->orderBy('nombre')->get(),
            'profesores' => Profesor::where('activo', true)->orderBy('apellido')->get(),
            'periodo' => $periodo,
            'facturas' => Factura::with('alumno')->where('periodo', $periodo)->orderBy('alumno_id')->get(),
        ]);
    }

    /** Guarda todos los valores de una sola vez. */
    public function guardarValores(Request $request)
    {
        $datos = $request->validate([
            'cuota' => 'required|numeric|min:0|max:99999999',
            'comedor' => 'required|numeric|min:0|max:99999999',
            'deportes' => 'nullable|array',
            'deportes.*.valor_mensual' => 'required|numeric|min:0|max:99999999',
            'deportes.*.profesor_id' => 'nullable|exists:profesores,id',
            'recorridos' => 'nullable|array',
            'recorridos.*.valor_mensual' => 'required|numeric|min:0|max:99999999',
        ]);

        ValorCuota::updateOrCreate(['concepto' => 'cuota'], ['importe' => $datos['cuota']]);
        ValorCuota::updateOrCreate(['concepto' => 'comedor'], ['importe' => $datos['comedor']]);

        foreach ($datos['deportes'] ?? [] as $id => $fila) {
            Deporte::whereKey($id)->update([
                'valor_mensual' => $fila['valor_mensual'],
                'profesor_id' => $fila['profesor_id'] ?? null,
            ]);
        }
        foreach ($datos['recorridos'] ?? [] as $id => $fila) {
            RecorridoTransporte::whereKey($id)->update(['valor_mensual' => $fila['valor_mensual']]);
        }

        return redirect()->route('admin.cuotas.index')->with('success', 'Valores guardados correctamente.');
    }

    /** Genera las facturas de todos los alumnos para el periodo elegido. */
    public function generar(Request $request, FacturacionService $servicio)
    {
        $datos = $request->validate(['periodo' => ['required', 'regex:/^\d{4}-(0[1-9]|1[0-2])$/']]);
        $creadas = $servicio->generarParaTodos($datos['periodo']);

        return redirect()->route('admin.cuotas.index', ['periodo' => $datos['periodo']])
            ->with('success', "Facturas nuevas generadas: $creadas.");
    }
}
