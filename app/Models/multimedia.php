<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Multimedia
 * 
 * @property int $id_multimedia
 * @property int|null $id_estudiante
 * @property int|null $id_empresa
 * @property int|null $id_trabajo
 * @property string|null $tipo
 * @property string|null $direccion
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * 
 * @property Estudiante|null $estudiante
 * @property Empresa|null $empresa
 * @property Trabajo|null $trabajo
 *
 * @package App\Models
 */
class Multimedia extends Model
{
	protected $table = 'multimedia';
	protected $primaryKey = 'id_multimedia';

	protected $casts = [
		'id_estudiante' => 'int',
		'id_empresa' => 'int',
		'id_trabajo' => 'int'
	];

	protected $fillable = [
		'id_estudiante',
		'id_empresa',
		'id_trabajo',
		'tipo',
		'direccion'
	];

	public function estudiante()
	{
		return $this->belongsTo(Estudiante::class, 'id_estudiante');
	}

	public function empresa()
	{
		return $this->belongsTo(Empresa::class, 'id_empresa');
	}

	public function trabajo()
	{
		return $this->belongsTo(Trabajo::class, 'id_trabajo');
	}
}
