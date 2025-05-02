<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Persona
 *
 * @property int $Id_Persona
 * @property string $Nombre
 * @property string $Apellido1
 * @property string|null $Apellido2
 * @property int|null $CI
 * @property string|null $Genero
 * @property string|null $Correo
 * @property int|null $Id_Usuario
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property Usuario|null $usuario
 * @property Collection|Estudiante[] $estudiantes
 * @property Collection|Telefono[] $telefonos
 *
 * @package App\Models
 */
class Persona extends Model
{
	protected $table = 'persona';
	protected $primaryKey = 'Id_Persona';

	protected $casts = [
		'CI' => 'int',
		'Genero' => 'boolean',
		'Id_Usuario' => 'int'
	];

	protected $fillable = [
		'Nombre',
		'Apellido1',
		'Apellido2',
		'CI',
		'Genero',
		'Correo',
		'Id_Usuario'
	];

	public function usuario()
	{
		return $this->belongsTo(Usuario::class, 'Id_Usuario');
	}

	public function estudiantes()
	{
		return $this->hasMany(Estudiante::class, 'Id_Persona');
	}

	public function telefonos()
	{
		return $this->hasMany(Telefono::class, 'Id_Persona');
	}
}
