<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bitacora extends Model
{
    protected $table = 'bitacora';
    protected $primaryKey = 'id_bitacora';
    public $timestamps = false;

    protected $fillable = [
        'fecha',
        'observacion',
        'porcentaje',
        'id_boleta',
        'id_actividad',
        'id_jefe'
    ];
}
