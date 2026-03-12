<?php

namespace App\Http\Controllers;

use App\Models\Regional;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class RegionalController extends Controller
{
    public function index(Request $request): View
    {
        $regionales = Regional::query()
            ->when($request->filled('q'), fn ($q) => $q->where('nombre', 'like', '%' . $request->q . '%'))
            ->orderBy('nombre')
            ->paginate(15)
            ->withQueryString();
        return view('activos.regionales.index', compact('regionales'));
    }

    public function create(): View
    {
        return view('activos.regionales.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
        ]);
        Regional::create($validated);
        return redirect()->route('regionales.index')->with('success', __('Regional creado.'));
    }

    public function show(Regional $regional): View
    {
        return view('activos.regionales.show', ['regional' => $regional]);
    }

    public function edit(Regional $regional): View
    {
        return view('activos.regionales.edit', ['regional' => $regional]);
    }

    public function update(Request $request, Regional $regional): RedirectResponse
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
        ]);
        $regional->update($validated);
        return redirect()->route('regionales.index')->with('success', __('Regional actualizado.'));
    }

    public function destroy(Regional $regional): RedirectResponse
    {
        $regional->delete();
        return redirect()->route('regionales.index')->with('success', __('Regional eliminado.'));
    }
}
