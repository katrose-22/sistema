<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JefePasante extends Model
{
    protected $table = 'jefe_pasante';
    protected $primaryKey = 'id_usuario';
    public $timestamps = false;

    protected $fillable = [
        'id_usuario',
        'cargo',
        'telefono'
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }
}