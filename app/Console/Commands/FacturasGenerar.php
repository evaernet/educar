<?php

namespace App\Console\Commands;

use App\Services\FacturacionService;
use Illuminate\Console\Command;

/**
 * ¿Qué hace?: Genera las facturas de todos los alumnos activos para un periodo.
 * Uso: php artisan facturas:generar 2026-11   (sin argumento usa el mes que viene)
 */
class FacturasGenerar extends Command
{
    protected $signature = 'facturas:generar {periodo? : Formato AAAA-MM}';
    protected $description = 'Genera las facturas mensuales de los alumnos';

    public function handle(FacturacionService $servicio): int
    {
        $periodo = $this->argument('periodo') ?: $servicio->periodoSiguiente(now());
        if (!preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $periodo)) {
            $this->error('El periodo debe tener formato AAAA-MM.');
            return self::FAILURE;
        }
        $this->info('Facturas creadas para ' . $periodo . ': ' . $servicio->generarParaTodos($periodo));
        return self::SUCCESS;
    }
}
