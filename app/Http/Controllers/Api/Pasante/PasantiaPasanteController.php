<?php

namespace App\Http\Controllers\Api\Pasante;

use App\Http\Controllers\Controller;
use App\Models\Pasantia;
use Illuminate\Http\Request;

use App\Models\Pasantia;
use Illuminate\Http\Request;



class PasantiaPasanteController extends Controller
{
    public function index()
    {
        $pasantias = Pasantia::with('empresa')
            ->where('estado', 'habilitada')
            ->orderBy('id_pasantia', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Pasantías disponibles obtenidas correctamente',
            'data' => $pasantias
        ]);
    }

    public function show($id)
    {
        $pasantia = Pasantia::with('empresa')
            ->where('id_pasantia', $id)
            ->first();

        if (!$pasantia) {
            return response()->json([
                'success' => false,
                'message' => 'Pasantía no encontrada'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $pasantia
        ]);
    }
}
