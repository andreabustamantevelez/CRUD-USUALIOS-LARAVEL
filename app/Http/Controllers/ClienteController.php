<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;
use App\Models\Empresa;

class ClienteController extends Controller
{
    public function index()
    {
        $clientes = Cliente::all();

        return view('clientes.index', compact('clientes'));
    }

    public function create()
    {
     $empresas = Empresa :: all(['id_empresa', 'nombre']);
     return view ('clientes.create', compact ('empresas'));
    }

    public function store(Request $request)
    {
      $request->validate ([
        'nombre' => 'required|max :100',
        'documento' => 'required|unique:cliente',
        'email' => 'required|email|unique:cliente',
      ]);

          Cliente::create($request->all());

          return redirect () -> route('clientes.index')
          ->with('success','Cliente creado exitosamente.');

    }

    public function show(string $id)
    {
        //
    }

    public function edit(Cliente $cliente)
    {
        $empresas = Empresa :: all(['id_empresa', 'nombre']);
        return view ('clientes.edit', compact ('cliente','empresas'));
    }

    public function update(Request $request, Cliente $cliente)
    {
      $request->validate([
        'nombre' => 'required|max:100',
        'apellido' => 'required|max:100',


    'documento' => 'required|unique:cliente,documento,' . $cliente ->id_cliente . ',id_cliente',
    'email' => 'required|email|unique:cliente,email,' . $cliente->id_cliente . ',id_cliente',

    'id_empresa' =>'required|exists:empresa,id_empresa',

      ]);
    

 $cliente->update($request->all());
 
 return redirect ()->route('clientes.index')
 ->with ('success','Cliente actualizado exitosamente.');
}

   public function destroy(Cliente $cliente)
{
    $cliente->delete();

    return redirect()
        ->route('clientes.index')
        ->with('success', 'Cliente eliminado exitosamente.');
}
}