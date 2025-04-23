<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Sector
 * 
 * @property int $Id_Sector
 * @property string|null $Nombre
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Collection|Empresa[] $empresas
 *
 * @package App\Models
 */
class Sector extends Model
{
	protected $table = 'sector';
	protected $primaryKey = 'Id_Sector';

	protected $fillable = [
		'Nombre'
	];

	public function empresas()
	{
		return $this->hasMany(Empresa::class, 'Id_Sector');
	}
}
