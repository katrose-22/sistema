<?php

namespace App\Http\Controllers\Api\Pasante;

use App\Http\Controllers\Controller;
use App\Models\HojaVida;
use App\Models\Pasante;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HojaVidaController extends Controller
{
    private function obtenerPasanteAutenticado(Request $request): ?Pasante
    {
        $usuario = $request->user();
        return Pasante::where('id_usuario', $usuario->id_usuario)->first();
    }

    public function show(Request $request)
    {
        $pasante = $this->obtenerPasanteAutenticado($request);

        if (! $pasante) {
            return response()->json(['message' => 'No se encontró el perfil de pasante.'], 404);
        }

        $hojaVida = HojaVida::where('id_pasante', $pasante->id_pasante)->first();

        if (! $hojaVida) {
            return response()->json([
                'message' => 'No se encontró la hoja de vida del pasante.',
                'hoja_vida' => null,
            ], 404);
        }

        return response()->json([
            'hoja_vida' => array_merge($hojaVida->toArray(), [
                'documento_base64' => $hojaVida->documento_base64,
            ]),
        ]);
    }

    public function store(Request $request)
    {
        $pasante = $this->obtenerPasanteAutenticado($request);

        if (! $pasante) {
            return response()->json(['message' => 'No se encontró el perfil de pasante.'], 404);
        }

        if (HojaVida::where('id_pasante', $pasante->id_pasante)->exists()) {
            return response()->json(['message' => 'La hoja de vida ya existe.'], 409);
        }

        $request->validate([
            'habilidades' => 'nullable|string|max:2000',
            'documento' => 'required|file|mimes:pdf|max:10240',
        ]);

        return DB::transaction(function () use ($request, $pasante) {
            $archivo = $request->file('documento');
            
            // SOLUCIÓN: Leer archivo y convertir a string Hexadecimal (\x...) para PostgreSQL
            $binario = file_get_contents($archivo->getRealPath());
            $documentoHex = '\x' . bin2hex($binario);

            $hojaVida = HojaVida::create([
                'id_pasante' => $pasante->id_pasante,
                'habilidades' => $request->habilidades,
                'documento_blob' => $documentoHex,
                'documento_mime' => $archivo->getClientMimeType(),
                'documento_nombre' => $archivo->getClientOriginalName(),
            ]);

            // Refrescar el modelo desde la base de datos para limpiar la memoria
            $hojaVida->refresh();

            return response()->json([
                'message' => 'Hoja de vida registrada correctamente.',
                'hoja_vida' => array_merge($hojaVida->toArray(), [
                    'documento_base64' => $hojaVida->documento_base64,
                ]),
            ], 201);
        });
    }

    public function update(Request $request)
    {
        $pasante = $this->obtenerPasanteAutenticado($request);

        if (! $pasante) {
            return response()->json(['message' => 'No se encontró el perfil de pasante.'], 404);
        }

        $hojaVida = HojaVida::where('id_pasante', $pasante->id_pasante)->first();

        if (! $hojaVida) {
            return response()->json(['message' => 'No se encontró la hoja de vida.'], 404);
        }

        $request->validate([
            'habilidades' => 'nullable|string|max:2000',
            'documento' => 'nullable|file|mimes:pdf|max:10240',
        ]);

        return DB::transaction(function () use ($request, $hojaVida) {
            $datosActualizar = [
                'habilidades' => $request->habilidades ?? $hojaVida->habilidades,
            ];

            if ($request->hasFile('documento')) {
                $archivo = $request->file('documento');
                
                // SOLUCIÓN: Leer archivo y convertir a string Hexadecimal
                $binario = file_get_contents($archivo->getRealPath());
                $datosActualizar['documento_blob'] = '\x' . bin2hex($binario);
                
                $datosActualizar['documento_nombre'] = $archivo->getClientOriginalName();
                $datosActualizar['documento_mime'] = $archivo->getClientMimeType();
            }

            $hojaVida->update($datosActualizar);

            // Refrescar el modelo
            $hojaVida->refresh();

            return response()->json([
                'message' => 'Hoja de vida actualizada correctamente.',
                'hoja_vida' => array_merge($hojaVida->toArray(), [
                    'documento_base64' => $hojaVida->documento_base64,
                ]),
            ]);
        });
    }
}