<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class multimedia extends Model
{
    protected $table = "multimedia";
    protected $fillable = [
        'id_empresa',
        'id_estudiante',
    ];
}