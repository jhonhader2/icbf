<?php

namespace App\Http\Controllers;

use App\Models\Cpu;
use App\Models\Persona;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class CpuController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:cpus.view')->only(['index', 'show']);
        $this->middleware('permission:cpus.create')->only(['create', 'store']);
        $this->middleware('permission:cpus.update')->only(['edit', 'update']);
        $this->middleware('permission:cpus.delete')->only(['destroy']);
    }

    public function index(Request $request): View
    {
        $user = $request->user();
        $tipoFiltro = $request->string('tipo')->trim();
        $isAdmin = $user && $user->can('cpus.update');

        if (! $isAdmin) {
            $persona = $this->currentPersona();
            if (! $persona) {
                abort(404);
            }
            $baseQuery = Cpu::query()->where('cpus.persona_id', $persona->id);
            $cpusPorTipo = $this->cpusPorTipo($baseQuery);
            $cpus = (clone $baseQuery)
                ->with(['persona', 'activoCrv.producto'])
                ->when($tipoFiltro->isNotEmpty(), function ($q) use ($tipoFiltro) {
                    $q->where(function ($q2) use ($tipoFiltro) {
                        $q2->whereRaw("COALESCE(NULLIF(TRIM(cpus.tipo_equipo), ''), '') = ?", [$tipoFiltro])
                            ->orWhereHas('activoCrv.producto', fn ($q3) => $q3->where('nombre', $tipoFiltro));
                    });
                })
                ->when($request->filled('q'), function ($q) use ($request) {
                    $term = '%' . $request->q . '%';
                    $q->where('placa', 'like', $term)
                        ->orWhere('nombre_maquina', 'like', $term)
                        ->orWhereHas('activoCrv', fn ($q2) => $q2->where('serie', 'like', $term));
                })
                ->orderBy('nombre_maquina')
                ->paginate(15)
                ->withQueryString();

            return view('activos.cpus.index', compact('cpus', 'cpusPorTipo'));
        }

        $rid = $this->userRegionalId();
        $baseQuery = Cpu::query()
            ->when($rid !== null, fn ($q) => $q->whereHas('activoCrv', fn ($q2) => $q2->where('regional_id', $rid)));

        $cpusPorTipo = $this->cpusPorTipo($baseQuery);

        $cpus = (clone $baseQuery)
            ->with(['persona', 'activoCrv.producto'])
            ->when($tipoFiltro->isNotEmpty(), function ($q) use ($tipoFiltro) {
                $q->where(function ($q2) use ($tipoFiltro) {
                    $q2->whereRaw("COALESCE(NULLIF(TRIM(cpus.tipo_equipo), ''), '') = ?", [$tipoFiltro])
                        ->orWhereHas('activoCrv.producto', fn ($q3) => $q3->where('nombre', $tipoFiltro));
                });
            })
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = '%' . $request->q . '%';
                $q->where('placa', 'like', $term)
                    ->orWhere('nombre_maquina', 'like', $term)
                    ->orWhereHas('activoCrv', fn ($q2) => $q2->where('serie', 'like', $term));
            })
            ->orderBy('nombre_maquina')
            ->paginate(15)
            ->withQueryString();

        return view('activos.cpus.index', compact('cpus', 'cpusPorTipo'));
    }

    /** @return array<int, array{tipo: string, total: int}> */
    private function cpusPorTipo(\Illuminate\Database\Eloquent\Builder $baseQuery): array
    {
        $query = (clone $baseQuery)
            ->leftJoin('activos_crv', 'activos_crv.id', '=', 'cpus.activo_crv_id')
            ->leftJoin('productos', 'productos.id', '=', 'activos_crv.producto_id')
            ->selectRaw("COALESCE(NULLIF(TRIM(cpus.tipo_equipo), ''), productos.nombre) AS tipo")
            ->selectRaw('COUNT(*) AS total')
            ->groupByRaw("COALESCE(NULLIF(TRIM(cpus.tipo_equipo), ''), productos.nombre)")
            ->orderByDesc('total');

        return $query->get()->map(fn ($r) => [
            'tipo' => $r->tipo ?: __('Sin tipo'),
            'total' => (int) $r->total,
        ])->all();
    }

    public function create(): View
    {
        $rid = $this->userRegionalId();
        $personas = Persona::query()
            ->orderBy('nombre')
            ->when($rid !== null, fn ($q) => $q->where('regional_id', $rid))
            ->get();
        return view('activos.cpus.create', compact('personas'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nombre_maquina' => 'nullable|string|max:255',
            'placa' => 'nullable|string|max:100',
            'estado' => 'nullable|string|max:100',
            'referencia_equipo' => 'nullable|string|max:255',
            'persona_id' => 'nullable|exists:personas,id',
        ]);
        Cpu::create($validated);
        return redirect()->route('cpus.index')->with('success', __('CPU creado.'));
    }

    public function show(Cpu $cpu): View
    {
        $user = request()->user();
        $isAdmin = $user && $user->can('cpus.update');

        if (! $isAdmin) {
            $persona = $this->currentPersona();
            if (! $persona || (int) $cpu->persona_id !== $persona->id) {
                abort(404);
            }
        } else {
            $rid = $this->userRegionalId();
            if ($rid !== null && (int) ($cpu->activoCrv?->regional_id) !== $rid) {
                abort(404);
            }
        }
        $cpu->load(['persona', 'activoCrv.producto', 'activoCrv.regional', 'monitor', 'teclado', 'mouse', 'tvmAssets' => fn ($q) => $q->orderByDesc('last_seen')]);
        return view('activos.cpus.show', compact('cpu'));
    }

    public function edit(Cpu $cpu): View
    {
        $rid = $this->userRegionalId();
        if ($rid !== null && (int) ($cpu->activoCrv?->regional_id) !== $rid) {
            abort(404);
        }
        $personas = Persona::query()
            ->orderBy('nombre')
            ->when($rid !== null, fn ($q) => $q->where('regional_id', $rid))
            ->get();
        return view('activos.cpus.edit', compact('cpu', 'personas'));
    }

    public function update(Request $request, Cpu $cpu): RedirectResponse
    {
        $rid = $this->userRegionalId();
        if ($rid !== null && (int) ($cpu->activoCrv?->regional_id) !== $rid) {
            abort(404);
        }
        $validated = $request->validate([
            'nombre_maquina' => 'nullable|string|max:255',
            'placa' => 'nullable|string|max:100',
            'estado' => 'nullable|string|max:100',
            'referencia_equipo' => 'nullable|string|max:255',
            'persona_id' => 'nullable|exists:personas,id',
        ]);
        $cpu->update($validated);
        return redirect()->route('cpus.index')->with('success', __('CPU actualizado.'));
    }

    public function destroy(Cpu $cpu): RedirectResponse
    {
        $rid = $this->userRegionalId();
        if ($rid !== null && (int) ($cpu->activoCrv?->regional_id) !== $rid) {
            abort(404);
        }
        $cpu->delete();
        return redirect()->route('cpus.index')->with('success', __('CPU eliminado.'));
    }
}
