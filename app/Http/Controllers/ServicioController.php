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
        $search = trim((string) $request->query('q', ''));

        $servicios = Servicio::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where('nombre_servicio', 'like', '%' . $search . '%');
            })
            ->latest()
            ->paginate(15)
            ->appends(['q' => $search]);

        return view('admin.servicios.index', compact('servicios', 'search'));
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
            'caracteristicas'   => 'nullable|string',
            'imagen'            => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',

            // ✅ nuevo: GRID JSON
            'horarios_grid'     => 'nullable|string',
        ], [
            'imagen.image' => 'Selecciona un archivo de imagen válido.',
            'imagen.mimes' => 'La imagen debe ser JPG, JPEG, PNG o WEBP.',
            'imagen.max'   => 'La imagen no debe pesar más de 2MB.',
        ]);

        // ✅ blindaje: nunca tocar descuento
        $data = $request->except(['imagen', 'descuento', 'horarios', 'horarios_grid']);

        if ($request->hasFile('imagen')) {
            $data['imagen'] = $request->file('imagen')->store('servicios', 'public');
        }

        $servicio = Servicio::create($data);

        // ✅ Horarios: prioridad al GRID; fallback al legacy
        $horarios = null;

        if ($request->has('horarios_grid')) {
            $horarios = $this->gridJsonToHorarios($request->input('horarios_grid'));
        } else {
            $horarios = $request->input('horarios');
        }

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

        $categorias = CategoriaServicio::orderBy('nombre')->get();

        // ✅ Prefill para el GRID (redondeado a 30 min por si había 10:40:00 etc)
        $existingRanges = $servicio->horarios
            ->groupBy('dia_semana')
            ->map(function ($items) {
                return $items->map(function ($h) {
                    $ini = substr((string)$h->hora_inicio, 0, 5);
                    $fin = substr((string)$h->hora_fin, 0, 5);

                    return [
                        'inicio' => $this->floorToSlot($ini, 30),
                        'fin'    => $this->ceilToSlot($fin, 30),
                    ];
                })->values();
            })
            ->toArray();

        return view('admin.servicios.edit', compact('servicio', 'categorias', 'existingRanges'));
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
            'caracteristicas'   => 'nullable|string',
            'imagen'            => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',

            // ✅ nuevo: GRID JSON
            'horarios_grid'     => 'nullable|string',
        ], [
            'imagen.image' => 'Selecciona un archivo de imagen válido.',
            'imagen.mimes' => 'La imagen debe ser JPG, JPEG, PNG o WEBP.',
            'imagen.max'   => 'La imagen no debe pesar más de 2MB.',
        ]);

        // ✅ blindaje: nunca tocar descuento
        $data = $request->except(['imagen', 'descuento', 'horarios', 'horarios_grid']);

        if ($request->hasFile('imagen')) {
            if ($servicio->imagen) {
                Storage::disk('public')->delete($servicio->imagen);
            }
            $data['imagen'] = $request->file('imagen')->store('servicios', 'public');
        }

        $servicio->update($data);

        // ✅ Horarios: prioridad al GRID; fallback al legacy
        $horarios = null;

        if ($request->has('horarios_grid')) {
            $horarios = $this->gridJsonToHorarios($request->input('horarios_grid'));
        } else {
            $horarios = $request->input('horarios');
        }

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
    /**
     * ✅ Valida:
     * - inicio < fin
     * - no solapes por día
     */
    private function validateHorariosOrFail(?array $horarios): void
    {
        if ($horarios === null) return; // null = "no tocar"
        if (!$horarios) return;         // [] = ok (reemplazar por vacío)

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
            throw ValidationException::withMessages(['horarios' => $errores]);
        }
    }

    /**
     * ✅ Inserta en servicio_horarios:
     * - si $horarios === null => NO tocar
     * - si $horarios viene (aunque vacío) => reemplazar
     */
    private function syncHorariosServicio(Servicio $servicio, ?array $horarios): void
    {
        if ($horarios === null) return;

        $servicioId = $servicio->getKey();

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

        if ($rows) {
            ServicioHorario::insert($rows);
        }
    }

    /**
     * ✅ Convierte JSON del GRID (slots 30 min) a rangos compatibles con validate/sync:
     * output:
     * [
     *   0 => [ ['hora_inicio'=>'07:00:00','hora_fin'=>'11:00:00'], ... ],
     *   1 => [ ... ],
     * ]
     */
    private function gridJsonToHorarios(?string $json): array
    {
        $json = trim((string)$json);

        // si viene vacío, interpretamos como "sin horarios"
        if ($json === '') return [];

        $data = json_decode($json, true);

        if (!is_array($data)) {
            throw ValidationException::withMessages([
                'horarios_grid' => ['Formato inválido de horarios (JSON).'],
            ]);
        }

        $slot = (int)($data['slot_minutes'] ?? 30);
        if ($slot !== 30) {
            throw ValidationException::withMessages([
                'horarios_grid' => ['slot_minutes inválido.'],
            ]);
        }

        $start = substr((string)($data['start'] ?? '07:00'), 0, 5);
        $end   = substr((string)($data['end'] ?? '21:00'), 0, 5);

        $days = $data['days'] ?? [];
        if (!is_array($days)) $days = [];

        $out = [];

        foreach ($days as $day => $slotsList) {
            $day = (int)$day;
            if ($day < 0 || $day > 6) continue;
            if (!is_array($slotsList)) continue;

            // normalizar slots: HH:MM, dedupe, dentro de ventana, alineado a 30 min
            $norm = [];
            foreach ($slotsList as $t) {
                if (!is_string($t)) continue;
                $t = substr($t, 0, 5);

                if (!preg_match('/^\d{2}:\d{2}$/', $t)) continue;

                $min = (int)substr($t, 3, 2);
                if (($min % $slot) !== 0) continue;

                if ($t < $start || $t >= $end) continue;

                $norm[$t] = true;
            }

            $slotsSorted = array_keys($norm);
            sort($slotsSorted);

            $ranges = $this->slotsToRanges($slotsSorted, $slot);

            foreach ($ranges as $r) {
                $out[$day][] = [
                    'hora_inicio' => $r['hora_inicio'],
                    'hora_fin'    => $r['hora_fin'],
                ];
            }
        }

        return $out;
    }

    /**
     * slots ["07:00","07:30","08:00"] => rangos
     * [
     *   ['hora_inicio'=>'07:00:00','hora_fin'=>'08:30:00']
     * ]
     */
    private function slotsToRanges(array $slotsHHMM, int $slotMinutes): array
    {
        if (!$slotsHHMM) return [];

        $ranges = [];
        $start = null;
        $prev  = null;

        foreach ($slotsHHMM as $t) {
            if ($start === null) { $start = $t; $prev = $t; continue; }

            $expected = $this->addMinutesHHMM($prev, $slotMinutes);

            if ($t === $expected) {
                $prev = $t;
            } else {
                $ranges[] = [
                    'hora_inicio' => $this->toTime($start),
                    'hora_fin'    => $this->toTime($this->addMinutesHHMM($prev, $slotMinutes)),
                ];
                $start = $t; $prev = $t;
            }
        }

        $ranges[] = [
            'hora_inicio' => $this->toTime($start),
            'hora_fin'    => $this->toTime($this->addMinutesHHMM($prev, $slotMinutes)),
        ];

        return $ranges;
    }

    private function addMinutesHHMM(string $hhmm, int $minutes): string
    {
        $dt = \DateTime::createFromFormat('Y-m-d H:i', '2000-01-01 '.$hhmm);
        $dt->modify("+{$minutes} minutes");
        return $dt->format('H:i');
    }

    private function toTime(string $hhmm): string
    {
        return $hhmm . ':00';
    }

    // ✅ Redondeos para prefill en EDIT
    private function floorToSlot(string $hhmm, int $slot): string
    {
        $mins = $this->toMinutes($hhmm);
        $floored = intdiv($mins, $slot) * $slot;
        return $this->fromMinutes($floored);
    }

    private function ceilToSlot(string $hhmm, int $slot): string
    {
        $mins = $this->toMinutes($hhmm);
        $ceiled = (int)(ceil($mins / $slot) * $slot);
        return $this->fromMinutes($ceiled);
    }

    private function toMinutes(string $hhmm): int
    {
        $hhmm = substr($hhmm, 0, 5);
        [$h, $m] = array_map('intval', explode(':', $hhmm));
        return $h * 60 + $m;
    }

    private function fromMinutes(int $mins): string
    {
        $h = str_pad((string)intdiv($mins, 60), 2, '0', STR_PAD_LEFT);
        $m = str_pad((string)($mins % 60), 2, '0', STR_PAD_LEFT);
        return "{$h}:{$m}";
    }

}
