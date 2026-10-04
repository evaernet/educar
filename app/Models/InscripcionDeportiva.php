<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InscripcionDeportiva extends Model
{
    protected $fillable = ['alumno_id', 'deporte_id', 'activo'];
    protected function casts(): array { return ['activo' => 'boolean']; }
    public function alumno() { return $this->belongsTo(Alumno::class); }
    public function deporte() { return $this->belongsTo(Deporte::class); }
}
