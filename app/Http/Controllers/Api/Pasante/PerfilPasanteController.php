<?php

namespace App\Http\Controllers\Api\Pasante;

use App\Http\Controllers\Controller;
use App\Models\Pasante;
use Illuminate\Http\Request;

class PerfilPasanteController extends Controller
{
    public function show(Request $request)
    {
        $usuario = $request->user();

        $pasante = Pasante::with(['usuario', 'institucion'])
            ->where('id_usuario', $usuario->id_usuario)
            ->first();

        if (! $pasante) {
            return response()->json([
                'message' => 'Perfil de pasante no encontrado',
            ], 404);
        }

        return response()->json([
            'message' => 'Perfil del pasante',
            'pasante' => [
                'id_pasante' => $pasante->id_pasante,
                'ci' => $pasante->ci,
                'reg_universitario' => $pasante->reg_universitario,
                'direccion' => $pasante->direccion,
                'telefono' => $pasante->telefono,
                'id_usuario' => $pasante->id_usuario,
                'id_institucion' => $pasante->id_institucion,
                'usuario' => $pasante->usuario,
                'institucion' => $pasante->institucion,
            ],
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'ci' => 'sometimes|string|max:50',
            'reg_universitario' => 'sometimes|string|max:100',
            'direccion' => 'sometimes|string|max:255',
            'telefono' => 'sometimes|string|max:20',
        ]);

        $usuario = $request->user();

        $pasante = Pasante::where('id_usuario', $usuario->id_usuario)->first();

        if (! $pasante) {
            return response()->json([
                'message' => 'Perfil de pasante no encontrado',
            ], 404);
        }

        $pasante->update($request->only(['ci', 'reg_universitario', 'direccion', 'telefono']));

        return response()->json([
            'message' => 'Perfil actualizado correctamente',
            'pasante' => [
                'id_pasante' => $pasante->id_pasante,
                'ci' => $pasante->ci,
                'reg_universitario' => $pasante->reg_universitario,
                'direccion' => $pasante->direccion,
                'telefono' => $pasante->telefono,
                'id_usuario' => $pasante->id_usuario,
                'id_institucion' => $pasante->id_institucion,
            ],
        ]);
    }
}
