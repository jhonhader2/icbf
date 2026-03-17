<?php

namespace App\Http\Controllers;

use App\Models\Cpu;
use App\Models\Mouse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class MouseController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:mice.view')->only(['index', 'show']);
        $this->middleware('permission:mice.create')->only(['create', 'store']);
        $this->middleware('permission:mice.update')->only(['edit', 'update']);
        $this->middleware('permission:mice.delete')->only(['destroy']);
    }

    public function index(Request $request): View
    {
        $mice = Mouse::query()
            ->with('cpu')
            ->when($request->filled('q'), fn ($q) => $q->where('serial', 'like', '%' . $request->q . '%')
                ->orWhere('marca', 'like', '%' . $request->q . '%'))
            ->orderBy('marca')
            ->paginate(15)
            ->withQueryString();
        return view('activos.mice.index', compact('mice'));
    }

    public function create(): View
    {
        $cpus = Cpu::query()->with('activoCrv')->orderBy('nombre_maquina')->get();
        return view('activos.mice.create', compact('cpus'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'marca' => 'nullable|string|max:100',
            'modelo' => 'nullable|string|max:100',
            'serial' => 'nullable|string|max:100',
            'estado' => 'nullable|string|max:100',
            'cpu_id' => 'nullable|exists:cpus,id',
        ]);
        Mouse::create($validated);
        return redirect()->route('mice.index')->with('success', __('Mouse creado.'));
    }

    public function show(Mouse $mouse): View
    {
        $mouse->load('cpu');
        return view('activos.mice.show', compact('mouse'));
    }

    public function edit(Mouse $mouse): View
    {
        $cpus = Cpu::query()->with('activoCrv')->orderBy('nombre_maquina')->get();
        return view('activos.mice.edit', compact('mouse', 'cpus'));
    }

    public function update(Request $request, Mouse $mouse): RedirectResponse
    {
        $validated = $request->validate([
            'marca' => 'nullable|string|max:100',
            'modelo' => 'nullable|string|max:100',
            'serial' => 'nullable|string|max:100',
            'estado' => 'nullable|string|max:100',
            'cpu_id' => 'nullable|exists:cpus,id',
        ]);
        $mouse->update($validated);
        return redirect()->route('mice.index')->with('success', __('Mouse actualizado.'));
    }

    public function destroy(Mouse $mouse): RedirectResponse
    {
        $mouse->delete();
        return redirect()->route('mice.index')->with('success', __('Mouse eliminado.'));
    }
}
