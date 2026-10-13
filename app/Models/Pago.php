<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * ¿Qué hace?: Pago realizado por un padre = comprobante de pago que emite el sistema.
 */
class Pago extends Model
{
    protected $fillable = ['factura_id', 'user_id', 'numero', 'total', 'fecha_pago', 'medio'];

    protected function casts(): array
    {
        return ['fecha_pago' => 'date', 'total' => 'decimal:2'];
    }

    public function factura() { return $this->belongsTo(Factura::class); }
    public function user() { return $this->belongsTo(User::class); }
    public function items() { return $this->hasMany(FacturaItem::class); }
}
