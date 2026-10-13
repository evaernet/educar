<?php

namespace App\Console\Commands;

use App\Models\Factura;
use App\Models\User;
use App\Mail\FacturaMensualMail;
use App\Services\FacturacionService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

/**
 * ¿Qué hace?: El último día hábil del mes genera las facturas del mes siguiente
 * y le manda a cada padre un mail con el total, la composición y la factura en PDF.
 * Se ejecuta todos los días desde el scheduler, pero solo actúa el último día hábil
 * (o siempre que se use --forzar).
 */
class FacturasRecordatorio extends Command
{
    protected $signature = 'facturas:recordatorio {--forzar : Ignora la fecha y envía igual}';
    protected $description = 'Mail de fin de mes con el total a pagar y la factura adjunta';

    public function handle(FacturacionService $servicio): int
    {
        $hoy = now()->startOfDay();
        if (!$this->option('forzar') && !$hoy->equalTo($servicio->ultimoDiaHabil($hoy))) {
            $this->line('Hoy no es el último día hábil del mes.');
            return self::SUCCESS;
        }

        $periodo = $servicio->periodoSiguiente($hoy);
        $servicio->generarParaTodos($periodo);

        $enviados = 0;
        foreach (User::where('role', 'padre')->where('activo', true)->get() as $padre) {
            $facturas = Factura::with('alumno', 'items')
                ->where('periodo', $periodo)
                ->whereIn('alumno_id', $padre->hijos()->pluck('alumnos.id'))
                ->get();
            if ($facturas->isEmpty()) {
                continue;
            }
            Mail::to($padre->email)->send(new FacturaMensualMail($padre->name, $facturas));
            $enviados++;
        }
        $this->info("Mails enviados: $enviados (periodo $periodo)");
        return self::SUCCESS;
    }
}
