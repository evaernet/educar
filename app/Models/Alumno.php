<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Alumno extends Model
{
    protected $fillable = [
        'user_id',
        'legajo',
        'dni',
        'nombre',
        'apellido',
        'fecha_nacimiento',
        'domicilio',
        'telefono',
        'email',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'fecha_nacimiento' => 'date',
            'activo' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function inscripciones()
    {
        return $this->hasMany(InscripcionAcademica::class);
    }

    public function inscripcionesDeportivas()
    {
        return $this->hasMany(InscripcionDeportiva::class);
    }

    public function inscripcionesComedor()
    {
        return $this->hasMany(InscripcionComedor::class);
    }

    public function inscripcionesTransporte()
    {
        return $this->hasMany(InscripcionTransporte::class);
    }
}
