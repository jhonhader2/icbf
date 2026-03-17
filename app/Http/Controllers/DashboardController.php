<?php

namespace App\Http\Controllers;

use App\Models\Cpu;
use App\Models\Persona;
use Carbon\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $user = request()->user();
        $isAdmin = $user && $user->can('personas.update'); // Ver KPIs globales solo si puede gestionar personas

        if (! $isAdmin) {
            return $this->dashboardUsuario();
        }

        return $this->dashboardAdmin();
    }

    /** Dashboard para administradores: datos por regional. */
    private function dashboardAdmin(): View
    {
        $rid = $this->userRegionalId();

        $personasQuery = Persona::query()->when($rid !== null, fn ($q) => $q->where('personas.regional_id', $rid));
        $totalPersonas = $personasQuery->count();
        $personasActivas = (clone $personasQuery)->where('personas.account_status', '1')->count();
        $personasInactivas = (clone $personasQuery)->where('personas.account_status', '0')->count();
        $personasSinEstado = $totalPersonas - $personasActivas - $personasInactivas;

        $personasPorDepartamento = Persona::query()
            ->when($rid !== null, fn ($q) => $q->where('personas.regional_id', $rid))
            ->select('departments.nombre as department_name')
            ->selectRaw('count(personas.id) as total')
            ->leftJoin('departments', 'personas.department_id', '=', 'departments.id')
            ->groupBy('personas.department_id', 'departments.nombre')
            ->orderByDesc('total')
            ->limit(12)
            ->get();

        $personasPorRegional = Persona::query()
            ->when($rid !== null, fn ($q) => $q->where('personas.regional_id', $rid))
            ->select('regionales.id as regional_id', 'regionales.nombre as regional_name')
            ->selectRaw('count(personas.id) as total')
            ->leftJoin('regionales', 'personas.regional_id', '=', 'regionales.id')
            ->groupBy('personas.regional_id', 'regionales.id', 'regionales.nombre')
            ->orderByDesc('total')
            ->get();

        $cpusQuery = Cpu::query()->when(
            $rid !== null,
            fn ($q) => $q->whereHas('activoCrv', fn ($q2) => $q2->where('regional_id', $rid))
        );

        return $this->vistaDashboard(true, [
            'totalPersonas' => $totalPersonas,
            'personasActivas' => $personasActivas,
            'personasInactivas' => $personasInactivas,
            'personasSinEstado' => $personasSinEstado,
            'personasPorDepartamento' => $personasPorDepartamento,
            'personasPorRegional' => $personasPorRegional,
        ], $cpusQuery);
    }

    /** Dashboard para usuario: solo sus activos (persona actual, sus CPUs). */
    private function dashboardUsuario(): View
    {
        $persona = $this->currentPersona();
        if (! $persona) {
            abort(404, __('No tiene una persona asociada; contacte al administrador.'));
        }

        $cpusQuery = Cpu::query()->where('cpus.persona_id', $persona->id);

        return $this->vistaDashboard(false, [
            'totalPersonas' => 0,
            'personasActivas' => 0,
            'personasInactivas' => 0,
            'personasSinEstado' => 0,
            'personasPorDepartamento' => collect(),
            'personasPorRegional' => collect(),
        ], $cpusQuery);
    }

    /** @param \Illuminate\Database\Eloquent\Builder $cpusQuery */
    private function vistaDashboard(bool $isAdmin, array $personasData, $cpusQuery): View
    {
        $totalCpus = $cpusQuery->count();

        $cpusPorTipo = (clone $cpusQuery)
            ->leftJoin('activos_crv', 'activos_crv.id', '=', 'cpus.activo_crv_id')
            ->leftJoin('productos', 'productos.id', '=', 'activos_crv.producto_id')
            ->selectRaw("COALESCE(NULLIF(TRIM(cpus.tipo_equipo), ''), productos.nombre) AS tipo")
            ->selectRaw('COUNT(*) AS total')
            ->groupByRaw("COALESCE(NULLIF(TRIM(cpus.tipo_equipo), ''), productos.nombre)")
            ->orderByDesc('total')
            ->get()
            ->map(fn ($r) => [
                'tipo' => $r->tipo ?: __('Sin tipo'),
                'total' => (int) $r->total,
            ])
            ->all();

        $limite5Anios = Carbon::now()->subYears(5)->startOfDay();
        $cpusMasDe5Anios = (clone $cpusQuery)->whereHas('activoCrv', fn ($q) => $q->whereNotNull('fecha_adquisicion')->where('fecha_adquisicion', '<=', $limite5Anios))->count();
        $cpusMenosOIgual5 = (clone $cpusQuery)->whereHas('activoCrv', fn ($q) => $q->whereNotNull('fecha_adquisicion')->where('fecha_adquisicion', '>', $limite5Anios))->count();
        $cpusSinFecha = (clone $cpusQuery)
            ->where(fn ($q) => $q->whereDoesntHave('activoCrv')->orWhereHas('activoCrv', fn ($q2) => $q2->whereNull('fecha_adquisicion')))
            ->count();

        return view('dashboard', [
            'isAdmin' => $isAdmin,
            'totalPersonas' => $personasData['totalPersonas'],
            'personasActivas' => $personasData['personasActivas'],
            'personasInactivas' => $personasData['personasInactivas'],
            'personasSinEstado' => $personasData['personasSinEstado'],
            'personasPorDepartamento' => $personasData['personasPorDepartamento'],
            'personasPorRegional' => $personasData['personasPorRegional'],
            'totalCpus' => $totalCpus,
            'cpusPorTipo' => $cpusPorTipo,
            'cpusMasDe5Anios' => $cpusMasDe5Anios,
            'cpusMenosOIgual5' => $cpusMenosOIgual5,
            'cpusSinFecha' => $cpusSinFecha,
        ]);
    }
}
