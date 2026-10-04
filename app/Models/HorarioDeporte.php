<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HorarioDeporte extends Model
{
    protected $fillable = ['deporte_id', 'dia_semana', 'hora_inicio', 'hora_fin'];
    public function deporte() { return $this->belongsTo(Deporte::class); }
}
