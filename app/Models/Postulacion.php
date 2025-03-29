<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Postulacion
 * 
 * @property int $Id_Postulacion
 * @property Carbon|null $Fecha_Postulacion
 * @property string|null $Estado
 * @property int|null $Id_Estudiante
 * @property int|null $Id_Trabajo
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Estudiante|null $estudiante
 * @property Trabajo|null $trabajo
 * @property Collection|Fase[] $fases
 *
 * @package App\Models
 */
class Postulacion extends Model
{
	protected $table = 'postulacion';
	protected $primaryKey = 'Id_Postulacion';

	protected $casts = [
		'Fecha_Postulacion' => 'datetime',
		'Id_Estudiante' => 'int',
		'Id_Trabajo' => 'int'
	];

	protected $fillable = [
		'Fecha_Postulacion',
		'Estado',
		'Id_Estudiante',
		'Id_Trabajo'
	];

	public function estudiante()
	{
		return $this->belongsTo(Estudiante::class, 'Id_Estudiante');
	}

	public function trabajo()
	{
		return $this->belongsTo(Trabajo::class, 'Id_Trabajo');
	}

	public function fases()
	{
		return $this->hasMany(Fase::class, 'Id_Postulacion');
	}
}
