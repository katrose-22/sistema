<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gerente extends Model
{
    protected $table = 'gerente';

    protected $primaryKey = 'id_usuario';

    public $incrementing = false;

    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'id_usuario',
        'cargo',
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
}
