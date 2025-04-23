<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Estudiante
 * 
 * @property int $Id_Estudiante
 * @property string|null $Nro_Registro
 * @property int|null $Id_Persona
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Persona|null $persona
 * @property Collection|Carrera[] $carreras
 * @property Collection|Curriculum[] $curricula
 * @property Collection|Postulacion[] $postulacions
 *
 * @package App\Models
 */
class Estudiante extends Model
{
	protected $table = 'estudiante';
	protected $primaryKey = 'Id_Estudiante';

	protected $casts = [
		'Id_Persona' => 'int'
	];

	protected $fillable = [
		'Nro_Registro',
		'Id_Persona'
	];

	public function persona()
	{
		return $this->belongsTo(Persona::class, 'Id_Persona');
	}

	public function carreras()
	{
		return $this->belongsToMany(Carrera::class, 'carrera_estudiante', 'Id_Estudiante', 'Id_Carrera')
					->withPivot('Id_Carrera_Estudiante')
					->withTimestamps();
	}

	public function curricula()
	{
		return $this->hasMany(Curriculum::class, 'Id_Estudiante');
	}

	public function postulacions()
	{
		return $this->hasMany(Postulacion::class, 'Id_Estudiante');
	}
}
