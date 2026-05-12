<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HojaVida extends Model
{
    protected $table = 'hoja_vida';

    protected $primaryKey = 'id_hv';

    public $timestamps = false;

    protected $fillable = [
        'habilidades',
        'id_pasante',
    ];
}
