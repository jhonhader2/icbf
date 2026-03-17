<?php

namespace App\Http\Controllers;

use App\Models\Bodega;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class BodegaController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:bodegas.view')->only(['index', 'show']);
        $this->middleware('permission:bodegas.create')->only(['create', 'store']);
        $this->middleware('permission:bodegas.update')->only(['edit', 'update']);
        $this->middleware('permission:bodegas.delete')->only(['destroy']);
    }

    public function index(Request $request): View
    {
        $bodegas = Bodega::query()
            ->when($request->filled('q'), fn ($q) => $q->where('codigo', 'like', '%' . $request->q . '%')
                ->orWhere('nombre', 'like', '%' . $request->q . '%'))
            ->orderBy('nombre')
            ->paginate(15)
            ->withQueryString();
        return view('activos.bodegas.index', compact('bodegas'));
    }

    public function create(): View
    {
        return view('activos.bodegas.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'codigo' => 'required|string|max:50|unique:bodegas,codigo',
            'nombre' => 'required|string|max:255',
        ]);
        Bodega::create($validated);
        return redirect()->route('bodegas.index')->with('success', __('Bodega creada.'));
    }

    public function show(Bodega $bodega): View
    {
        return view('activos.bodegas.show', compact('bodega'));
    }

    public function edit(Bodega $bodega): View
    {
        return view('activos.bodegas.edit', compact('bodega'));
    }

    public function update(Request $request, Bodega $bodega): RedirectResponse
    {
        $validated = $request->validate([
            'codigo' => 'required|string|max:50|unique:bodegas,codigo,' . $bodega->codigo,
            'nombre' => 'required|string|max:255',
        ]);
        $bodega->update($validated);
        return redirect()->route('bodegas.index')->with('success', __('Bodega actualizada.'));
    }

    public function destroy(Bodega $bodega): RedirectResponse
    {
        $bodega->delete();
        return redirect()->route('bodegas.index')->with('success', __('Bodega eliminada.'));
    }
}
