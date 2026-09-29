<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CicloLectivo extends Model
{
    protected $fillable = ['anio','fecha_inicio','fecha_fin','activo'];
    protected function casts(): array { return ['fecha_inicio'=>'date','fecha_fin'=>'date','activo'=>'boolean']; }
    public function asignaciones() { return $this->hasMany(AsignacionAcademica::class); }
    public function inscripciones() { return $this->hasMany(InscripcionAcademica::class); }
}
