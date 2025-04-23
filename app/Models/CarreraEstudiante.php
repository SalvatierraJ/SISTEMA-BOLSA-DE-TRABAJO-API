<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class CarreraEstudiante
 * 
 * @property int $Id_Carrera_Estudiante
 * @property int|null $Id_Carrera
 * @property int|null $Id_Estudiante
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Carrera|null $carrera
 * @property Estudiante|null $estudiante
 *
 * @package App\Models
 */
class CarreraEstudiante extends Model
{
	protected $table = 'carrera_estudiante';
	protected $primaryKey = 'Id_Carrera_Estudiante';

	protected $casts = [
		'Id_Carrera' => 'int',
		'Id_Estudiante' => 'int'
	];

	protected $fillable = [
		'Id_Carrera',
		'Id_Estudiante'
	];

	public function carrera()
	{
		return $this->belongsTo(Carrera::class, 'Id_Carrera');
	}

	public function estudiante()
	{
		return $this->belongsTo(Estudiante::class, 'Id_Estudiante');
	}
}
