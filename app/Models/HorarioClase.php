<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HorarioClase extends Model
{
    protected $fillable = ['asignacion_academica_id','dia_semana','hora_inicio','hora_fin'];
    public function asignacion() { return $this->belongsTo(AsignacionAcademica::class, 'asignacion_academica_id'); }
}
