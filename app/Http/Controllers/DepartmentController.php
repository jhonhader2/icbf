<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class DepartmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:departments.view')->only(['index', 'show']);
        $this->middleware('permission:departments.create')->only(['create', 'store']);
        $this->middleware('permission:departments.update')->only(['edit', 'update']);
        $this->middleware('permission:departments.delete')->only(['destroy']);
    }

    public function index(Request $request): View
    {
        $departments = Department::query()
            ->when($request->filled('q'), fn ($q) => $q->where('nombre', 'like', '%' . $request->q . '%'))
            ->orderBy('nombre')
            ->paginate(15)
            ->withQueryString();
        return view('activos.departments.index', compact('departments'));
    }

    public function create(): View
    {
        return view('activos.departments.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate(['nombre' => 'required|string|max:255|unique:departments,nombre']);
        Department::create($validated);
        return redirect()->route('departments.index')->with('success', __('Departamento creado.'));
    }

    public function show(Department $department): View
    {
        return view('activos.departments.show', compact('department'));
    }

    public function edit(Department $department): View
    {
        return view('activos.departments.edit', compact('department'));
    }

    public function update(Request $request, Department $department): RedirectResponse
    {
        $validated = $request->validate(['nombre' => 'required|string|max:255|unique:departments,nombre,' . $department->id]);
        $department->update($validated);
        return redirect()->route('departments.index')->with('success', __('Departamento actualizado.'));
    }

    public function destroy(Department $department): RedirectResponse
    {
        $department->delete();
        return redirect()->route('departments.index')->with('success', __('Departamento eliminado.'));
    }
}
