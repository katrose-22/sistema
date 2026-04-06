<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InstitucionAcademica extends Model
{
    protected $table = 'institucion_academica';
    protected $primaryKey = 'id_institucion';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'direccion',
        'telefono'
    ];

    public function pasantes()
    {
        return $this->hasMany(Pasante::class, 'id_institucion');
    }
}
