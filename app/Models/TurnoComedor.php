<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class TurnoComedor extends Model { protected $fillable=['nombre','hora_inicio','hora_fin','cupo','activo']; protected function casts():array{return ['activo'=>'boolean'];} public function inscripciones(){return $this->hasMany(InscripcionComedor::class);} }
