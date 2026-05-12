<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Pasantia extends Model
{
    protected $table = 'pasantia';
    protected $primaryKey = 'id_pasantia';

    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'descripcion',
        'fecha_inicio',
        'fecha_fin',
        'estado',
        'horario',
        'id_empresa',
        'id_jefe',
        'documento_path',
        'documento_nombre',
    ];

    protected $appends = [
        'documento_url',
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'id_empresa', 'id_empresa');
    }

    public function jefePasante()
    {
        return $this->belongsTo(JefePasante::class, 'id_jefe', 'id_usuario');
    }

    public function actividades()
    {
        return $this->hasMany(Actividad::class, 'id_pasantia', 'id_pasantia');
    }

    public function getDocumentoUrlAttribute()
    {
        if (!$this->documento_path) {
            return null;
        }

        return Storage::url($this->documento_path);
    }
    
}