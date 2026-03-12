<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ProductoController extends Controller
{
    public function index(Request $request): View
    {
        $productos = Producto::query()
            ->when($request->filled('q'), fn ($q) => $q->where('codigo', 'like', '%' . $request->q . '%')
                ->orWhere('nombre', 'like', '%' . $request->q . '%')
                ->orWhere('marca', 'like', '%' . $request->q . '%'))
            ->orderBy('codigo')
            ->paginate(15)
            ->withQueryString();
        return view('activos.productos.index', compact('productos'));
    }

    public function create(): View
    {
        return view('activos.productos.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'codigo' => 'required|string|max:255|unique:productos,codigo',
            'nombre' => 'nullable|string|max:255',
            'marca' => 'nullable|string|max:255',
            'modelo' => 'nullable|string|max:255',
        ]);
        Producto::create($validated);
        return redirect()->route('productos.index')->with('success', __('Producto creado.'));
    }

    public function show(Producto $producto): View
    {
        $producto->loadCount('activosCrv');
        return view('activos.productos.show', compact('producto'));
    }

    public function edit(Producto $producto): View
    {
        return view('activos.productos.edit', compact('producto'));
    }

    public function update(Request $request, Producto $producto): RedirectResponse
    {
        $validated = $request->validate([
            'codigo' => 'required|string|max:255|unique:productos,codigo,' . $producto->id,
            'nombre' => 'nullable|string|max:255',
            'marca' => 'nullable|string|max:255',
            'modelo' => 'nullable|string|max:255',
        ]);
        $producto->update($validated);
        return redirect()->route('productos.index')->with('success', __('Producto actualizado.'));
    }

    public function destroy(Producto $producto): RedirectResponse
    {
        $producto->delete();
        return redirect()->route('productos.index')->with('success', __('Producto eliminado.'));
    }
}
