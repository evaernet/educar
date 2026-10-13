<?php

namespace App\Services;

use App\Models\Factura;
use App\Models\Pago;

/**
 * ¿Qué hace?: Genera el PDF de una factura y el PDF de un comprobante de pago.
 */
class FacturaPdf
{
    public static function factura(Factura $factura): string
    {
        $factura->loadMissing('alumno', 'items');
        $l = [];
        $l[] = 'EDUCAR PARA TRANSFORMAR';
        $l[] = 'FACTURA - Periodo ' . $factura->periodo;
        $l[] = str_repeat('-', 70);
        $l[] = 'Alumno: ' . $factura->alumno->apellido . ', ' . $factura->alumno->nombre . ' (Legajo ' . $factura->alumno->legajo . ')';
        $l[] = 'Emision: ' . $factura->fecha_emision->format('d/m/Y') . '   Vencimiento: ' . $factura->fecha_vencimiento->format('d/m/Y');
        $l[] = 'Estado: ' . strtoupper($factura->estado);
        $l[] = str_repeat('-', 70);
        foreach ($factura->items as $item) {
            $l[] = str_pad(mb_substr($item->descripcion, 0, 45), 48) . str_pad('$ ' . number_format((float) $item->importe, 2, ',', '.'), 14, ' ', STR_PAD_LEFT)
                . ($item->pago_id ? '  PAGADO' : '');
        }
        $l[] = str_repeat('-', 70);
        $l[] = str_pad('TOTAL', 48) . str_pad('$ ' . number_format((float) $factura->total, 2, ',', '.'), 14, ' ', STR_PAD_LEFT);
        $l[] = 'Saldo pendiente: $ ' . number_format($factura->saldoPendiente(), 2, ',', '.');
        $l[] = '';
        $l[] = 'Forma de pago: transferencia bancaria (no se aceptan pagos en efectivo).';
        return SimplePdf::render($l);
    }

    public static function comprobante(Pago $pago): string
    {
        $pago->loadMissing('factura.alumno', 'items');
        $l = [];
        $l[] = 'EDUCAR PARA TRANSFORMAR';
        $l[] = 'COMPROBANTE DE PAGO Nro ' . $pago->numero;
        $l[] = str_repeat('-', 70);
        $l[] = 'Alumno: ' . $pago->factura->alumno->apellido . ', ' . $pago->factura->alumno->nombre;
        $l[] = 'Factura periodo: ' . $pago->factura->periodo;
        $l[] = 'Fecha de pago: ' . $pago->fecha_pago->format('d/m/Y') . '   Medio: ' . $pago->medio;
        $l[] = str_repeat('-', 70);
        foreach ($pago->items as $item) {
            $l[] = str_pad(mb_substr($item->descripcion, 0, 45), 48) . str_pad('$ ' . number_format((float) $item->importe, 2, ',', '.'), 14, ' ', STR_PAD_LEFT);
        }
        $l[] = str_repeat('-', 70);
        $l[] = str_pad('TOTAL PAGADO', 48) . str_pad('$ ' . number_format((float) $pago->total, 2, ',', '.'), 14, ' ', STR_PAD_LEFT);
        return SimplePdf::render($l);
    }
}
