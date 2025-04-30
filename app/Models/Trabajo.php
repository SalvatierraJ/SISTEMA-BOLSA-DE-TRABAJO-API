<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Trabajo
 * 
 * @property int $Id_Trabajo
 * @property string|null $Titulo
 * @property string|null $Descripcion
 * @property array|null $Requisitos
 * @property string|null $Competencia
 * @property string|null $Ubicacion
 * @property float|null $Salario
 * @property string|null $Modalidad
 * @property Carbon|null $Fecha_Inicio
 * @property Carbon|null $Fecha_Fin
 * @property string|null $Duracion
 * @property string|null $Estado
 * @property string|null $Tipo_Trabajo
 * @property int|null $Id_Empresa
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Empresa|null $empresa
 * @property Collection|Multimedia[] $multimedia
 * @property Collection|Postulacion[] $postulacions
 *
 * @package App\Models
 */
class Trabajo extends Model
{
	protected $table = 'trabajo';
	protected $primaryKey = 'Id_Trabajo';

	protected $casts = [
		'Requisitos' => 'json',
		'Salario' => 'float',
		'Fecha_Inicio' => 'datetime',
		'Fecha_Fin' => 'datetime',
		'Tipo_Trabajo' => 'string', //Daba un error que el tipo de dato binary
		'Id_Empresa' => 'int'
	];

	protected $fillable = [
		'Titulo',
		'Descripcion',
		'Requisitos',
		'Competencia',
		'Ubicacion',
		'Salario',
		'Modalidad',
		'Fecha_Inicio',
		'Fecha_Fin',
		'Duracion',
		'Estado',
		'Tipo_Trabajo',
		'Id_Empresa'
	];

	public function empresa()
	{
		return $this->belongsTo(Empresa::class, 'Id_Empresa');
	}

	public function multimedia()
	{
		return $this->hasMany(Multimedia::class, 'Id_Trabajo');
	}

	public function postulacions()
	{
		return $this->hasMany(Postulacion::class, 'Id_Trabajo');
	}
}