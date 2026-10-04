<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class InscripcionComedor extends Model { protected $fillable=['alumno_id','turno_comedor_id','activo']; protected function casts():array{return ['activo'=>'boolean'];} public function alumno(){return $this->belongsTo(Alumno::class);} public function turno(){return $this->belongsTo(TurnoComedor::class,'turno_comedor_id');} }
