<?php

namespace App\Http\Controllers\Api\Gerente;

use App\Http\Controllers\Controller;
use App\Models\BoletaInscripcion;
use App\Models\Gerente;
use Illuminate\Http\Request;

class PostulacionGerenteController extends Controller
{
    public function index(Request $request)
    {
        $usuario = $request->user();

        $gerente = Gerente::where('id_usuario', $usuario->id_usuario)->first();

        if (!$gerente) {
            return response()->json([
                'message' => 'El usuario autenticado no es gerente.'
            ], 403);
        }

        if (!$gerente->id_empresa) {
            return response()->json([
                'message' => 'Primero debe registrar una empresa.',
                'postulaciones' => []
            ], 409);
        }

        $boletas = BoletaInscripcion::with([
            'pasante.usuario',
            'pasantia',
            'jefe.usuario',
        ])
            ->whereHas('pasantia', function ($query) use ($gerente) {
                $query->where('id_empresa', $gerente->id_empresa);
            })
            ->orderByDesc('id_boleta')
            ->get();

        $postulaciones = $boletas->map(function ($boleta) {
            return [
                'id_boleta' => $boleta->id_boleta,
                'fecha' => $boleta->fecha,
                'descripcion' => $boleta->descripcion,

                'pasante' => [
                    'id_pasante' => $boleta->pasante?->id_pasante,
                    'ci' => $boleta->pasante?->ci,
                    'reg_universitario' => $boleta->pasante?->reg_universitario,
                    'direccion' => $boleta->pasante?->direccion,
                    'telefono' => $boleta->pasante?->telefono ?? $boleta->pasante?->usuario?->telefono,

                    'usuario' => [
                        'id_usuario' => $boleta->pasante?->usuario?->id_usuario,
                        'nombre' => $boleta->pasante?->usuario?->nombre,
                        'apellido' => $boleta->pasante?->usuario?->apellido,
                        'email' => $boleta->pasante?->usuario?->email,
                        'telefono' => $boleta->pasante?->usuario?->telefono,
                    ],
                ],

                'pasantia' => [
                    'id_pasantia' => $boleta->pasantia?->id_pasantia,
                    'nombre' => $boleta->pasantia?->nombre,
                    'descripcion' => $boleta->pasantia?->descripcion,
                    'fecha_inicio' => $boleta->pasantia?->fecha_inicio,
                    'fecha_fin' => $boleta->pasantia?->fecha_fin,
                    'horario' => $boleta->pasantia?->horario,
                    'estado' => $boleta->pasantia?->estado,
                ],

                'jefe' => [
                    'id_usuario' => $boleta->jefe?->id_usuario,
                    'cargo' => $boleta->jefe?->cargo,
                    'telefono' => $boleta->jefe?->telefono,
                    'usuario' => [
                        'nombre' => $boleta->jefe?->usuario?->nombre,
                        'apellido' => $boleta->jefe?->usuario?->apellido,
                        'email' => $boleta->jefe?->usuario?->email,
                    ],
                ],
            ];
        });

        return response()->json([
            'postulaciones' => $postulaciones
        ]);
    }
}
