<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pasante extends Model
{
    protected $table = 'pasante';

    protected $primaryKey = 'id_pasante';

    public $timestamps = false;

    protected $fillable = [
        'ci',
        'reg_universitario',
        'direccion',
        'telefono',
        'id_usuario',
        'id_institucion',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }

    public function institucion()
    {
        return $this->belongsTo(InstitucionAcademica::class, 'id_institucion');
    }

    public function boletas()
    {
        return $this->hasMany(BoletaInscripcion::class, 'id_pasante');
    }

    public function comentarios()
    {
        return $this->hasMany(Comentario::class, 'id_pasante');
    }

>>>>>>> 0be9f3a1ff655362e4e010026a5f6d36b2e4dfd1
    public function hojaVida()
    {
        return $this->hasOne(HojaVida::class, 'id_pasante');
    }
}
