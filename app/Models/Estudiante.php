<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Estudiante
 * 
 * @property int $Id_Estudiante
 * @property string $Nro_Registro
 * @property string $Carnet
 * @property string $Nombre
 * @property string $Apellido
 * @property string $Correo
 * @property string|null $Carrera
 * @property int|null $Id_Usuario
 * @property int|null $Id_Testimonio
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Usuario|null $usuario
 * @property Testimonio|null $testimonio
 * @property Curriculum|null $curriculum
 * @property Collection|Postulacion[] $postulacions
 *
 * @package App\Models
 */
class Estudiante extends Model
{
	protected $table = 'estudiante';
	protected $primaryKey = 'Id_Estudiante';

	protected $casts = [
		'Id_Usuario' => 'int',
		'Id_Testimonio' => 'int'
	];

	protected $fillable = [
		'Nro_Registro',
		'Carnet',
		'Nombre',
		'Apellido',
		'Correo',
		'Carrera',
		'Id_Usuario',
		'Id_Testimonio'
	];

	public function usuario()
	{
		return $this->belongsTo(Usuario::class, 'Id_Usuario');
	}

	public function testimonio()
	{
		return $this->belongsTo(Testimonio::class, 'Id_Testimonio');
	}

	public function curriculum()
	{
		return $this->hasOne(Curriculum::class, 'Id_Estudiante');
	}

	public function postulacions()
	{
		return $this->hasMany(Postulacion::class, 'Id_Estudiante');
	}
}
