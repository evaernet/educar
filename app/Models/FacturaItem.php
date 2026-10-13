<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FacturaItem extends Model
{
    protected $fillable = ['factura_id', 'tipo', 'referencia_id', 'descripcion', 'importe', 'pago_id', 'pagado_at'];

    protected function casts(): array
    {
        return ['importe' => 'decimal:2', 'pagado_at' => 'datetime'];
    }

    public function factura() { return $this->belongsTo(Factura::class); }
    public function pago() { return $this->belongsTo(Pago::class); }
    public function deporte() { return $this->belongsTo(Deporte::class, 'referencia_id'); }
    public function recorrido() { return $this->belongsTo(RecorridoTransporte::class, 'referencia_id'); }
}
