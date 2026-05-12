<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Gerente;
use App\Models\Pasante;
use App\Models\Rol;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'contrasena' => 'required',
        ]);

        $usuario = Usuario::with('rol')
            ->where('email', $request->email)
            ->first();

        if (! $usuario || ! Hash::check($request->contrasena, $usuario->contrasena)) {

            return response()->json([
                'message' => 'Credenciales incorrectas',
            ], 401);
        }

        // verificar si el rol está habilitado
        if (! $usuario->rol || ! $usuario->rol->habilitado) {

            return response()->json([
                'message' => 'Rol deshabilitado',
            ], 403);
        }

        // eliminar tokens anteriores opcional
        $usuario->tokens()->delete();

        // crear token
        $token = $usuario->createToken('token_api')->plainTextToken;

        $redirect = '/login';

        switch ($usuario->rol->abreviacion) {

            case 'GER_EMP':
                $redirect = '/gerente/dashboard';
                break;

            case 'PAS':
                $redirect = '/pasante/dashboard';
                break;

            case 'ENC_PAS':
                $redirect = '/jefe/dashboard';
                break;
        }

        return response()->json([
            'token' => $token,

            'usuario' => [
                'id_usuario' => $usuario->id_usuario,
                'nombre' => $usuario->nombre,
                'apellido' => $usuario->apellido,
                'email' => $usuario->email,
                'telefono' => $usuario->telefono,

                'rol' => [
                    'id_rol' => $usuario->rol->id_rol,
                    'descripcion' => $usuario->rol->descripcion,
                    'abreviacion' => $usuario->rol->abreviacion,
                ],
            ],

            'redirect' => $redirect,
        ]);
    }

    public function me(Request $request)
    {
        return response()->json([
            'usuario' => $request->user(),
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Sesión cerrada correctamente',
        ]);
    }

    public function RegistroUsuario(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'correo' => 'required|email|unique:usuario,email',
            'telefono' => 'nullable|string|max:30',
            'password' => 'required|string|min:6',

            'tipo_usuario' => [
                'required',
                Rule::in(['pasante', 'gerente']),
            ],

            'ci' => 'required_if:tipo_usuario,pasante|nullable|string|max:30',
            'registro_universitario' => 'required_if:tipo_usuario,pasante|nullable|string|max:50',
            'direccion' => 'required_if:tipo_usuario,pasante|nullable|string|max:255',
            'id_institucion' => 'nullable|integer|exists:institucion_academica,id_institucion',

            'cargo' => 'required_if:tipo_usuario,gerente|nullable|string|max:100',
            'id_empresa' => 'nullable|integer|exists:empresa,id_empresa',
        ]);

        return DB::transaction(function () use ($request) {

            $abreviacionRol = null;

            if ($request->tipo_usuario === 'pasante') {
                $abreviacionRol = 'PAS';
            }

            if ($request->tipo_usuario === 'gerente') {
                $abreviacionRol = 'GER_EMP';
            }

            $rol = Rol::where('abreviacion', $abreviacionRol)
                ->where('habilitado', true)
                ->first();

            if (! $rol) {
                return response()->json([
                    'message' => 'El rol seleccionado no existe o está deshabilitado.',
                ], 422);
            }

            $usuario = Usuario::create([
                'nombre' => $request->nombre,
                'apellido' => $request->apellido,
                'email' => $request->correo,
                'telefono' => $request->telefono,
                'contrasena' => Hash::make($request->password),
                'id_rol' => $rol->id_rol,
            ]);

            if ($request->tipo_usuario === 'pasante') {
                Pasante::create([
                    'ci' => $request->ci,
                    'reg_universitario' => $request->registro_universitario,
                    'direccion' => $request->direccion,
                    'telefono' => $request->telefono,
                    'id_usuario' => $usuario->id_usuario,
                    'id_institucion' => $request->id_institucion,
                ]);
            }

            if ($request->tipo_usuario === 'gerente') {
                Gerente::create([
                    'id_usuario' => $usuario->id_usuario,
                    'cargo' => $request->cargo,
                    'id_empresa' => $request->id_empresa,
                ]);
            }

            $usuario->load('rol');

            return response()->json([
                'message' => 'Usuario registrado correctamente',
                'usuario' => [
                    'id_usuario' => $usuario->id_usuario,
                    'nombre' => $usuario->nombre,
                    'apellido' => $usuario->apellido,
                    'email' => $usuario->email,
                    'telefono' => $usuario->telefono,
                    'rol' => [
                        'id_rol' => $usuario->rol->id_rol,
                        'descripcion' => $usuario->rol->descripcion,
                        'abreviacion' => $usuario->rol->abreviacion,
                    ],
                ],
            ], 201);
        });
    }
}
