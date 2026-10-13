<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class RecorridoTransporte extends Model { protected $table = 'recorridos_transporte'; protected $fillable = ['nombre','zona','cupo','activo','valor_mensual']; protected function casts(): array { return ['activo'=>'boolean']; } public function inscripciones() { return $this->hasMany(InscripcionTransporte::class); } }
