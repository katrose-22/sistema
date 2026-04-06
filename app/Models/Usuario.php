<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    protected $table = 'usuario';
    protected $primaryKey = 'id_usuario';

    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'apellido',
        'email',
        'contrasena',
        'telefono'
    ];


    public function pasante()
{
    return $this->hasOne(Pasante::class, 'id_usuario');
}

public function tutor()
{
    return $this->hasOne(Tutor::class, 'id_usuario');
}

public function gerente()
{
    return $this->hasOne(Gerente::class, 'id_usuario');
}

public function jefe()
{
    return $this->hasOne(JefePasante::class, 'id_usuario');
}
}