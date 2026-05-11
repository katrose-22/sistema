<?php

namespace App\Http\Controllers\Api\Gerente;

use App\Http\Controllers\Controller;
use App\Models\Gerente;
use App\Models\JefePasante;
use App\Models\Rol;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class JefePasanteGerenteController extends Controller
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
                'encargados' => []
            ], 409);
        }

        $encargados = JefePasante::with('usuario')
            ->where('id_empresa', $gerente->id_empresa)
            ->get();

        return response()->json([
            'encargados' => $encargados
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'email' => 'required|email|unique:usuario,email',
            'contrasena' => 'required|string|min:6',
            'telefono' => 'required|string|max:30',
            'cargo' => 'required|string|max:100',
        ]);

        return DB::transaction(function () use ($request) {

            $usuarioAutenticado = $request->user();

            $gerente = Gerente::where('id_usuario', $usuarioAutenticado->id_usuario)->first();

            if (!$gerente) {
                return response()->json([
                    'message' => 'El usuario autenticado no es gerente.'
                ], 403);
            }

            if (!$gerente->id_empresa) {
                return response()->json([
                    'message' => 'Primero debe registrar una empresa antes de agregar encargados.'
                ], 409);
            }

            $rolEncargado = Rol::where('abreviacion', 'ENC_PAS')
                ->where('habilitado', true)
                ->first();

            if (!$rolEncargado) {
                return response()->json([
                    'message' => 'El rol Encargado de Pasante no existe o está deshabilitado.'
                ], 422);
            }

            $usuario = Usuario::create([
                'nombre' => $request->nombre,
                'apellido' => $request->apellido,
                'email' => $request->email,
                'telefono' => $request->telefono,
                'contrasena' => Hash::make($request->contrasena),
                'id_rol' => $rolEncargado->id_rol,
            ]);

            $encargado = JefePasante::create([
                'id_usuario' => $usuario->id_usuario,
                'cargo' => $request->cargo,
                'telefono' => $request->telefono,
                'id_empresa' => $gerente->id_empresa,
            ]);

            return response()->json([
                'message' => 'Encargado de pasante registrado correctamente.',
                'usuario' => [
                    'id_usuario' => $usuario->id_usuario,
                    'nombre' => $usuario->nombre,
                    'apellido' => $usuario->apellido,
                    'email' => $usuario->email,
                    'telefono' => $usuario->telefono,
                    'rol' => [
                        'id_rol' => $rolEncargado->id_rol,
                        'descripcion' => $rolEncargado->descripcion,
                        'abreviacion' => $rolEncargado->abreviacion,
                    ],
                ],
                'encargado' => $encargado
            ], 201);
        });
    }
}
