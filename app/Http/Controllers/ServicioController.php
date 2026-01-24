<?php

namespace App\Http\Controllers;

use App\Models\Servicio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\ServicioHorario;
use Illuminate\Support\Facades\DB;


class ServicioController extends Controller
{
    
    public function index(Request $request)
    {
        if ($r = $this->redirectIfModalInBrowser($request)) return $r;

        $servicios = Servicio::latest()->paginate(10);

        if ($this->isFragment($request)) {
            return view('admin.servicios.partials.index-content', compact('servicios'));
        }

        return view('admin.servicios.index', compact('servicios'));
    }

    public function create(Request $request)
    {
        if ($r = $this->redirectIfModalInBrowser($request)) return $r;

        $servicio = new Servicio();

        if ($this->isFragment($request)) {
            return view('admin.servicios.partials.create-content', compact('servicio'));
        }

        return view('admin.servicios.create', compact('servicio'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'nombre_servicio' => 'required|string|max:100',
            'descripcion' => 'nullable|string',
            'precio' => 'required|numeric|min:0',
            'duracion_minutos' => 'required|integer|min:1',
            'categoria' => 'nullable|string|max:50',
            'estado' => 'nullable|in:activo,inactivo',
            'descuento' => 'nullable|numeric|min:0',
            'caracteristicas' => 'nullable|string',
            'imagen' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'imagen.image' => 'Selecciona un archivo de imagen válido.',
            'imagen.mimes' => 'La imagen debe ser JPG, JPEG, PNG o WEBP.',
            'imagen.max'   => 'La imagen no debe pesar más de 2MB.',
        ]);

        $data = $request->except('imagen');

        if ($request->hasFile('imagen')) {
            $data['imagen'] = $request->file('imagen')->store('servicios', 'public');
        }

        Servicio::create($data);

        return redirect()->route('admin.servicios.index')->with('success', 'Servicio creado correctamente');
    }



    public function show(Request $request, Servicio $servicio)
    {
        if ($r = $this->redirectIfModalInBrowser($request)) return $r;

        $servicio->load('horarios');

        if ($this->isFragment($request)) {
            return view('admin.servicios.partials.show-content', compact('servicio'));
        }

        return view('admin.servicios.show', compact('servicio'));
    }


    public function edit(Request $request, Servicio $servicio)
    {
        if ($r = $this->redirectIfModalInBrowser($request)) return $r;

        $servicio->load('horarios');

        if ($this->isFragment($request)) {
            return view('admin.servicios.partials.edit-content', compact('servicio'));
        }

        return view('admin.servicios.edit', compact('servicio'));
    }



    public function update(Request $request, Servicio $servicio)
    {
        $request->validate([
            'nombre_servicio' => 'required|string|max:100',
            'descripcion' => 'nullable|string',
            'precio' => 'required|numeric|min:0',
            'duracion_minutos' => 'required|integer|min:1',
            'categoria' => 'nullable|string|max:50',
            'estado' => 'required|in:activo,inactivo',
            'descuento' => 'nullable|numeric|min:0',
            'caracteristicas' => 'nullable|string',
            'imagen' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            // ✅ mensajes “hardcode” para que NO salga validation.image
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

        return redirect()->route('admin.servicios.index')->with('success', 'Servicio actualizado correctamente');
    }



    public function destroy(Servicio $servicio)
    {
        // Eliminar imagen si existe
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

            // Normalizar y filtrar rangos completos
            $clean = [];

            foreach ($rangos as $idx => $r) {
                $hi = $r['hora_inicio'] ?? null;
                $hf = $r['hora_fin'] ?? null;

                // si viene incompleto lo ignoramos (no es error)
                if (!$hi || !$hf) continue;

                // Validación: inicio < fin (sin medianoche)
                if ($hi >= $hf) {
                    $errores[] = "Día {$dia}: el rango #".($idx+1)." tiene hora_inicio ({$hi}) mayor o igual a hora_fin ({$hf}).";
                    continue;
                }

                $clean[] = ['inicio' => $hi, 'fin' => $hf, 'idx' => $idx];
            }

            // Ordenar por inicio y validar solapes
            usort($clean, fn($a, $b) => strcmp($a['inicio'], $b['inicio']));

            for ($i = 0; $i < count($clean); $i++) {
                for ($j = $i + 1; $j < count($clean); $j++) {
                    $a = $clean[$i];
                    $b = $clean[$j];

                    // Si el siguiente empieza después (o igual) del fin del actual, ya no puede solapar (por estar ordenado)
                    if ($b['inicio'] >= $a['fin']) break;

                    // Solape detectado
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
        if (!$horarios) return;

        $servicio->horarios()->delete();

        $rows = [];
        foreach ($horarios as $dia => $rangos) {
            if (!is_array($rangos)) continue;

            foreach ($rangos as $r) {
                $hi = $r['hora_inicio'] ?? null;
                $hf = $r['hora_fin'] ?? null;

                if (!$hi || !$hf) continue;

                $rows[] = [
                    'servicio_id' => $servicio->id_servicio,
                    'dia_semana'  => (int)$dia,
                    'hora_inicio' => $hi,
                    'hora_fin'    => $hf,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ];
            }
        }

        if ($rows) ServicioHorario::insert($rows);
    }

    private function isFragment(Request $request): bool
    {
        // Fragment = lo que usará el loader
        return $request->ajax() || $request->boolean('modal');
    }

    private function redirectIfModalInBrowser(Request $request)
    {
        // Si alguien abre ?modal=1 directo (NO ajax), lo mandamos a la página normal
        if ($request->boolean('modal') && !$request->ajax()) {
            $query = $request->except('modal');
            $url = $request->url() . (count($query) ? ('?' . http_build_query($query)) : '');
            return redirect()->to($url);
        }

        return null;
    }


}