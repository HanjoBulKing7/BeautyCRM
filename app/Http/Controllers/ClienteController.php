<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ClienteController extends Controller
{
    public function index(Request $request)
    {
        // (Opcional) si alguien abre ?modal=1 directo en el navegador, limpiamos la URL
        if ($r = $this->redirectIfModalInBrowser($request)) return $r;

        $clientes = Cliente::latest()->paginate(10);
        return view('admin.clientes.index', compact('clientes'));
    }

    public function create(Request $request)
    {
        if ($r = $this->redirectIfModalInBrowser($request)) return $r;

        $cliente = new Cliente();
        return view('admin.clientes.create', compact('cliente'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'    => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email',
            'telefono'  => 'nullable|string|max:255',
            'direccion' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request) {
            $user = User::create([
                'role_id'  => 1, // CLIENTE
                'name'     => $request->nombre,
                'email'    => $request->email,
                'password' => Hash::make(Str::random(32)),
            ]);

            Cliente::create([
                'user_id'   => $user->id,
                'nombre'    => $request->nombre,
                'email'     => $request->email,
                'telefono'  => $request->telefono,
                'direccion' => $request->direccion,
            ]);
        });

        return redirect()->route('admin.clientes.index')
            ->with('success', 'Cliente creado correctamente');
    }

    public function show(Request $request, Cliente $cliente)
    {
        if ($r = $this->redirectIfModalInBrowser($request)) return $r;

        return view('admin.clientes.show', compact('cliente'));
    }

    public function edit(Request $request, Cliente $cliente)
    {
        if ($r = $this->redirectIfModalInBrowser($request)) return $r;

        return view('admin.clientes.edit', compact('cliente'));
    }

    public function update(Request $request, Cliente $cliente)
    {
        $request->validate([
            'nombre'    => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email,' . $cliente->user_id,
            'telefono'  => 'nullable|string|max:255',
            'direccion' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request, $cliente) {
            $cliente->update($request->only(['nombre', 'email', 'telefono', 'direccion']));

            if ($cliente->user_id) {
                User::where('id', $cliente->user_id)->update([
                    'name'  => $request->nombre,
                    'email' => $request->email,
                ]);
            }
        });

        return redirect()->route('admin.clientes.index')
            ->with('success', 'Cliente actualizado correctamente');
    }

    public function destroy(Request $request, Cliente $cliente)
    {
        DB::transaction(function () use ($cliente) {
            if ($cliente->user_id) {
                User::where('id', $cliente->user_id)->delete();
            }
            $cliente->delete();
        });

        return redirect()->route('admin.clientes.index')
            ->with('success', 'Cliente eliminado correctamente');
    }

    private function redirectIfModalInBrowser(Request $request)
    {
        // Si abren ?modal=1 directo (NO ajax), mandamos a la misma ruta sin modal
        if ($request->boolean('modal') && !$request->ajax()) {
            $query = $request->except('modal');
            $url = $request->url() . (count($query) ? ('?' . http_build_query($query)) : '');
            return redirect()->to($url);
        }
        return null;
    }
}
