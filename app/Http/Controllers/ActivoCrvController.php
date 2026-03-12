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
    public function index(Request $request): View
    {
        $activosCrv = ActivoCrv::query()
            ->with(['persona', 'producto', 'bodega'])
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
        $personas = Persona::orderBy('nombre')->get();
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
        ActivoCrv::create($validated);
        return redirect()->route('activos-crv.index')->with('success', __('Activo CRV creado.'));
    }

    public function show(ActivoCrv $activoCrv): View
    {
        $activoCrv->load(['persona', 'producto', 'regional', 'bodega', 'cpu.monitor', 'cpu.teclado', 'cpu.mouse']);
        return view('activos.activos-crv.show', compact('activoCrv'));
    }

    public function edit(ActivoCrv $activoCrv): View
    {
        $personas = Persona::orderBy('nombre')->get();
        $productos = Producto::orderBy('codigo')->get();
        $regionales = Regional::orderBy('nombre')->get();
        $bodegas = Bodega::orderBy('nombre')->get();
        return view('activos.activos-crv.edit', compact('activoCrv', 'personas', 'productos', 'regionales', 'bodegas'));
    }

    public function update(Request $request, ActivoCrv $activoCrv): RedirectResponse
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
        $activoCrv->update($validated);
        return redirect()->route('activos-crv.index')->with('success', __('Activo CRV actualizado.'));
    }

    public function destroy(ActivoCrv $activoCrv): RedirectResponse
    {
        $activoCrv->delete();
        return redirect()->route('activos-crv.index')->with('success', __('Activo CRV eliminado.'));
    }
}
