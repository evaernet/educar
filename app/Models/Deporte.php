<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Deporte extends Model
{
    use HasFactory;
    protected $fillable = ['nombre', 'activo'];
    protected function casts(): array { return ['activo' => 'boolean']; }
    public function horarios() { return $this->hasMany(HorarioDeporte::class); }
    public function inscripciones() { return $this->hasMany(InscripcionDeportiva::class); }
}
