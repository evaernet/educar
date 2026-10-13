<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * ¿Qué hace?: Importes generales fijados por el admin ('cuota' y 'comedor').
 */
class ValorCuota extends Model
{
    protected $table = 'valores_cuota';
    protected $fillable = ['concepto', 'importe'];

    /** Devuelve el importe de un concepto (0 si todavía no se cargó). */
    public static function de(string $concepto): float
    {
        return (float) (static::where('concepto', $concepto)->value('importe') ?? 0);
    }
}
