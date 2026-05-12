<?php


namespace App\Http\Controllers\Api\Pasante;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MensajePasanteController extends Controller
{
    public function index(Request $request)
    {
        return response()->json([
            'mensajes' => []
        ]);
    }

    public function store(Request $request)
    {
        return response()->json([
            'message' => 'Mensaje registrado temporalmente.',
            'mensaje' => [
                'texto' => $request->mensaje,
                'fecha' => now()->toDateTimeString()
            ]
        ], 201);
    }

    public function marcarLeido(Request $request, $id_mensaje)
    {
        return response()->json([
            'message' => 'Mensaje marcado como leído temporalmente.',
            'id_mensaje' => $id_mensaje
        ]);
    }
}