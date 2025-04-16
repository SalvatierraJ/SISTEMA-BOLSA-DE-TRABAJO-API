<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Testimonio
 * 
 * @property int $Id_Testimonio
 * @property int $Id_Estudiante
 * @property string|null $Comentario
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Estudiante $estudiante
 *
 * @package App\Models
 */
class Testimonio extends Model
{
	protected $table = 'testimonios';
	protected $primaryKey = 'Id_Testimonio';

	protected $casts = [
		'Id_Estudiante' => 'int'
	];

	protected $fillable = [
		'Id_Estudiante',
		'Comentario'
	];

	public function estudiante()
	{
		return $this->belongsTo(Estudiante::class, 'Id_Estudiante');
	}
}
