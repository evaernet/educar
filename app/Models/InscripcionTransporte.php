<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class InscripcionTransporte extends Model { protected $fillable=['alumno_id','recorrido_transporte_id','activo']; protected function casts(): array { return ['activo'=>'boolean']; } public function alumno(){ return $this->belongsTo(Alumno::class); } public function recorrido(){ return $this->belongsTo(RecorridoTransporte::class,'recorrido_transporte_id'); } }
