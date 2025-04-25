<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Telefono
 *
 * @property int $Id_Telefono
 * @property int|null $numero
 * @property int|null $Id_Persona
 * @property int|null $Id_Empresa
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property Persona|null $persona
 * @property Empresa|null $empresa
 *
 * @package App\Models
 */
class Telefono extends Model
{
	protected $table = 'telefono';
	protected $primaryKey = 'Id_Telefono';

	protected $casts = [
		'numero' => 'int',
		'Id_Persona' => 'int',
		'Id_Empresa' => 'int'
	];

	protected $fillable = [
		'numero',
		'Id_Persona',
		'Id_Empresa'
	];

	public function persona()
	{
		return $this->belongsTo(Persona::class, 'Id_Persona');
	}

	public function empresa()
	{
		return $this->belongsTo(Empresa::class, 'Id_Empresa');
	}
}
