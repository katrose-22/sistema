<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'contrasena' => 'required'
        ]);

        $usuario = Usuario::where('email', $request->email)->first();

        if (!$usuario || !Hash::check($request->contrasena, $usuario->contrasena)) {
            return response()->json([
                'message' => 'Credenciales incorrectas'
            ], 401);
        }

        $token = $usuario->createToken('token_api')->plainTextToken;

        $redirect = null;

        if ($usuario->rol === 'gerente') {
            $redirect = '/gerente/dashboard';
        } elseif ($usuario->rol === 'pasante') {
            $redirect = '/pasante/dashboard';
        } elseif ($usuario->rol === 'jefe_pasante') {
            $redirect = '/jefe/dashboard';
        } elseif ($usuario->rol === 'tutor') {
            $redirect = '/tutor/dashboard';
        } else {
            $redirect = '/login';
        }

        return response()->json([
            'token' => $token,
            'usuario' => [
                'id_usuario' => $usuario->id_usuario,
                'nombre' => $usuario->nombre,
                'apellido' => $usuario->apellido,
                'email' => $usuario->email,
                'telefono' => $usuario->telefono,
                'rol' => $usuario->rol,
            ],
            'redirect' => $redirect
        ]);
    }

    public function me(Request $request)
    {
        return response()->json([
            'usuario' => $request->user()
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Sesión cerrada correctamente'
        ]);
    }
}