<?php

namespace App\Http\Controllers;

use App\Models\Cpu;
use App\Models\Teclado;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class TecladoController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:teclados.view')->only(['index', 'show']);
        $this->middleware('permission:teclados.create')->only(['create', 'store']);
        $this->middleware('permission:teclados.update')->only(['edit', 'update']);
        $this->middleware('permission:teclados.delete')->only(['destroy']);
    }

    public function index(Request $request): View
    {
        $teclados = Teclado::query()
            ->with('cpu')
            ->when($request->filled('q'), fn ($q) => $q->where('serial', 'like', '%' . $request->q . '%')
                ->orWhere('placa', 'like', '%' . $request->q . '%'))
            ->orderBy('marca')
            ->paginate(15)
            ->withQueryString();
        return view('activos.teclados.index', compact('teclados'));
    }

    public function create(): View
    {
        $cpus = Cpu::query()->with('activoCrv')->orderBy('nombre_maquina')->get();
        return view('activos.teclados.create', compact('cpus'));
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
        Teclado::create($validated);
        return redirect()->route('teclados.index')->with('success', __('Teclado creado.'));
    }

    public function show(Teclado $teclado): View
    {
        $teclado->load('cpu');
        return view('activos.teclados.show', compact('teclado'));
    }

    public function edit(Teclado $teclado): View
    {
        $cpus = Cpu::query()->with('activoCrv')->orderBy('nombre_maquina')->get();
        return view('activos.teclados.edit', compact('teclado', 'cpus'));
    }

    public function update(Request $request, Teclado $teclado): RedirectResponse
    {
        $validated = $request->validate([
            'marca' => 'nullable|string|max:100',
            'modelo' => 'nullable|string|max:100',
            'serial' => 'nullable|string|max:100',
            'placa' => 'nullable|string|max:100',
            'estado' => 'nullable|string|max:100',
            'cpu_id' => 'nullable|exists:cpus,id',
        ]);
        $teclado->update($validated);
        return redirect()->route('teclados.index')->with('success', __('Teclado actualizado.'));
    }

    public function destroy(Teclado $teclado): RedirectResponse
    {
        $teclado->delete();
        return redirect()->route('teclados.index')->with('success', __('Teclado eliminado.'));
    }
}
