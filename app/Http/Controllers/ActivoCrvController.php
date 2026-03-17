<?php

namespace App\Http\Controllers;

use App\Models\ActivoCrv;
use App\Models\Bodega;
use App\Models\Persona;
use App\Models\Producto;
use App\Models\Regional;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ActivoCrvController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:activos_crv.view')->only(['index', 'show']);
        $this->middleware('permission:activos_crv.create')->only(['create', 'store']);
        $this->middleware('permission:activos_crv.update')->only(['edit', 'update']);
        $this->middleware('permission:activos_crv.delete')->only(['destroy']);
    }

    public function index(Request $request): View
    {
        $user = $request->user();
        $isAdmin = $user && $user->can('activos_crv.update');

        if (! $isAdmin) {
            $persona = $this->currentPersona();
            if (! $persona) {
                abort(404);
            }
            $activosCrv = ActivoCrv::query()
                ->with(['persona', 'producto', 'bodega'])
                ->where('persona_id', $persona->id)
                ->when($request->filled('q'), fn ($q) => $q->where('placa', 'like', '%' . $request->q . '%')
                    ->orWhere('serie', 'like', '%' . $request->q . '%')
                    ->orWhereHas('producto', fn ($q2) => $q2->where('codigo', 'like', '%' . $request->q . '%')
                        ->orWhere('nombre', 'like', '%' . $request->q . '%')))
                ->orderBy('placa')
                ->paginate(15)
                ->withQueryString();

            return view('activos.activos-crv.index', compact('activosCrv'));
        }

        $rid = $this->userRegionalId();
        $activosCrv = ActivoCrv::query()
            ->with(['persona', 'producto', 'bodega'])
            ->when($rid !== null, fn ($q) => $q->where('regional_id', $rid))
            ->when($request->filled('persona_id'), fn ($q) => $q->where('persona_id', $request->persona_id))
            ->when($request->filled('producto_id'), fn ($q) => $q->where('producto_id', $request->producto_id))
            ->when($request->filled('q'), fn ($q) => $q->where('placa', 'like', '%' . $request->q . '%')
                ->orWhere('serie', 'like', '%' . $request->q . '%')
                ->orWhereHas('producto', fn ($q2) => $q2->where('codigo', 'like', '%' . $request->q . '%')
                    ->orWhere('nombre', 'like', '%' . $request->q . '%')))
            ->orderBy('placa')
            ->paginate(15)
            ->withQueryString();
        return view('activos.activos-crv.index', compact('activosCrv'));
    }

    public function create(): View
    {
        $rid = $this->userRegionalId();
        $personas = Persona::query()
            ->orderBy('nombre')
            ->when($rid !== null, fn ($q) => $q->where('regional_id', $rid))
            ->get();
        $productos = Producto::orderBy('codigo')->get();
        $regionales = Regional::orderBy('nombre')->get();
        $bodegas = Bodega::orderBy('nombre')->get();
        return view('activos.activos-crv.create', compact('personas', 'productos', 'regionales', 'bodegas'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'placa' => 'nullable|string|max:100',
            'producto_id' => 'nullable|exists:productos,id',
            'fecha_adquisicion' => 'nullable|date',
            'serie' => 'nullable|string|max:100',
            'costo_historico' => 'nullable|numeric',
            'depreciacion' => 'nullable|numeric',
            'persona_id' => 'nullable|exists:personas,id',
            'regional_id' => 'nullable|exists:regionales,id',
            'bodega_codigo' => 'nullable|string|max:50|exists:bodegas,codigo',
        ]);
        $rid = $this->userRegionalId();
        if ($rid !== null) {
            $validated['regional_id'] = $rid;
        }
        ActivoCrv::create($validated);
        return redirect()->route('activos-crv.index')->with('success', __('Activo CRV creado.'));
    }

    public function show(ActivoCrv $activoCrv): View
    {
        $user = request()->user();
        $isAdmin = $user && $user->can('activos_crv.update');

        if (! $isAdmin) {
            $persona = $this->currentPersona();
            if (! $persona || (int) $activoCrv->persona_id !== $persona->id) {
                abort(404);
            }
        } else {
            $rid = $this->userRegionalId();
            if ($rid !== null && (int) $activoCrv->regional_id !== $rid) {
                abort(404);
            }
        }
        $activoCrv->load(['persona', 'producto', 'regional', 'bodega', 'cpu.monitor', 'cpu.teclado', 'cpu.mouse']);
        return view('activos.activos-crv.show', compact('activoCrv'));
    }

    public function edit(ActivoCrv $activoCrv): View
    {
        $rid = $this->userRegionalId();
        if ($rid !== null && (int) $activoCrv->regional_id !== $rid) {
            abort(404);
        }
        $personas = Persona::query()
            ->orderBy('nombre')
            ->when($rid !== null, fn ($q) => $q->where('regional_id', $rid))
            ->get();
        $productos = Producto::orderBy('codigo')->get();
        $regionales = Regional::orderBy('nombre')->get();
        $bodegas = Bodega::orderBy('nombre')->get();
        return view('activos.activos-crv.edit', compact('activoCrv', 'personas', 'productos', 'regionales', 'bodegas'));
    }

    public function update(Request $request, ActivoCrv $activoCrv): RedirectResponse
    {
        $rid = $this->userRegionalId();
        if ($rid !== null && (int) $activoCrv->regional_id !== $rid) {
            abort(404);
        }
        $validated = $request->validate([
            'placa' => 'nullable|string|max:100',
            'producto_id' => 'nullable|exists:productos,id',
            'fecha_adquisicion' => 'nullable|date',
            'serie' => 'nullable|string|max:100',
            'costo_historico' => 'nullable|numeric',
            'depreciacion' => 'nullable|numeric',
            'persona_id' => 'nullable|exists:personas,id',
            'regional_id' => 'nullable|exists:regionales,id',
            'bodega_codigo' => 'nullable|string|max:50|exists:bodegas,codigo',
        ]);
        if ($rid !== null) {
            $validated['regional_id'] = $rid;
        }
        $activoCrv->update($validated);
        return redirect()->route('activos-crv.index')->with('success', __('Activo CRV actualizado.'));
    }

    public function destroy(ActivoCrv $activoCrv): RedirectResponse
    {
        $rid = $this->userRegionalId();
        if ($rid !== null && (int) $activoCrv->regional_id !== $rid) {
            abort(404);
        }
        $activoCrv->delete();
        return redirect()->route('activos-crv.index')->with('success', __('Activo CRV eliminado.'));
    }
}
