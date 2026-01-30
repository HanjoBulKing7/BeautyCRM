<?php

namespace App\Http\Controllers;

use App\Models\Servicio;
use App\Models\ServicioHorario;
use App\Models\CategoriaServicio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ServicioController extends Controller
{
    public function index()
    {
        $servicios = Servicio::latest()->paginate(10);
        return view('admin.servicios.index', compact('servicios'));
    }

    public function create()
    {
        $servicio = new Servicio();

        // ✅ Para el <select> de categoría
        $categorias = CategoriaServicio::orderBy('nombre')->get();

        return view('admin.servicios.create', compact('servicio', 'categorias'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre_servicio'   => 'required|string|max:100',
            'descripcion'       => 'nullable|string',
            'precio'            => 'required|numeric|min:0',
            'duracion_minutos'  => 'required|integer|min:1',
            'id_categoria'      => 'nullable|exists:categorias_servicios,id_categoria',
            'estado'            => 'nullable|in:activo,inactivo',
            'descuento'         => 'nullable|numeric|min:0',
            'caracteristicas'   => 'nullable|string',
            'imagen'            => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'imagen.image' => 'Selecciona un archivo de imagen válido.',
            'imagen.mimes' => 'La imagen debe ser JPG, JPEG, PNG o WEBP.',
            'imagen.max'   => 'La imagen no debe pesar más de 2MB.',
        ]);

        $data = $request->except('imagen');

        if ($request->hasFile('imagen')) {
            $data['imagen'] = $request->file('imagen')->store('servicios', 'public');
        }

        $servicio = Servicio::create($data);

        // ✅ Guardar horarios si vienen
        $horarios = $request->input('horarios');
        $this->validateHorariosOrFail($horarios);
        $this->syncHorariosServicio($servicio, $horarios);

        return redirect()->route('admin.servicios.index')->with('success', 'Servicio creado correctamente');
    }

    public function show(Servicio $servicio)
    {
        $servicio->load('horarios', 'categoria');
        return view('admin.servicios.show', compact('servicio'));
    }

    public function edit(Servicio $servicio)
    {
        $servicio->load('horarios');

        // ✅ Para el <select> de categoría
        $categorias = CategoriaServicio::orderBy('nombre')->get();

        return view('admin.servicios.edit', compact('servicio', 'categorias'));
    }

    public function update(Request $request, Servicio $servicio)
    {
        $request->validate([
            'nombre_servicio'   => 'required|string|max:100',
            'descripcion'       => 'nullable|string',
            'precio'            => 'required|numeric|min:0',
            'duracion_minutos'  => 'required|integer|min:1',
            'id_categoria'      => 'nullable|exists:categorias_servicios,id_categoria', // ✅ antes decía 'categoria'
            'estado'            => 'required|in:activo,inactivo',
            'descuento'         => 'nullable|numeric|min:0',
            'caracteristicas'   => 'nullable|string',
            'imagen'            => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'imagen.image' => 'Selecciona un archivo de imagen válido.',
            'imagen.mimes' => 'La imagen debe ser JPG, JPEG, PNG o WEBP.',
            'imagen.max'   => 'La imagen no debe pesar más de 2MB.',
        ]);

        $data = $request->except('imagen');

        if ($request->hasFile('imagen')) {
            if ($servicio->imagen) {
                Storage::disk('public')->delete($servicio->imagen);
            }
            $data['imagen'] = $request->file('imagen')->store('servicios', 'public');
        }

        $servicio->update($data);

        // ✅ Actualizar horarios si vienen
        $horarios = $request->input('horarios');
        $this->validateHorariosOrFail($horarios);
        $this->syncHorariosServicio($servicio, $horarios);

        return redirect()->route('admin.servicios.index')->with('success', 'Servicio actualizado correctamente');
    }

    public function destroy(Servicio $servicio)
    {
        if ($servicio->imagen) {
            Storage::disk('public')->delete($servicio->imagen);
        }

        $servicio->delete();

        return redirect()->route('admin.servicios.index')->with('success', 'Servicio eliminado correctamente');
    }

    private function validateHorariosOrFail(?array $horarios): void
    {
        if (!$horarios) return;

        $errores = [];

        foreach ($horarios as $dia => $rangos) {
            if (!is_array($rangos)) continue;

            $clean = [];

            foreach ($rangos as $idx => $r) {
                $hi = $r['hora_inicio'] ?? null;
                $hf = $r['hora_fin'] ?? null;

                if (!$hi || !$hf) continue;

                if ($hi >= $hf) {
                    $errores[] = "Día {$dia}: el rango #".($idx+1)." tiene hora_inicio ({$hi}) mayor o igual a hora_fin ({$hf}).";
                    continue;
                }

                $clean[] = ['inicio' => $hi, 'fin' => $hf, 'idx' => $idx];
            }

            usort($clean, fn($a, $b) => strcmp($a['inicio'], $b['inicio']));

            for ($i = 0; $i < count($clean); $i++) {
                for ($j = $i + 1; $j < count($clean); $j++) {
                    $a = $clean[$i];
                    $b = $clean[$j];

                    if ($b['inicio'] >= $a['fin']) break;

                    $errores[] = "Día {$dia}: rangos solapados ({$a['inicio']}-{$a['fin']}) con ({$b['inicio']}-{$b['fin']}).";
                }
            }
        }

        if (!empty($errores)) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'horarios' => $errores
            ]);
        }
    }

    private function syncHorariosServicio(\App\Models\Servicio $servicio, ?array $horarios): void
    {
        // Si NO viene el campo "horarios", no tocamos nada
        if ($horarios === null) return;

        // Si viene (aunque venga vacío), se interpreta como "reemplazar"
        $servicioId = $servicio->getKey(); // ✅ evita depender de id_servicio vs id

        $servicio->horarios()->delete();

        $rows = [];
        foreach ($horarios as $dia => $rangos) {
            if (!is_array($rangos)) continue;

            foreach ($rangos as $r) {
                $hi = $r['hora_inicio'] ?? null;
                $hf = $r['hora_fin'] ?? null;

                if (!$hi || !$hf) continue;

                $rows[] = [
                    'servicio_id' => $servicioId,
                    'dia_semana'  => (int)$dia,
                    'hora_inicio' => $hi,
                    'hora_fin'    => $hf,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ];
            }
        }

        if ($rows) \App\Models\ServicioHorario::insert($rows);
    }

}
