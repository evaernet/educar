<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Profesor extends Model
{
    protected $fillable = [
        'user_id','legajo','dni','nombre', 'apellido', 'email', 'telefono', 'activo', 'especialidad',
    ];

    protected function casts():array
    {
        return[
            'activo' => 'boolean'
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }


}
