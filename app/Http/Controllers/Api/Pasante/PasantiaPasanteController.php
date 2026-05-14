<?php

namespace App\Http\Controllers\Api\Pasante;

use App\Http\Controllers\Controller;
use App\Models\Pasantia;
use Illuminate\Http\Request;

class PasantiaPasanteController extends Controller
{
    public function index(Request $request)
    {
        $pasantias = Pasantia::with(['empresa', 'jefePasante'])
            ->where('estado', 'habilitada')
            ->orderBy('fecha_inicio', 'desc')
            ->get();

        return response()->json([
            'message' => 'Listado de pasantías disponibles',
            'pasantias' => $pasantias,
        ]);
    }

    public function show($id_pasantia)
    {
        $pasantia = Pasantia::with(['empresa', 'jefePasante', 'actividades'])
            ->where('id_pasantia', $id_pasantia)
            ->first();

        if (! $pasantia) {
            return response()->json([
                'message' => 'Pasantía no encontrada',
            ], 404);
        }

        return response()->json([
            'message' => 'Detalle de la pasantía',
            'pasantia' => $pasantia,
        ]);
    }
}
