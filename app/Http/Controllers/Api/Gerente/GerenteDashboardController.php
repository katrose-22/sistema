<?php

namespace App\Http\Controllers\Api\Gerente;

use App\Http\Controllers\Controller;
use App\Models\Gerente;
use App\Models\Pasantia;
use Illuminate\Http\Request;

class GerenteDashboardController extends Controller
{
    public function index(Request $request)
    {
        $usuario = $request->user();

        $gerente = Gerente::with(['usuario', 'empresa'])
            ->where('id_usuario', $usuario->id_usuario)
            ->first();

        if (!$gerente) {
            return response()->json([
                'message' => 'No se encontró el perfil de gerente'
            ], 404);
        }

        $totalPasantias = Pasantia::where('id_empresa', $gerente->id_empresa)->count();

        $pasantiasActivas = Pasantia::where('id_empresa', $gerente->id_empresa)
            ->where('estado', 'activa')
            ->count();

        return response()->json([
            'message' => 'Dashboard gerente',
            'gerente' => $gerente,
            'resumen' => [
                'total_pasantias' => $totalPasantias,
                'pasantias_activas' => $pasantiasActivas,
            ]
        ]);
    }

    public function perfil(Request $request)
    {
        $usuario = $request->user();

        $gerente = Gerente::with(['usuario', 'empresa'])
            ->where('id_usuario', $usuario->id_usuario)
            ->first();

        return response()->json([
            'gerente' => $gerente
        ]);
    }
}