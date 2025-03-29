<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Curriculum
 * 
 * @property int $Id_Curriculum
 * @property string|null $Descripcion
 * @property array|null $Habilidades
 * @property array|null $Certificados
 * @property array|null $Experiencia
 * @property array|null $Idiomas
 * @property int|null $Id_Estudiante
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Estudiante|null $estudiante
 *
 * @package App\Models
 */
class Curriculum extends Model
{
	protected $table = 'curriculum';
	protected $primaryKey = 'Id_Curriculum';

	protected $casts = [
		'Habilidades' => 'json',
		'Certificados' => 'json',
		'Experiencia' => 'json',
		'Idiomas' => 'json',
		'Id_Estudiante' => 'int'
	];

	protected $fillable = [
		'Descripcion',
		'Habilidades',
		'Certificados',
		'Experiencia',
		'Idiomas',
		'Id_Estudiante'
	];

	public function estudiante()
	{
		return $this->belongsTo(Estudiante::class, 'Id_Estudiante');
	}
}
