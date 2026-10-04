<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Curso extends Model
{
    protected $fillable = ['nivel_id', 'nombre', 'division', 'turno', 'activo'];
    protected function casts(): array { return ['activo' => 'boolean']; }
    public function nivel() { return $this->belongsTo(Nivel::class); }
    public function inscripciones() { return $this->hasMany(InscripcionAcademica::class); }
}
