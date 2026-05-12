<?php

namespace App\Http\Controllers\Api\Gerente;

use App\Http\Controllers\Controller;
use App\Models\Gerente;
use App\Models\Pasantia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PasantiaGerenteController extends Controller
{
    private function obtenerGerenteAutenticado(Request $request)
    {
        $usuario = $request->user();

        return Gerente::where('id_usuario', $usuario->id_usuario)->first();
    }

    public function index(Request $request)
    {
        $gerente = $this->obtenerGerenteAutenticado($request);

        if (! $gerente) {
            return response()->json([
                'message' => 'El usuario autenticado no es gerente.',
            ], 403);
        }

        if (! $gerente->id_empresa) {
            return response()->json([
                'message' => 'Primero debe registrar una empresa.',
                'pasantias' => [],
            ], 409);
        }

        $pasantias = Pasantia::with([
            'empresa',
            'jefePasante.usuario',
        ])
            ->where('id_empresa', $gerente->id_empresa)
            ->orderByDesc('id_pasantia')
            ->get();

        return response()->json([
            'pasantias' => $pasantias,
        ]);
    }

    public function store(Request $request)
    {
        $gerente = $this->obtenerGerenteAutenticado($request);

        if (! $gerente) {
            return response()->json([
                'message' => 'El usuario autenticado no es gerente.',
            ], 403);
        }

        if (! $gerente->id_empresa) {
            return response()->json([
                'message' => 'Primero debe registrar una empresa antes de publicar pasantías.',
            ], 409);
        }

        $request->validate([
            'nombre' => 'required|string|max:150',
            'descripcion' => 'required|string|max:2000',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'horario' => 'required|string|max:100',

            'estado' => [
                'nullable',
                Rule::in(['habilitada', 'inhabilitada']),
            ],

            'id_jefe' => [
                'required',
                'integer',
                Rule::exists('jefe_pasante', 'id_usuario')
                    ->where('id_empresa', $gerente->id_empresa),
            ],

            'documento' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
        ]);

        return DB::transaction(function () use ($request, $gerente) {
            $documentoPath = null;
            $documentoNombre = null;

            if ($request->hasFile('documento')) {
                $archivo = $request->file('documento');

                $documentoPath = $archivo->store(
                    'pasantias/documentos',
                    'public'
                );

                $documentoNombre = $archivo->getClientOriginalName();
            }

            $pasantia = Pasantia::create([
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'fecha_inicio' => $request->fecha_inicio,
                'fecha_fin' => $request->fecha_fin,
                'estado' => $request->estado ?? 'habilitada',
                'horario' => $request->horario,
                'id_empresa' => $gerente->id_empresa,
                'id_jefe' => $request->id_jefe,
                'documento_path' => $documentoPath,
                'documento_nombre' => $documentoNombre,
            ]);

            $pasantia->load([
                'empresa',
                'jefePasante.usuario',
            ]);

            return response()->json([
                'message' => 'Pasantía registrada correctamente.',
                'pasantia' => $pasantia,
            ], 201);
        });
    }

    public function show(Request $request, $id)
    {
        $gerente = $this->obtenerGerenteAutenticado($request);

        if (! $gerente) {
            return response()->json([
                'message' => 'El usuario autenticado no es gerente.',
            ], 403);
        }

        $pasantia = Pasantia::with([
            'empresa',
            'jefePasante.usuario',
        ])
            ->where('id_empresa', $gerente->id_empresa)
            ->where('id_pasantia', $id)
            ->first();

        if (! $pasantia) {
            return response()->json([
                'message' => 'Pasantía no encontrada.',
            ], 404);
        }

        return response()->json([
            'pasantia' => $pasantia,
        ]);
    }

    public function update(Request $request, $id)
    {
        $gerente = $this->obtenerGerenteAutenticado($request);

        if (! $gerente) {
            return response()->json([
                'message' => 'El usuario autenticado no es gerente.',
            ], 403);
        }

        $pasantia = Pasantia::where('id_empresa', $gerente->id_empresa)
            ->where('id_pasantia', $id)
            ->first();

        if (! $pasantia) {
            return response()->json([
                'message' => 'Pasantía no encontrada.',
            ], 404);
        }

        $request->validate([
            'nombre' => 'required|string|max:150',
            'descripcion' => 'required|string|max:2000',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'horario' => 'required|string|max:100',

            'estado' => [
                'required',
                Rule::in(['habilitada', 'inhabilitada']),
            ],

            'id_jefe' => [
                'required',
                'integer',
                Rule::exists('jefe_pasante', 'id_usuario')
                    ->where('id_empresa', $gerente->id_empresa),
            ],

            'documento' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
        ]);

        return DB::transaction(function () use ($request, $pasantia) {
            $documentoPath = $pasantia->documento_path;
            $documentoNombre = $pasantia->documento_nombre;

            if ($request->hasFile('documento')) {
                if ($pasantia->documento_path) {
                    Storage::disk('public')->delete($pasantia->documento_path);
                }

                $archivo = $request->file('documento');

                $documentoPath = $archivo->store(
                    'pasantias/documentos',
                    'public'
                );

                $documentoNombre = $archivo->getClientOriginalName();
            }

            $pasantia->update([
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'fecha_inicio' => $request->fecha_inicio,
                'fecha_fin' => $request->fecha_fin,
                'estado' => $request->estado,
                'horario' => $request->horario,
                'id_jefe' => $request->id_jefe,
                'documento_path' => $documentoPath,
                'documento_nombre' => $documentoNombre,
            ]);

            $pasantia->load([
                'empresa',
                'jefePasante.usuario',
            ]);

            return response()->json([
                'message' => 'Pasantía actualizada correctamente.',
                'pasantia' => $pasantia,
            ]);
        });
    }

    public function cambiarEstado(Request $request, $id)
    {
        $gerente = $this->obtenerGerenteAutenticado($request);

        if (! $gerente) {
            return response()->json([
                'message' => 'El usuario autenticado no es gerente.',
            ], 403);
        }

        $request->validate([
            'estado' => [
                'required',
                Rule::in(['habilitada', 'inhabilitada']),
            ],
        ]);

        $pasantia = Pasantia::where('id_empresa', $gerente->id_empresa)
            ->where('id_pasantia', $id)
            ->first();

        if (! $pasantia) {
            return response()->json([
                'message' => 'Pasantía no encontrada.',
            ], 404);
        }

        $pasantia->estado = $request->estado;
        $pasantia->save();

        return response()->json([
            'message' => $request->estado === 'habilitada'
                ? 'Pasantía habilitada correctamente.'
                : 'Pasantía inhabilitada correctamente.',
            'pasantia' => $pasantia,
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $gerente = $this->obtenerGerenteAutenticado($request);

        if (! $gerente) {
            return response()->json([
                'message' => 'El usuario autenticado no es gerente.',
            ], 403);
        }

        $pasantia = Pasantia::where('id_empresa', $gerente->id_empresa)
            ->where('id_pasantia', $id)
            ->first();

        if (! $pasantia) {
            return response()->json([
                'message' => 'Pasantía no encontrada.',
            ], 404);
        }

        if ($pasantia->documento_path) {
            Storage::disk('public')->delete($pasantia->documento_path);
        }

        $pasantia->delete();

        return response()->json([
            'message' => 'Pasantía eliminada correctamente.',
        ]);
    }
}
