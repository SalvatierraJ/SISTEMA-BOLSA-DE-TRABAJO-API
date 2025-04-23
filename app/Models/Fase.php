<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Fase
 * 
 * @property int $Id_Fase
 * @property string|null $Titulo
 * @property int|null $Archivo
 * @property int|null $Resultado
 * @property string|null $Etapa
 * @property int|null $Id_Postulacion
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Postulacion|null $postulacion
 *
 * @package App\Models
 */
class Fase extends Model
{
	protected $table = 'fase';
	protected $primaryKey = 'Id_Fase';

	protected $casts = [
		'Archivo' => 'int',
		'Resultado' => 'int',
		'Id_Postulacion' => 'int'
	];

	protected $fillable = [
		'Titulo',
		'Archivo',
		'Resultado',
		'Etapa',
		'Id_Postulacion'
	];

	public function postulacion()
	{
		return $this->belongsTo(Postulacion::class, 'Id_Postulacion');
	}
}
