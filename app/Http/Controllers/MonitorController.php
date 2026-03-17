<?php

namespace App\Http\Controllers;

use App\Models\ActivoCrv;
use App\Models\Cpu;
use App\Models\Monitor;
use App\Models\Persona;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class MonitorController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:monitores.view')->only(['index', 'show']);
        $this->middleware('permission:monitores.create')->only(['create', 'store']);
        $this->middleware('permission:monitores.update')->only(['edit', 'update']);
        $this->middleware('permission:monitores.delete')->only(['destroy']);
    }

    public function index(Request $request): View
    {
        $user = $request->user();
        $isAdmin = $user && $user->can('monitores.update');

        if (! $isAdmin) {
            $persona = $this->currentPersona();
            if (! $persona) {
                abort(404);
            }

            $baseQuery = Monitor::query()
                ->whereHas('cpu', fn ($q) => $q->where('cpus.persona_id', $persona->id));

            $monitoresPorMarca = $this->monitoresPorMarca($baseQuery);

            $monitores = (clone $baseQuery)
                ->with(['activoCrv.persona', 'cpu.persona', 'cpu.activoCrv.persona'])
                ->when($request->filled('q'), function ($q) use ($request) {
                    $term = '%' . $request->q . '%';
                    $q->where('serial', 'like', $term)
                        ->orWhere('placa', 'like', $term)
                        ->orWhere('marca', 'like', $term)
                        ->orWhere('modelo', 'like', $term);
                })
                ->orderBy('marca')
                ->orderBy('modelo')
                ->paginate(15)
                ->withQueryString();

            $propietariosPorId = $this->resolverPropietariosMonitores($monitores);
            return view('activos.monitores.index', compact('monitores', 'monitoresPorMarca', 'propietariosPorId'));
        }

        $rid = $this->userRegionalId();
        $baseQuery = Monitor::query()
            ->when(
                $rid !== null,
                fn ($q) => $q->whereHas('cpu.activoCrv', fn ($q2) => $q2->where('regional_id', $rid))
            );

        $monitoresPorMarca = $this->monitoresPorMarca($baseQuery);

        $monitores = (clone $baseQuery)
            ->with(['activoCrv.persona', 'cpu.persona', 'cpu.activoCrv.persona'])
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = '%' . $request->q . '%';
                $q->where('serial', 'like', $term)
                    ->orWhere('placa', 'like', $term)
                    ->orWhere('marca', 'like', $term)
                    ->orWhere('modelo', 'like', $term);
            })
            ->orderBy('marca')
            ->orderBy('modelo')
            ->paginate(15)
            ->withQueryString();

        $propietariosPorId = $this->resolverPropietariosMonitores($monitores);
        return view('activos.monitores.index', compact('monitores', 'monitoresPorMarca', 'propietariosPorId'));
    }

    /**
     * Resuelve el propietario (Persona) de cada monitor: activoCrv->persona, cpu->activoCrv->persona, cpu->persona, o por placa en activos_crv.
     * @param \Illuminate\Contracts\Pagination\LengthAwarePaginator $monitores
     * @return array<int, Persona|null>
     */
    private function resolverPropietariosMonitores($monitores): array
    {
        $porId = [];
        $placasSinProp = [];
        foreach ($monitores as $m) {
            $prop = $m->activoCrv?->persona ?? $m->cpu?->activoCrv?->persona ?? $m->cpu?->persona;
            if ($prop) {
                $porId[$m->id] = $prop;
            } elseif ($m->placa) {
                $placasSinProp[$m->id] = $m->placa;
            } else {
                $porId[$m->id] = null;
            }
        }
        if ($placasSinProp !== []) {
            $activos = ActivoCrv::query()
                ->with('persona')
                ->whereIn('placa', array_values($placasSinProp))
                ->get()
                ->keyBy('placa');
            foreach ($placasSinProp as $monitorId => $placa) {
                $porId[$monitorId] = $activos->get($placa)?->persona;
            }
        }
        return $porId;
    }

    /** @return array<int, array{marca: string, total: int}> */
    private function monitoresPorMarca(\Illuminate\Database\Eloquent\Builder $baseQuery): array
    {
        $query = (clone $baseQuery)
            ->selectRaw("COALESCE(NULLIF(TRIM(monitores.marca), ''), ?) AS marca", [__('Sin marca')])
            ->selectRaw('COUNT(*) AS total')
            ->groupBy('marca')
            ->orderByDesc('total');

        return $query->get()->map(fn ($r) => [
            'marca' => (string) $r->marca,
            'total' => (int) $r->total,
        ])->all();
    }

    public function create(): View
    {
        $cpus = Cpu::query()->with('activoCrv')->orderBy('nombre_maquina')->get();
        return view('activos.monitores.create', compact('cpus'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'marca' => 'nullable|string|max:100',
            'modelo' => 'nullable|string|max:100',
            'serial' => 'nullable|string|max:100',
            'placa' => 'nullable|string|max:100',
            'estado' => 'nullable|string|max:100',
            'cpu_id' => 'nullable|exists:cpus,id',
        ]);
        Monitor::create($validated);
        return redirect()->route('monitores.index')->with('success', __('Monitor creado.'));
    }

    public function show(Monitor $monitor): View
    {
        $user = request()->user();
        $isAdmin = $user && $user->can('monitores.update');

        if (! $isAdmin) {
            $persona = $this->currentPersona();
            if (! $persona || (int) ($monitor->cpu?->persona_id) !== $persona->id) {
                abort(404);
            }
        } else {
            $rid = $this->userRegionalId();
            if ($rid !== null && (int) ($monitor->cpu?->activoCrv?->regional_id) !== $rid) {
                abort(404);
            }
        }

        $monitor->load('cpu');
        return view('activos.monitores.show', compact('monitor'));
    }

    public function edit(Monitor $monitor): View
    {
        $cpus = Cpu::query()->with('activoCrv')->orderBy('nombre_maquina')->get();
        return view('activos.monitores.edit', compact('monitor', 'cpus'));
    }

    public function update(Request $request, Monitor $monitor): RedirectResponse
    {
        $validated = $request->validate([
            'marca' => 'nullable|string|max:100',
            'modelo' => 'nullable|string|max:100',
            'serial' => 'nullable|string|max:100',
            'placa' => 'nullable|string|max:100',
            'estado' => 'nullable|string|max:100',
            'cpu_id' => 'nullable|exists:cpus,id',
        ]);
        $monitor->update($validated);
        return redirect()->route('monitores.index')->with('success', __('Monitor actualizado.'));
    }

    public function destroy(Monitor $monitor): RedirectResponse
    {
        $monitor->delete();
        return redirect()->route('monitores.index')->with('success', __('Monitor eliminado.'));
    }
}
