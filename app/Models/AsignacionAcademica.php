<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AsignacionAcademica extends Model
{
    protected $fillable = ['ciclo_lectivo_id', 'curso_id', 'materia_id', 'profesor_id', 'activo'];

    protected function casts(): array
    {
        return ['activo' => 'boolean'];
    }
    public function cicloLectivo() { return $this->belongsTo(CicloLectivo::class); }
    public function curso() { return $this->belongsTo(Curso::class); }
    public function materia() { return $this->belongsTo(Materia::class); }
    public function profesor() { return $this->belongsTo(Profesor::class); }
    public function horarios() { return $this->hasMany(HorarioClase::class); }
}
