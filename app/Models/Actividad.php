<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Actividad extends Model
{
    protected $table = 'actividad';
    protected $primaryKey = 'id_actividad';

    public $timestamps = false;

    protected $fillable = [
        'descripcion',
        'titulo',
        'fecha_inicio',
        'fecha_fin',
        'id_pasantia',
    ];

    public function pasantia()
    {
        return $this->belongsTo(Pasantia::class, 'id_pasantia', 'id_pasantia');
    }
}