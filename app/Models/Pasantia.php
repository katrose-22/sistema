<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pasantia extends Model
{
    protected $table = 'pasantia';
    protected $primaryKey = 'id_pasantia';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'descripcion',
        'fecha_inicio',
        'fecha_fin',
        'estado',
        'horario',
        'id_empresa'
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'id_empresa');
    }
}
