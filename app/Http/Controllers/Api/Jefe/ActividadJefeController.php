<?php

namespace App\Http\Controllers\Api\Jefe;

use App\Http\Controllers\Controller;
use App\Models\Actividad;
use App\Models\JefePasante;
use App\Models\Pasantia;
use Illuminate\Http\Request;

class ActividadJefeController extends Controller
{
    private function obtenerJefeAutenticado(Request $request)
    {
        $usuario = $request->user();

        return JefePasante::where('id_usuario', $usuario->id_usuario)->first();
    }

    private function verificarPasantiaAsignada($idPasantia, $idJefe)
    {
        return Pasantia::where('id_pasantia', $idPasantia)
            ->where('id_jefe', $idJefe)
            ->first();
    }

    public function pasantiasAsignadas(Request $request)
    {
        $jefe = $this->obtenerJefeAutenticado($request);

        if (!$jefe) {
            return response()->json([
                'message' => 'El usuario autenticado no es encargado de pasante.'
            ], 403);
        }

        $pasantias = Pasantia::with(['empresa', 'actividades'])
            ->where('id_jefe', $jefe->id_usuario)
            ->orderByDesc('id_pasantia')
            ->get();

        return response()->json([
            'pasantias' => $pasantias
        ]);
    }

    public function index(Request $request)
    {
        $jefe = $this->obtenerJefeAutenticado($request);

        if (!$jefe) {
            return response()->json([
                'message' => 'El usuario autenticado no es encargado de pasante.'
            ], 403);
        }

        $pasantiasIds = Pasantia::where('id_jefe', $jefe->id_usuario)
            ->pluck('id_pasantia');

        if ($pasantiasIds->isEmpty()) {
            return response()->json([
                'message' => 'No tienes pasantías asignadas.',
                'actividades' => []
            ]);
        }

        $actividades = Actividad::with('pasantia')
            ->whereIn('id_pasantia', $pasantiasIds)
            ->orderByDesc('id_actividad')
            ->get();

        return response()->json([
            'actividades' => $actividades
        ]);
    }

    public function store(Request $request)
    {
        $jefe = $this->obtenerJefeAutenticado($request);

        if (!$jefe) {
            return response()->json([
                'message' => 'El usuario autenticado no es encargado de pasante.'
            ], 403);
        }

        $request->validate([
            'titulo' => 'required|string|max:150',
            'descripcion' => 'required|string|max:2000',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'id_pasantia' => 'required|integer|exists:pasantia,id_pasantia',
        ]);

        $pasantia = $this->verificarPasantiaAsignada(
            $request->id_pasantia,
            $jefe->id_usuario
        );

        if (!$pasantia) {
            return response()->json([
                'message' => 'No puedes crear actividades en una pasantía que no tienes asignada.'
            ], 403);
        }

        $actividad = Actividad::create([
            'titulo' => $request->titulo,
            'descripcion' => $request->descripcion,
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin,
            'id_pasantia' => $request->id_pasantia,
        ]);

        $actividad->load('pasantia');

        return response()->json([
            'message' => 'Actividad registrada correctamente.',
            'actividad' => $actividad
        ], 201);
    }

    public function show(Request $request, $id)
    {
        $jefe = $this->obtenerJefeAutenticado($request);

        if (!$jefe) {
            return response()->json([
                'message' => 'El usuario autenticado no es encargado de pasante.'
            ], 403);
        }

        $actividad = Actividad::with('pasantia')
            ->where('id_actividad', $id)
            ->first();

        if (!$actividad) {
            return response()->json([
                'message' => 'Actividad no encontrada.'
            ], 404);
        }

        $pasantia = $this->verificarPasantiaAsignada(
            $actividad->id_pasantia,
            $jefe->id_usuario
        );

        if (!$pasantia) {
            return response()->json([
                'message' => 'No tienes permiso para ver esta actividad.'
            ], 403);
        }

        return response()->json([
            'actividad' => $actividad
        ]);
    }

    public function update(Request $request, $id)
    {
        $jefe = $this->obtenerJefeAutenticado($request);

        if (!$jefe) {
            return response()->json([
                'message' => 'El usuario autenticado no es encargado de pasante.'
            ], 403);
        }

        $actividad = Actividad::where('id_actividad', $id)->first();

        if (!$actividad) {
            return response()->json([
                'message' => 'Actividad no encontrada.'
            ], 404);
        }

        $pasantiaActual = $this->verificarPasantiaAsignada(
            $actividad->id_pasantia,
            $jefe->id_usuario
        );

        if (!$pasantiaActual) {
            return response()->json([
                'message' => 'No tienes permiso para editar esta actividad.'
            ], 403);
        }

        $request->validate([
            'titulo' => 'required|string|max:150',
            'descripcion' => 'required|string|max:2000',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'id_pasantia' => 'required|integer|exists:pasantia,id_pasantia',
        ]);

        $nuevaPasantia = $this->verificarPasantiaAsignada(
            $request->id_pasantia,
            $jefe->id_usuario
        );

        if (!$nuevaPasantia) {
            return response()->json([
                'message' => 'No puedes mover la actividad a una pasantía que no tienes asignada.'
            ], 403);
        }

        $actividad->update([
            'titulo' => $request->titulo,
            'descripcion' => $request->descripcion,
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin,
            'id_pasantia' => $request->id_pasantia,
        ]);

        $actividad->load('pasantia');

        return response()->json([
            'message' => 'Actividad actualizada correctamente.',
            'actividad' => $actividad
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $jefe = $this->obtenerJefeAutenticado($request);

        if (!$jefe) {
            return response()->json([
                'message' => 'El usuario autenticado no es encargado de pasante.'
            ], 403);
        }

        $actividad = Actividad::where('id_actividad', $id)->first();

        if (!$actividad) {
            return response()->json([
                'message' => 'Actividad no encontrada.'
            ], 404);
        }

        $pasantia = $this->verificarPasantiaAsignada(
            $actividad->id_pasantia,
            $jefe->id_usuario
        );

        if (!$pasantia) {
            return response()->json([
                'message' => 'No tienes permiso para eliminar esta actividad.'
            ], 403);
        }

        $actividad->delete();

        return response()->json([
            'message' => 'Actividad eliminada correctamente.'
        ]);
    }
}