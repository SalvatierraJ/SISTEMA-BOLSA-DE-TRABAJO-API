<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Testimonio
 * 
 * @property int $Id_Testimonio
 * @property string|null $Comentario
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Collection|Estudiante[] $estudiantes
 *
 * @package App\Models
 */
class Testimonio extends Model
{
	protected $table = 'testimonios';
	protected $primaryKey = 'Id_Testimonio';

	protected $fillable = [
		'Comentario'
	];

	public function estudiantes()
	{
		return $this->hasMany(Estudiante::class, 'Id_Testimonio');
	}
}
