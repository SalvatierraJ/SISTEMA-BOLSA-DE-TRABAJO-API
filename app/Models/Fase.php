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
 * @property string $Titulo
 * @property string|null $Archivo
 * @property string $Etapa
 * @property int $Id_Postulacion
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Postulacion $postulacion
 *
 * @package App\Models
 */
class Fase extends Model
{
	protected $table = 'fase';
	protected $primaryKey = 'Id_Fase';

	protected $casts = [
		'Id_Postulacion' => 'int'
	];

	protected $fillable = [
		'Titulo',
		'Archivo',
		'Etapa',
		'Id_Postulacion'
	];

	public function postulacion()
	{
		return $this->belongsTo(Postulacion::class, 'Id_Postulacion');
	}
}
