<?php

namespace App\Http\Controllers;

use App\Models\Servicio;
use App\Models\ServicioHorario;
use App\Models\CategoriaServicio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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

        // ✅ Para el <select> de categoría (del pull)
        $categorias = CategoriaServicio::orderBy('nombre')->get();

            if ($this->isFragment($request)) {
                return view('admin.servicios.create', compact('servicio','categorias'));
            }


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

    public function show(Request $request, Servicio $servicio)
    {
        if ($r = $this->redirectIfModalInBrowser($request)) return $r;

        $servicio->load(['horarios', 'categoria']);

        if ($this->isFragment($request)) {
            // ✅ para modal/loader (sin layout)
            return view('admin.servicios.partials.show-content', compact('servicio'));
            // (si NO quieres partials, puedes regresar admin.servicios.show y listo,
            // pero lo ideal es partial para evitar layout dentro del modal)
        }

        return view('admin.servicios.show', compact('servicio'));
    }


    public function edit(Request $request, Servicio $servicio)
    {
        if ($r = $this->redirectIfModalInBrowser($request)) return $r;

        $servicio->load('horarios');

        // ✅ Para el <select> de categoría (del pull)
        $categorias = CategoriaServicio::orderBy('nombre')->get();

            if ($this->isFragment($request)) {
                return view('admin.servicios.create', compact('servicio','categorias'));
            }

        return view('admin.servicios.edit', compact('servicio', 'categorias'));
    }

    public function update(Request $request, Servicio $servicio)
    {
        $request->validate([
            'nombre_servicio'   => 'required|string|max:100',
            'descripcion'       => 'nullable|string',
            'precio'            => 'required|numeric|min:0',
            'duracion_minutos'  => 'required|integer|min:1',
            'id_categoria'      => 'nullable|exists:categorias_servicios,id_categoria',
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

    // ============================
    // Horarios helpers
    // ============================

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
                    $errores[] = "Día {$dia}: el rango #" . ($idx + 1) . " tiene hora_inicio ({$hi}) mayor o igual a hora_fin ({$hf}).";
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

    private function syncHorariosServicio(Servicio $servicio, ?array $horarios): void
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

    // ============================
    // Loader helpers (como antes)
    // ============================

    private function isFragment(Request $request): bool
    {
        return $request->ajax(); // el loader siempre manda X-Requested-With
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
