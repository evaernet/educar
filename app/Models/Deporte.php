<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Deporte extends Model
{
    use HasFactory;
    protected $fillable = ['nombre', 'activo', 'valor_mensual', 'profesor_id'];
    protected function casts(): array { return ['activo' => 'boolean']; }
    public function profesor() { return $this->belongsTo(Profesor::class); }
    public function horarios() { return $this->hasMany(HorarioDeporte::class); }
    public function inscripciones() { return $this->hasMany(InscripcionDeportiva::class); }
}
