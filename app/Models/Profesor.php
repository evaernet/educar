<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Profesor extends Model
{
    protected $table = 'profesores';

    protected $fillable = ['user_id', 'legajo', 'dni', 'nombre', 'apellido', 'especialidad', 'email', 'telefono', 'activo'];
    protected function casts(): array { return ['activo' => 'boolean']; }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
