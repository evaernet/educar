<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InscripcionAcademica extends Model
{
    protected $fillable = ['alumno_id', 'ciclo_lectivo_id', 'curso_id', 'activo'];

    protected function casts(): array
    {
        return ['activo' => 'boolean'];
    }

    public function alumno() { return $this->belongsTo(Alumno::class); }
    public function cicloLectivo() { return $this->belongsTo(CicloLectivo::class); }
    public function curso() { return $this->belongsTo(Curso::class); }
}
