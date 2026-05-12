<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Usuario extends Authenticatable
{
    use HasApiTokens;

    protected $table = 'usuario';

    protected $primaryKey = 'id_usuario';

    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'apellido',
        'email',
        'contrasena',
        'telefono',
        'id_rol',
    ];

    protected $hidden = [
        'contrasena',
    ];

    public function getAuthPassword()
    {
        return $this->contrasena;
    }

    public function pasante()
    {
        return $this->hasOne(Pasante::class, 'id_usuario', 'id_usuario');
    }

    public function gerente()
    {
        return $this->hasOne(Gerente::class, 'id_usuario', 'id_usuario');
    }

    public function rol()
    {
        return $this->belongsTo(Rol::class, 'id_rol', 'id_rol');
    }
}
