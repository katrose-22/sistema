<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pasante;
use App\Models\Usuario;
use Illuminate\Support\Facades\DB;

class PasanteController extends Controller
{
    public function index()
    {
        $pasantes = Pasante::with('usuario')->get();
        return view('pasantes.index', compact('pasantes'));
    }

    public function create()
    {
        return view('pasantes.create');
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $usuario = Usuario::create([
                'nombre' => $request->nombre,
                'apellido' => $request->apellido,
                'email' => $request->email,
                'password' => bcrypt('123456')
            ]);

            Pasante::create([
                'ci' => $request->ci,
                'reg_universitario' => $request->reg_universitario,
                'id_usuario' => $usuario->id_usuario
            ]);

            DB::commit();

            return redirect('/pasantes');

        } catch (\Exception $e) {
            DB::rollBack();
            dd($e->getMessage());
        }
    }

    public function edit($id)
    {
        $pasante = Pasante::with('usuario')->findOrFail($id);
        return view('pasantes.edit', compact('pasante'));
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            $pasante = Pasante::findOrFail($id);
            $usuario = $pasante->usuario;

            $usuario->update([
                'nombre' => $request->nombre,
                'apellido' => $request->apellido,
                'email' => $request->email
            ]);

            $pasante->update([
                'ci' => $request->ci,
                'reg_universitario' => $request->reg_universitario
            ]);

            DB::commit();

            return redirect('/pasantes');

        } catch (\Exception $e) {
            DB::rollBack();
            dd($e->getMessage());
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $pasante = Pasante::findOrFail($id);
            $usuario = $pasante->usuario;

            $pasante->delete();
            $usuario->delete();

            DB::commit();

            return redirect('/pasantes');

        } catch (\Exception $e) {
            DB::rollBack();
            dd($e->getMessage());
        }
    }
}