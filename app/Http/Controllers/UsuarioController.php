<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use App\Models\Empresa;

class UsuarioController extends Controller
{
    public function index()
    {
        $usuarios = Usuario::all();

        return view('usuario.index', compact('usuarios'));
    }

    public function create()
    {
    $empresas = Empresa::all(['id_empresa', 'nombre']);

    return view('usuario.create', compact('empresas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|max:100|unique:usuario,username',
            'password' => 'required|min:6',
            'rol' => 'required',
            'activo' => 'required',
            'fecha_creacion' => 'required|date',
            'id_empresa' => 'required|exists:empresa,id_empresa',
        ]);

        $datos = $request->all();
        $datos['password'] = \Illuminate\Support\Facades\Hash::make($request->password);

        Usuario::create($datos);

        return redirect()
            ->route('usuarios.index')
            ->with('success', 'Usuario creado exitosamente.');
    }

    public function show(string $id)
    {
        //
    }

    public function edit(Usuario $usuario)
    {
    $empresas = Empresa::all(['id_empresa', 'nombre']);

    return view('usuario.edit', compact('usuario', 'empresas'));
    }

    public function update(Request $request, Usuario $usuario)
    {
        $request->validate([
            'username' => 'required|max:100|unique:usuario,username,' . $usuario->id_usuario . ',id_usuario',
            'rol' => 'required',
            'activo' => 'required',
            'fecha_creacion' => 'required|date',
            'id_empresa' => 'required|exists:empresa,id_empresa',
        ]);

        $datos = $request->except('password');

        if ($request->filled('password')) {
            $request->validate([
                'password' => 'min:6',
            ]);

            $datos['password'] = \Illuminate\Support\Facades\Hash::make($request->password);
        }

        $usuario->update($datos);

        return redirect()
            ->route('usuarios.index')
            ->with('success', 'Usuario actualizado exitosamente.');
    }

    public function destroy(Usuario $usuario)
    {
        $usuario->delete();

        return redirect()
            ->route('usuarios.index')
            ->with('success', 'Usuario eliminado exitosamente.');
    }
}
