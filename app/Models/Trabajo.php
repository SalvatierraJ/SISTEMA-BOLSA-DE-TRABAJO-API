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
 * @property string $Titulo
 * @property string|null $Descripcion
 * @property string|null $Requisitos
 * @property string|null $Competencias
 * @property string|null $Ubicacion
 * @property float|null $Salario
 * @property string|null $Categoria
 * @property string $Modalidad
 * @property Carbon|null $Fecha_Inicio
 * @property Carbon|null $Fecha_Final
 * @property int|null $Duracion
 * @property string $Tipo
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
		'Salario' => 'float',
		'Fecha_Inicio' => 'datetime',
		'Fecha_Final' => 'datetime',
		'Duracion' => 'int',
		'Id_Empresa' => 'int'
	];

	protected $fillable = [
		'Titulo',
		'Descripcion',
		'Requisitos',
		'Competencias',
		'Ubicacion',
		'Salario',
		'Categoria',
		'Modalidad',
		'Fecha_Inicio',
		'Fecha_Final',
		'Duracion',
		'Tipo',
		'Id_Empresa'
	];

	public function empresa()
	{
		return $this->belongsTo(Empresa::class, 'Id_Empresa');
	}

	public function multimedia()
	{
		return $this->hasMany(Multimedia::class, 'id_trabajo');
	}

	public function postulacions()
	{
		return $this->hasMany(Postulacion::class, 'Id_Trabajo');
	}
}
