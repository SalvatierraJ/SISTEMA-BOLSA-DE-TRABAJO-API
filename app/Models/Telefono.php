<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Telefono
 * 
 * @property int $Id_Telefono
 * @property int|null $numero
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Collection|Empresa[] $empresas
 * @property Collection|Persona[] $personas
 *
 * @package App\Models
 */
class Telefono extends Model
{
	protected $table = 'telefono';
	protected $primaryKey = 'Id_Telefono';

	protected $casts = [
		'Numero' => 'int'
	];

	protected $fillable = [
		'Numero'
	];

	public function empresas()
	{
		return $this->hasMany(Empresa::class, 'Id_Telefono');
	}

	public function personas()
	{
		return $this->hasMany(Persona::class, 'Id_Telefono');
	}
}
