<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BoletaInscripcion extends Model
{
    protected $table = 'boleta_inscripcion';

    protected $primaryKey = 'id_boleta';

    public $timestamps = false;

    protected $fillable = [
        'fecha',
        'descripcion',
        'id_pasante',
        'id_tutor',
        'id_pasantia',
        'id_jefe',
    ];

    public function pasante()
    {
        return $this->belongsTo(Pasante::class, 'id_pasante');
    }

    public function tutor()
    {
        return $this->belongsTo(Tutor::class, 'id_tutor');
    }

    public function pasantia()
    {
        return $this->belongsTo(Pasantia::class, 'id_pasantia');
    }

    public function jefe()
    {
        return $this->belongsTo(JefePasante::class, 'id_jefe');
    }
}
