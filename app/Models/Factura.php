<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * ¿Qué hace?: Factura mensual de un alumno. Tiene ítems, pagos y comprobantes subidos.
 */
class Factura extends Model
{
    protected $fillable = ['alumno_id', 'periodo', 'fecha_emision', 'fecha_vencimiento', 'total', 'estado'];

    protected function casts(): array
    {
        return [
            'fecha_emision' => 'date',
            'fecha_vencimiento' => 'date',
            'total' => 'decimal:2',
        ];
    }

    public function alumno() { return $this->belongsTo(Alumno::class); }
    public function items() { return $this->hasMany(FacturaItem::class); }
    public function pagos() { return $this->hasMany(Pago::class); }
    public function comprobantes() { return $this->hasMany(Comprobante::class); }

    /** Suma de los ítems que todavía no se pagaron. */
    public function saldoPendiente(): float
    {
        return (float) $this->items()->whereNull('pago_id')->sum('importe');
    }

    /** Vuelve a calcular el estado según los ítems pagados. */
    public function recalcularEstado(): void
    {
        $total = $this->items()->count();
        $pagados = $this->items()->whereNotNull('pago_id')->count();
        $this->estado = $pagados === 0 ? 'pendiente' : ($pagados === $total ? 'pagada' : 'parcial');
        $this->save();
    }
}
