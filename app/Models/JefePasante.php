<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JefePasante extends Model
{
    protected $table = 'jefe_pasante';
    protected $primaryKey = 'id_usuario';

    public $incrementing = false;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'id_usuario',
        'cargo',
        'telefono',
        'id_empresa',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'id_empresa', 'id_empresa');
    }

    public function pasantias()
    {
        return $this->hasMany(Pasantia::class, 'id_jefe', 'id_usuario');
    }
}