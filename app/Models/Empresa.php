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
        'nit'
    ];
    public function gerentes()
{
    return $this->hasMany(Gerente::class, 'id_empresa');
}

public function pasantias()
{
    return $this->hasMany(Pasantia::class, 'id_empresa');
}
}