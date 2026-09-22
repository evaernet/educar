<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * RELACIÓN: alumno()
     *
     * ¿Qué hace?: Si este usuario tiene rol "alumno", esta relación trae
     * su fila correspondiente en la tabla "alumnos".
     * Conexión: Es la relación inversa de Alumno::user() (que ya existía).
     * Alumno "pertenece a" un User (belongsTo); acá decimos que un User
     * "tiene un" Alumno (hasOne). Gracias a esto, en el DashboardController
     * podemos escribir auth()->user()->alumno y Laravel arma el JOIN solo.
     */
    public function alumno(): HasOne
    {
        return $this->hasOne(Alumno::class);
    }

    /**
     * RELACIÓN: profesor()
     *
     * ¿Qué hace?: Lo mismo que alumno(), pero para usuarios con rol "profesor".
     * Es la relación inversa de Profesor::user().
     */
    public function profesor(): HasOne
    {
        return $this->hasOne(Profesor::class);
    }
}
