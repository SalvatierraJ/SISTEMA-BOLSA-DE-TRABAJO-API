<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Usuario extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    protected $table = 'usuario'; // Indicar la tabla de tu BD
    protected $primaryKey = 'Id_Usuario'; // Definir tu primary key personalizada
    public $timestamps = true; // Laravel manejará created_at y updated_at

    /**
     * Los atributos que son asignables en masa (Mass-Assignable).
     *
     * @var array
     */
    protected $fillable = [
        'Usuario',
        'Clave',
        'Rol',
        'Estado'
    ];

    /**
     * Los atributos que deben estar ocultos cuando se serializa el modelo.
     *
     * @var array
     */
    protected $hidden = [
        'Clave',
        'remember_token',
    ];

    /**
     * Los atributos que deben ser convertidos a tipos nativos.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relaciones
     */
    public function empresa()
    {
        return $this->hasOne(Empresa::class, 'Id_Usuario');
    }

    public function estudiante()
    {
        return $this->hasOne(Estudiante::class, 'Id_Usuario');
    }

    /**
     * Obtener el identificador que se almacenará en el JWT.
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Devolver un array con los claims personalizados del JWT.
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * Reemplazar la columna `password` por `Clave`.
     */
    public function getAuthPassword()
    {
        return $this->Clave;
    }
}
