<?php

namespace App\Http\Controllers\Api\Gerente;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EmpresaGerenteController extends Controller
{
    public function show(Request $request)
    {
        return response()->json([
            'message' => 'Mostrar empresa del gerente'
        ]);
    }

    public function store(Request $request)
    {
        return response()->json([
            'message' => 'Registrar empresa del gerente'
        ]);
    }

    public function update(Request $request)
    {
        return response()->json([
            'message' => 'Editar empresa del gerente'
        ]);
    }
}
