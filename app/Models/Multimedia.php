<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Multimedia
 *
 * @property int $Id_Multimedia
 * @property int|null $Id_Usuario
 * @property int|null $Id_Trabajo
 * @property string|null $Titulo
 * @property string|null $Descripcion
 * @property string|null $Nombre
 * @property string|null $Tipo
 * @property string|null $Estado
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property Usuario|null $usuario
 * @property Trabajo|null $trabajo
 *
 * @package App\Models
 */
class Multimedia extends Model
{
	protected $table = 'multimedia';
	protected $primaryKey = 'Id_Multimedia';

	protected $casts = [
		'Id_Usuario' => 'int',
		'Id_Trabajo' => 'int'
	];

	protected $fillable = [
		'Id_Usuario',
		'Id_Trabajo',
		'Titulo',
		'Descripcion',
		'Nombre',
		'Tipo',
		'Estado'
	];

	public function usuario()
	{
		return $this->belongsTo(Usuario::class, 'Id_Usuario');
	}

	public function trabajo()
	{
		return $this->belongsTo(Trabajo::class, 'Id_Trabajo');
	}
}
