<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    protected $table = 'empresa';
    protected $primaryKey = 'id_empresa';

    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'direccion',
        'telefono',
        'nit',
    ];

    public function gerente()
    {
        return $this->hasOne(Gerente::class, 'id_empresa', 'id_empresa');
    }

    public function pasantias()
    {
        return $this->hasMany(Pasantia::class, 'id_empresa', 'id_empresa');
    }

    public function encargadosPasantes()
    {
        return $this->hasMany(JefePasante::class, 'id_empresa', 'id_empresa');
    }
}
