<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Empresa
 * 
 * @property int $Id_Empresa
 * @property int|null $NIT
 * @property string|null $Nombre
 * @property string|null $Direccion
 * @property string|null $Direccion_Web
 * @property string|null $Correo
 * @property int|null $Id_Usuario
 * @property int|null $Id_Sector
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Usuario|null $usuario
 * @property Sector|null $sector
 * @property Collection|Telefono[] $telefonos
 * @property Collection|Trabajo[] $trabajos
 *
 * @package App\Models
 */
class Empresa extends Model
{
	protected $table = 'empresa';
	protected $primaryKey = 'Id_Empresa';

	protected $casts = [
		'NIT' => 'int',
		'Id_Usuario' => 'int',
		'Id_Sector' => 'int'
	];

	protected $fillable = [
		'NIT',
		'Nombre',
		'Direccion',
		'Direccion_Web',
		'Correo',
		'Id_Usuario',
		'Id_Sector'
	];

	public function usuario()
	{
		return $this->belongsTo(Usuario::class, 'Id_Usuario');
	}

	public function sector()
	{
		return $this->belongsTo(Sector::class, 'Id_Sector');
	}

	public function telefonos()
	{
		return $this->hasMany(Telefono::class, 'Id_Empresa');
	}

	public function trabajos()
	{
		return $this->hasMany(Trabajo::class, 'Id_Empresa');
	}
}