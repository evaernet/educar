<?php

namespace App\Console\Commands;

use App\Mail\DeudaMail;
use App\Models\Factura;
use App\Models\User;
use App\Services\FacturacionService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

/**
 * ¿Qué hace?: El día 20 de cada mes avisa por mail a los padres que todavía
 * tienen ítems sin pagar de las facturas del mes. Con --forzar ignora la fecha.
 */
class FacturasDeuda extends Command
{
    protected $signature = 'facturas:deuda {--forzar : Ignora la fecha y envía igual}';
    protected $description = 'Mail del día 20 con la deuda pendiente';

    public function handle(): int
    {
        if (!$this->option('forzar') && now()->day !== FacturacionService::DIA_VENCIMIENTO) {
            $this->line('Hoy no es día 20.');
            return self::SUCCESS;
        }

        $periodo = now()->format('Y-m');
        $enviados = 0;
        foreach (User::where('role', 'padre')->where('activo', true)->get() as $padre) {
            $facturas = Factura::with('alumno', 'items')
                ->where('periodo', $periodo)
                ->where('estado', '!=', 'pagada')
                ->whereIn('alumno_id', $padre->hijos()->pluck('alumnos.id'))
                ->get();
            if ($facturas->isEmpty()) {
                continue;
            }
            Mail::to($padre->email)->send(new DeudaMail($padre->name, $facturas));
            $enviados++;
        }
        $this->info("Mails de deuda enviados: $enviados");
        return self::SUCCESS;
    }
}
