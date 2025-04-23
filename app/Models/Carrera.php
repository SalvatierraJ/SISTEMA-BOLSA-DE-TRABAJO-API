<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Carrera
 * 
 * @property int $Id_Carrera
 * @property string|null $Nombre
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Collection|Estudiante[] $estudiantes
 *
 * @package App\Models
 */
class Carrera extends Model
{
	protected $table = 'carrera';
	protected $primaryKey = 'Id_Carrera';

	protected $fillable = [
		'Nombre'
	];

	public function estudiantes()
	{
		return $this->belongsToMany(Estudiante::class, 'carrera_estudiante', 'Id_Carrera', 'Id_Estudiante')
					->withPivot('Id_Carrera_Estudiante')
					->withTimestamps();
	}
}
