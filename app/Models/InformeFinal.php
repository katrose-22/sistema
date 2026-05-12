<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InformeFinal extends Model
{
    protected $table = 'informe_final';

    protected $primaryKey = 'id_informe';

    public $timestamps = false;

    protected $fillable = [
        'fecha_entrega',
        'observacion',
        'descripcion',
        'nota',
        'id_usuario',
        'id_boleta',
    ];
}
