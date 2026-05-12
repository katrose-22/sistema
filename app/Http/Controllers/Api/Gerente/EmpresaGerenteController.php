<?php

namespace App\Http\Controllers\Api\Gerente;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Models\Gerente;
use App\Models\JefePasante;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class EmpresaGerenteController extends Controller
{
    public function MostrarMiEmpresa(Request $request)
    {
        $usuario = $request->user();

        $gerente = Gerente::with('empresa')
            ->where('id_usuario', $usuario->id_usuario)
            ->first();

        if ($gerente) {
            if (! $gerente->empresa) {
                return response()->json([
                    'message' => 'El gerente todavía no tiene una empresa registrada.',
                    'empresa' => null,
                ], 404);
            }

            return response()->json([
                'message' => 'Empresa encontrada correctamente.',
                'empresa' => $gerente->empresa,
            ]);
        }

        $encargado = JefePasante::with('empresa')
            ->where('id_usuario', $usuario->id_usuario)
            ->first();

        if ($encargado) {
            if (! $encargado->empresa) {
                return response()->json([
                    'message' => 'El encargado todavía no tiene empresa asignada.',
                    'empresa' => null,
                ], 404);
            }

            return response()->json([
                'message' => 'Empresa encontrada correctamente.',
                'empresa' => $encargado->empresa,
            ]);
        }

        return response()->json([
            'message' => 'El usuario autenticado no pertenece a una empresa.',
        ], 403);
    }

    public function RegistarMiEmpresa(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:150',
            'direccion' => 'required|string|max:255',
            'telefono' => 'required|string|max:30',
            'nit' => 'required|string|max:50|unique:empresa,nit',
        ]);

        return DB::transaction(function () use ($request) {

            $usuario = $request->user();

            $gerente = Gerente::where('id_usuario', $usuario->id_usuario)->first();

            if (! $gerente) {
                return response()->json([
                    'message' => 'El usuario autenticado no es gerente.',
                ], 403);
            }

            if ($gerente->id_empresa) {
                return response()->json([
                    'message' => 'Este gerente ya tiene una empresa registrada.',
                ], 409);
            }

            $empresa = Empresa::create([
                'nombre' => $request->nombre,
                'direccion' => $request->direccion,
                'telefono' => $request->telefono,
                'nit' => $request->nit,
            ]);

            $gerente->id_empresa = $empresa->id_empresa;
            $gerente->save();

            return response()->json([
                'message' => 'Empresa registrada correctamente.',
                'empresa' => $empresa,
                'gerente' => [
                    'id_usuario' => $gerente->id_usuario,
                    'cargo' => $gerente->cargo,
                    'id_empresa' => $gerente->id_empresa,
                ],
            ], 201);
        });
    }

    public function update(Request $request)
    {
        $usuario = $request->user();

        $gerente = Gerente::with('empresa')
            ->where('id_usuario', $usuario->id_usuario)
            ->first();

        if (! $gerente) {
            return response()->json([
                'message' => 'El usuario autenticado no es gerente.',
            ], 403);
        }

        if (! $gerente->empresa) {
            return response()->json([
                'message' => 'No existe una empresa registrada para editar.',
            ], 404);
        }

        $empresa = $gerente->empresa;

        $request->validate([
            'nombre' => 'required|string|max:150',
            'direccion' => 'required|string|max:255',
            'telefono' => 'required|string|max:30',
            'nit' => [
                'required',
                'string',
                'max:50',
                Rule::unique('empresa', 'nit')->ignore($empresa->id_empresa, 'id_empresa'),
            ],
        ]);

        $empresa->update([
            'nombre' => $request->nombre,
            'direccion' => $request->direccion,
            'telefono' => $request->telefono,
            'nit' => $request->nit,
        ]);

        return response()->json([
            'message' => 'Empresa actualizada correctamente.',
            'empresa' => $empresa,
        ]);
    }

    public function destroy(Request $request)
    {
        return DB::transaction(function () use ($request) {
            $usuario = $request->user();

            $gerente = Gerente::with('empresa')
                ->where('id_usuario', $usuario->id_usuario)
                ->first();

            if (! $gerente) {
                return response()->json([
                    'message' => 'El usuario autenticado no es gerente.',
                ], 403);
            }

            if (! $gerente->empresa) {
                return response()->json([
                    'message' => 'No existe una empresa registrada para eliminar.',
                ], 404);
            }

            $empresa = $gerente->empresa;

            $gerente->id_empresa = null;
            $gerente->save();

            $empresa->delete();

            return response()->json([
                'message' => 'Empresa eliminada correctamente.',
            ]);
        });
    }
}
