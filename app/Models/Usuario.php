<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * Class Usuario
 *
 * @property int $Id_Usuario
 * @property string $Usuario
 * @property string $Clave
 * @property string $Rol
 * @property string|null $Estado
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property Empresa|null $empresa
 * @property Estudiante|null $estudiante
 */
class Usuario extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    protected $table = 'usuario';
    protected $primaryKey = 'Id_Usuario';

    protected $fillable = [
        'Usuario',
        'Clave',
        'Rol',
        'Estado'
    ];

    protected $hidden = [
        'Clave',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'Clave' => 'hashed',
        ];
    }

    // Sobrescribimos este método para que Laravel use 'Clave' como campo de contraseña
    public function getAuthPassword()
    {
        return $this->Clave;
    }

    // Relaciones
    public function empresa()
    {
        return $this->hasOne(Empresa::class, 'Id_Usuario');
    }

    public function estudiante()
    {
        return $this->hasOne(Estudiante::class, 'Id_Usuario');
    }

    // Métodos requeridos por JWT
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}

