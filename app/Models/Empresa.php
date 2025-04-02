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
 * @property string $Nombre
 * @property string|null $Sector
 * @property string $Correo
 * @property string|null $Direccion
 * @property string|null $Contacto
 * @property string|null $Direccion_Web
 * @property int|null $Id_Usuario
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property Usuario|null $usuario
 * @property Collection|Trabajo[] $trabajos
 *
 * @package App\Models
 */
class Empresa extends Model
{
	protected $table = 'empresas';
	protected $primaryKey = 'Id_Empresa';

	protected $casts = [
		'Id_Usuario' => 'int'
	];

	protected $fillable = [
		'Nombre',
		'Sector',
		'Correo',
		'Direccion',
		'Contacto',
		'Direccion_Web',
        'logotipo',
		'Id_Usuario'
	];

	public function usuario()
	{
		return $this->belongsTo(Usuario::class, 'Id_Usuario');
	}

	public function trabajos()
	{
		return $this->hasMany(Trabajo::class, 'Id_Empresa');
	}
}
