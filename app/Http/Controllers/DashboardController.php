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
        $totalPersonas = Persona::count();
        $personasActivas = Persona::where('account_status', '1')->count();
        $personasInactivas = Persona::where('account_status', '0')->count();
        $personasSinEstado = $totalPersonas - $personasActivas - $personasInactivas;

        $personasPorDepartamento = Persona::query()
            ->select('departments.nombre as department_name')
            ->selectRaw('count(personas.id) as total')
            ->leftJoin('departments', 'personas.department_id', '=', 'departments.id')
            ->groupBy('personas.department_id', 'departments.nombre')
            ->orderByDesc('total')
            ->limit(12)
            ->get();

        $personasPorRegional = Persona::query()
            ->select('regionales.id as regional_id', 'regionales.nombre as regional_name')
            ->selectRaw('count(personas.id) as total')
            ->leftJoin('regionales', 'personas.regional_id', '=', 'regionales.id')
            ->groupBy('personas.regional_id', 'regionales.id', 'regionales.nombre')
            ->orderByDesc('total')
            ->get();

        $totalCpus = Cpu::count();
        $limite5Anios = Carbon::now()->subYears(5)->startOfDay();
        $cpusMasDe5Anios = Cpu::whereHas('activoCrv', fn ($q) => $q->whereNotNull('fecha_adquisicion')->where('fecha_adquisicion', '<=', $limite5Anios))->count();
        $cpusMenosOIgual5 = Cpu::whereHas('activoCrv', fn ($q) => $q->whereNotNull('fecha_adquisicion')->where('fecha_adquisicion', '>', $limite5Anios))->count();
        $cpusSinFecha = Cpu::query()
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
