<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AjustesController extends Controller
{
    public function perfil(Request $request)
    {
        return view('app.ajustes.perfil', [
            'usuario' => $request->user(),
        ]);
    }

    public function actualizarPerfil(Request $request)
    {
        $usuario = $request->user();

        $datos = $request->validate([
            'nombre' => ['required', 'string', 'max:100'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($usuario->id),
            ],
            'ingreso_mensual' => [
                'required',
                'integer',
                'min:0',
                'max:999999999999',
            ],
        ]);

        $usuario->nombre = $datos['nombre'];
        $usuario->email = $datos['email'];
        $usuario->ingreso_mensual = $datos['ingreso_mensual'];
        $usuario->save();

        return redirect()
            ->route('ajustes.perfil')
            ->with('exito', 'Tu perfil se actualizó correctamente.');
    }
}
