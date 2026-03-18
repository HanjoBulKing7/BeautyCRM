<?php

namespace App\Http\Controllers;

use App\Models\CategoriaServicio;
use App\Models\Servicio;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class ServiciosPublicController extends Controller
{
    public function index()
    {
        // Obtener el último servicio modificado
        $ultimoServicio = Servicio::orderBy('updated_at', 'desc')->first();

        // Categorías + servicios (listado completo)
        $categorias = CategoriaServicio::query()
            ->where('estado', 'activo')
            ->with(['servicios' => function ($q) {
                $q->where('estado', 'activo')
                  ->orderBy('nombre_servicio');
            }])
            ->orderBy('nombre')
            ->get();

        // Obtener servicios destacados (populares o nuevos)
        $destacados = $this->getServiciosDestacados();

        return view('servicio', compact('categorias', 'ultimoServicio', 'destacados'));
    }

    /**
     * Obtiene los servicios más solicitados o, en su defecto, los más nuevos.
     *
     * @return array
     */
    private function getServiciosDestacados(): array
    {
        $titulo = 'Más Solicitados';
        
        // Intentamos obtener los 4 servicios con más citas (más populares)
        $servicios = Servicio::query()
            ->where('estado', 'activo')
            ->withCount('citas') // Asume que tienes la relación 'citas' en el modelo Servicio
            ->orderByDesc('citas_count')
            ->having('citas_count', '>', 0) // Solo los que tienen al menos una cita
            ->take(4)
            ->get();

        // Si no hay servicios con citas, obtenemos los 4 más nuevos
        if ($servicios->isEmpty()) {
            $titulo = 'Nuevos Servicios';
            $servicios = Servicio::query()
                ->where('estado', 'activo')
                ->orderByDesc('created_at')
                ->take(4)
                ->get();
        }

        return [
            'titulo' => $titulo,
            'servicios' => $servicios,
        ];
    }

    /**
     * Muestra los detalles de un servicio específico.
     *
     * @param  int  $id
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function show($id)
    {
        $servicio = Servicio::where('id_servicio', $id)
            ->where('estado', 'activo')
            ->firstOrFail();

        return view('servicio_detalle', compact('servicio'));
    }
}
