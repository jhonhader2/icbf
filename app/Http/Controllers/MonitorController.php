<?php

namespace App\Http\Controllers;

use App\Models\Cpu;
use App\Models\Monitor;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class MonitorController extends Controller
{
    public function index(Request $request): View
    {
        $monitores = Monitor::query()
            ->with('cpu')
            ->when($request->filled('q'), fn ($q) => $q->where('serial', 'like', '%' . $request->q . '%')
                ->orWhere('placa', 'like', '%' . $request->q . '%')
                ->orWhere('marca', 'like', '%' . $request->q . '%'))
            ->orderBy('marca')
            ->paginate(15)
            ->withQueryString();
        return view('activos.monitores.index', compact('monitores'));
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
