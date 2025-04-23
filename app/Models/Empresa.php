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
 * @property int|null $Id_Telefono
 * @property int|null $Id_Usuario
 * @property int|null $Id_Sector
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Telefono|null $telefono
 * @property Usuario|null $usuario
 * @property Sector|null $sector
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
		'Id_Telefono' => 'int',
		'Id_Usuario' => 'int',
		'Id_Sector' => 'int'
	];

	protected $fillable = [
		'NIT',
		'Nombre',
		'Direccion',
		'Direccion_Web',
		'Correo',
		'Id_Telefono',
		'Id_Usuario',
		'Id_Sector'
	];

	public function telefono()
	{
		return $this->belongsTo(Telefono::class, 'Id_Telefono');
	}

	public function usuario()
	{
		return $this->belongsTo(Usuario::class, 'Id_Usuario');
	}

	public function sector()
	{
		return $this->belongsTo(Sector::class, 'Id_Sector');
	}

	public function trabajos()
	{
		return $this->hasMany(Trabajo::class, 'Id_Empresa');
	}
}
