<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * ¿Qué hace?: Archivo de la transferencia bancaria que sube el padre, atado a una factura.
 */
class Comprobante extends Model
{
    protected $fillable = ['factura_id', 'user_id', 'ruta', 'nombre_original', 'observacion'];

    public function factura() { return $this->belongsTo(Factura::class); }
    public function user() { return $this->belongsTo(User::class); }
}
