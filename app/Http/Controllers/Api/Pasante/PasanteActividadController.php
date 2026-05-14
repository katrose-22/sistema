<?php

namespace App\Http\Controllers\Api\Pasante;

use App\Http\Controllers\Controller;
use App\Models\Actividad;
use App\Models\BoletaInscripcion;
use App\Models\Pasante;
use Illuminate\Http\Request;

class PasanteActividadController extends Controller
{
    public function index(Request $request)
    {
        $usuario = $request->user();

        $pasante = Pasante::where('id_usuario', $usuario->id_usuario)->first();

        if (! $pasante) {
            return response()->json([
                'message' => 'No se encontró el perfil de pasante',
            ], 404);
        }

        $boleta = BoletaInscripcion::where('id_pasante', $pasante->id_pasante)
            ->latest('id_boleta')
            ->first();

        if (! $boleta) {
            return response()->json([
                'message' => 'No se encontró boleta de inscripción',
            ], 404);
        }

        $actividades = Actividad::where('id_pasantia', $boleta->id_pasantia)
            ->orderBy('fecha_inicio')
            ->get();

        return response()->json([
            'message' => 'Actividades del pasante',
            'actividades' => $actividades,
        ]);
    }

    public function update(Request $request, $id_actividad)
    {
        $request->validate([
            'avance' => ['required', 'integer', 'min:0', 'max:100'],
        ]);

        $usuario = $request->user();

        $pasante = Pasante::where('id_usuario', $usuario->id_usuario)->first();

        if (! $pasante) {
            return response()->json([
                'message' => 'No se encontró el perfil de pasante',
            ], 404);
        }

        $boleta = BoletaInscripcion::where('id_pasante', $pasante->id_pasante)
            ->latest('id_boleta')
            ->first();

        if (! $boleta) {
            return response()->json([
                'message' => 'No se encontró boleta de inscripción',
            ], 404);
        }

        $actividad = Actividad::where('id_actividad', $id_actividad)
            ->where('id_pasantia', $boleta->id_pasantia)
            ->first();

        if (! $actividad) {
            return response()->json([
                'message' => 'Actividad no encontrada o no pertenece al pasante',
            ], 404);
        }

        $actividad->update([
            'avance' => $request->avance,
        ]);

        return response()->json([
            'message' => 'Avance actualizado correctamente',
            'actividad' => $actividad,
        ]);
    }
}
