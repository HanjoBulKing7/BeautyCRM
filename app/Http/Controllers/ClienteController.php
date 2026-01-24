<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function index(Request $request)
    {
        $clientes = Cliente::latest()->paginate(10);

        if ($request->boolean('modal') || $request->ajax()) {
            return view('admin.clientes.partials.index-content', compact('clientes'));
        }

        return view('admin.clientes.index', compact('clientes'));
    }

    public function create(Request $request)
    {
        $cliente = new Cliente();

        if ($request->boolean('modal') || $request->ajax()) {
            return view('admin.clientes.partials.create-content', compact('cliente'));
        }

        return view('admin.clientes.create', compact('cliente'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'   => 'required|string|max:255',
            'email'    => 'required|email|unique:clientes,email',
            'telefono' => 'required|string|max:20',
            'direccion'=> 'nullable|string|max:255',
        ]);

        Cliente::create($request->only(['nombre','email','telefono','direccion']));

        if ($request->boolean('modal') || $request->ajax()) {
            $clientes = Cliente::latest()->paginate(10);
            return view('admin.clientes.partials.index-content', compact('clientes'))
                ->with('success', 'Cliente creado correctamente');
        }

        return redirect()->route('admin.clientes.index')->with('success', 'Cliente creado correctamente');
    }

    public function show(Request $request, Cliente $cliente)
    {
        if ($request->boolean('modal') || $request->ajax()) {
            return view('admin.clientes.partials.show-content', compact('cliente'));
        }

        return view('admin.clientes.show', compact('cliente'));
    }

    public function edit(Request $request, Cliente $cliente)
    {
        if ($request->boolean('modal') || $request->ajax()) {
            return view('admin.clientes.partials.edit-content', compact('cliente'));
        }

        return view('admin.clientes.edit', compact('cliente'));
    }

    public function update(Request $request, Cliente $cliente)
    {
        $request->validate([
            'nombre'   => 'required|string|max:255',
            'email'    => 'required|email|unique:clientes,email,' . $cliente->getKey() . ',' . $cliente->getKeyName(),
            'telefono' => 'required|string|max:20',
            'direccion'=> 'nullable|string|max:255',
        ]);

        $cliente->update($request->only(['nombre','email','telefono','direccion']));

        if ($request->boolean('modal') || $request->ajax()) {
            $clientes = Cliente::latest()->paginate(10);
            return view('admin.clientes.partials.index-content', compact('clientes'))
                ->with('success', 'Cliente actualizado correctamente');
        }

        return redirect()->route('admin.clientes.index')->with('success', 'Cliente actualizado correctamente');
    }

    public function destroy(Request $request, Cliente $cliente)
    {
        $cliente->delete();

        if ($request->boolean('modal') || $request->ajax()) {
            $clientes = Cliente::latest()->paginate(10);
            return view('admin.clientes.partials.index-content', compact('clientes'))
                ->with('success', 'Cliente eliminado correctamente');
        }

        return redirect()->route('admin.clientes.index')->with('success', 'Cliente eliminado correctamente');
    }
}
