<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
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
 * @property string|null $Estado
 * @property int|null $Id_Rol
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property Rol|null $rol
 * @property Collection|Empresa[] $empresas
 * @property Collection|Multimedia[] $multimedia
 * @property Collection|Persona[] $personas
 * @property Collection|Testimonio[] $testimonios
 *
 * @package App\Models
 */
class Usuario extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

	protected $table = 'usuario';
	protected $primaryKey = 'Id_Usuario';

	protected $casts = [
		'Id_Rol' => 'int',
        'email_verified_at' => 'datetime',
        'Clave' => 'hashed',
	];

	protected $fillable = [
		'Usuario',
		'Clave',
		'Estado',
		'Id_Rol'
	];
    protected $hidden = [
        'Clave',
        'remember_token',
    ];
    public function username(){
        return 'Usuario';
    }


	public function rol()
	{
		return $this->belongsTo(Rol::class, 'Id_Rol');
	}

	public function empresas()
	{
		return $this->hasMany(Empresa::class, 'Id_Usuario');
	}

	public function multimedia()
	{
		return $this->hasMany(Multimedia::class, 'Id_Usuario');
	}

	public function personas()
	{
		return $this->hasMany(Persona::class, 'Id_Usuario');
	}

	public function testimonios()
	{
		return $this->hasMany(Testimonio::class, 'Id_Usuario');
	}
    // Sobrescribimos este método para que Laravel use 'Clave' como campo de contraseña
    public function getAuthPassword()
    {
        return $this->Clave;
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
