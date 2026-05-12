<?php

namespace App\Http\Controllers\Api\Pasante;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificacionPasanteController extends Controller
{
    public function index(Request $request)
    {
        return response()->json([
            'notificaciones' => []
        ]);
    }

    public function marcarLeida(Request $request, $id_notificacion)
    {
        return response()->json([
            'message' => 'Notificación marcada como leída temporalmente.',
            'id_notificacion' => $id_notificacion
        ]);
    }

    public function marcarTodasLeidas(Request $request)
    {
        return response()->json([
            'message' => 'Todas las notificaciones fueron marcadas como leídas temporalmente.'
        ]);
    }
}