<?php

namespace App\Http\Controllers\Api\Pasante;

use App\Http\Controllers\Controller;
use App\Models\BoletaInscripcion;
use App\Models\Pasante;
use App\Models\Pasantia;
use Illuminate\Http\Request;

class BoletaPasanteController extends Controller
{
    public function store(Request $request, $id_pasantia)
    {
        $usuario = $request->user();

        $pasante = Pasante::where('id_usuario', $usuario->id_usuario)->first();

        if (! $pasante) {
            return response()->json([
                'message' => 'Perfil de pasante no encontrado',
            ], 404);
        }

        $pasantia = Pasantia::where('id_pasantia', $id_pasantia)->first();

        if (! $pasantia) {
            return response()->json([
                'message' => 'Pasantía no encontrada',
            ], 404);
        }

        $boletaExistente = BoletaInscripcion::where('id_pasante', $pasante->id_pasante)
            ->where('id_pasantia', $id_pasantia)
            ->first();

        if ($boletaExistente) {
            return response()->json([
                'message' => 'El pasante ya está inscrito en esta pasantía',
            ], 409);
        }

        $boleta = BoletaInscripcion::create([
            'fecha' => now()->format('Y-m-d'),
            'descripcion' => 'Inscripción inicial',
            'id_pasante' => $pasante->id_pasante,
            'id_pasantia' => $id_pasantia,
            'id_tutor' => null,
            'id_jefe' => $pasantia->id_jefe,
        ]);

        return response()->json([
            'message' => 'Inscripción a pasantía completada correctamente',
            'boleta' => $boleta->load(['pasante', 'pasantia', 'jefe']),
        ], 201);
    }

    public function show(Request $request)
    {
        $usuario = $request->user();

        $pasante = Pasante::where('id_usuario', $usuario->id_usuario)->first();

        if (! $pasante) {
            return response()->json([
                'message' => 'Perfil de pasante no encontrado',
            ], 404);
        }

        $boleta = BoletaInscripcion::where('id_pasante', $pasante->id_pasante)
            ->latest('id_boleta')
            ->first();

        if (! $boleta) {
            return response()->json([
                'message' => 'No existe boleta de inscripción',
            ], 404);
        }

        return response()->json([
            'message' => 'Boleta de inscripción',
            'boleta' => $boleta->load(['pasante', 'pasantia', 'tutor', 'jefe']),
        ]);
    }
}
