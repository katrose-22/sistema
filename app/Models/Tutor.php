<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tutor extends Model
{
    protected $table = 'tutor';
    protected $primaryKey = 'id_usuario';
    public $timestamps = false;

    protected $fillable = [
        'id_usuario',
        'codigo_doc'
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }
}
