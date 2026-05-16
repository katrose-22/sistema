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
        'documento_blob',
        'documento_mime',
        'documento_nombre',
    ];

    protected $hidden = [
        'documento_blob',
    ];

    protected $appends = [
        'documento_base64',
    ];

    public function getDocumentoBase64Attribute(): ?string
    {
        if ($this->documento_blob === null) {
            return null;
        }

        // SOLUCIÓN: En PostgreSQL los campos binarios se devuelven como "streams" (recursos).
        // Debemos leer el contenido del recurso antes de codificar a Base64.
        $blob = is_resource($this->documento_blob) 
            ? stream_get_contents($this->documento_blob) 
            : $this->documento_blob;

        return base64_encode((string) $blob);
    }
}