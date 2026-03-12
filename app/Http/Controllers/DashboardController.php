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

        $cpusQuery = Cpu::query()->when($rid !== null, fn ($q) => $q->whereHas('activoCrv', fn ($q2) => $q2->where('regional_id', $rid)));
        $totalCpus = $cpusQuery->count();
        $limite5Anios = Carbon::now()->subYears(5)->startOfDay();
        $cpusMasDe5Anios = (clone $cpusQuery)->whereHas('activoCrv', fn ($q) => $q->whereNotNull('fecha_adquisicion')->where('fecha_adquisicion', '<=', $limite5Anios))->count();
        $cpusMenosOIgual5 = (clone $cpusQuery)->whereHas('activoCrv', fn ($q) => $q->whereNotNull('fecha_adquisicion')->where('fecha_adquisicion', '>', $limite5Anios))->count();
        $cpusSinFecha = (clone $cpusQuery)
            ->where(fn ($q) => $q->whereDoesntHave('activoCrv')->orWhereHas('activoCrv', fn ($q2) => $q2->whereNull('fecha_adquisicion')))
            ->count();

        return view('dashboard', [
            'totalPersonas' => $totalPersonas,
            'personasActivas' => $personasActivas,
            'personasInactivas' => $personasInactivas,
            'personasSinEstado' => $personasSinEstado,
            'personasPorDepartamento' => $personasPorDepartamento,
            'personasPorRegional' => $personasPorRegional,
            'totalCpus' => $totalCpus,
            'cpusMasDe5Anios' => $cpusMasDe5Anios,
            'cpusMenosOIgual5' => $cpusMenosOIgual5,
            'cpusSinFecha' => $cpusSinFecha,
        ]);
    }
}
