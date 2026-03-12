<?php

namespace App\Http\Controllers;

use App\Models\Cpu;
use App\Models\Persona;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class CpuController extends Controller
{
    public function index(Request $request): View
    {
        $cpus = Cpu::query()
            ->with(['persona', 'activoCrv'])
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = '%' . $request->q . '%';
                $q->where('placa', 'like', $term)
                    ->orWhere('nombre_maquina', 'like', $term)
                    ->orWhereHas('activoCrv', fn ($q2) => $q2->where('serie', 'like', $term));
            })
            ->orderBy('nombre_maquina')
            ->paginate(15)
            ->withQueryString();
        return view('activos.cpus.index', compact('cpus'));
    }

    public function create(): View
    {
        $personas = Persona::orderBy('nombre')->get();
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
        $cpu->load(['persona', 'activoCrv.producto', 'activoCrv.regional', 'monitor', 'teclado', 'mouse']);
        return view('activos.cpus.show', compact('cpu'));
    }

    public function edit(Cpu $cpu): View
    {
        $personas = Persona::orderBy('nombre')->get();
        return view('activos.cpus.edit', compact('cpu', 'personas'));
    }

    public function update(Request $request, Cpu $cpu): RedirectResponse
    {
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
        $cpu->delete();
        return redirect()->route('cpus.index')->with('success', __('CPU eliminado.'));
    }
}
