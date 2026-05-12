<?php

namespace App\Http\Controllers\Api\Pasante;

use App\Http\Controllers\Controller;
use App\Models\BoletaInscripcion;
use App\Models\Pasante;
use Illuminate\Http\Request;

class PasanteDashboardController extends Controller
{
    public function index(Request $request)
    {
        $usuario = $request->user();

        $pasante = Pasante::with(['usuario', 'institucion', 'hojaVida'])
            ->where('id_usuario', $usuario->id_usuario)
            ->first();

        if (! $pasante) {
            return response()->json([
                'message' => 'No se encontró el perfil de pasante',
            ], 404);
        }

        $boleta = BoletaInscripcion::where('id_pasante', $pasante->id_pasante)
            ->latest('id_boleta')
            ->first();

        return response()->json([
            'message' => 'Dashboard pasante',
            'pasante' => $pasante,
            'resumen' => [
                'tiene_hoja_vida' => $pasante->hojaVida ? true : false,
                'tiene_boleta' => $boleta ? true : false,
            ],
            'boleta' => $boleta,
        ]);
    }
}
