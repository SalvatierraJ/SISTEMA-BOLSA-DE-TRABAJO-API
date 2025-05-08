<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Testimonio
 *
 * @property int $Id_Testimonio
 * @property string|null $Comentario
 * @property int|null $Id_Usuario
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property Usuario|null $usuario
 *
 * @package App\Models
 */
class Testimonio extends Model
{
	protected $table = 'testimonio';
	protected $primaryKey = 'Id_Testimonio';

	protected $casts = [
		'Id_Usuario' => 'int'
	];

	protected $fillable = [
		'Comentario',
		'Id_Usuario',
        'Estado',
        'Titulo'
	];

	public function usuario()
	{
		return $this->belongsTo(Usuario::class, 'Id_Usuario');
	}
}
