<?php

namespace App\Http\Controllers;

use App\Models\ActivoCrv;
use App\Models\Department;
use App\Models\Office;
use App\Models\Persona;
use App\Models\Regional;
use App\Models\Title;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class PersonaController extends Controller
{
    public function index(Request $request): View
    {
        $regionales = Regional::orderBy('nombre')->get();
        $regionalId = $request->filled('regional_id') ? (int) $request->regional_id : null;
        $departmentId = $request->filled('department_id') ? (int) $request->department_id : null;

        // Si hay regional seleccionada, solo departamentos con personas en esa regional
        $departments = $regionalId > 0
            ? Department::whereHas('personas', fn ($q) => $q->where('regional_id', $regionalId))->orderBy('nombre')->get()
            : Department::orderBy('nombre')->get();

        $personasPorDepartamentoEnRegional = collect();
        $regionalSeleccionada = null;
        if ($regionalId > 0) {
            $regionalSeleccionada = Regional::find($regionalId);
            $personasPorDepartamentoEnRegional = Persona::query()
                ->where('personas.regional_id', $regionalId)
                ->when($departmentId > 0, fn ($q) => $q->where('personas.department_id', $departmentId))
                ->select('departments.nombre as department_name')
                ->selectRaw('count(personas.id) as total')
                ->leftJoin('departments', 'personas.department_id', '=', 'departments.id')
                ->groupBy('personas.department_id', 'departments.nombre')
                ->orderByDesc('total')
                ->get();
        }

        $search = $request->string('q')->trim();
        $accountStatus = $request->account_status;
        $personas = Persona::query()
            ->with(['department', 'office', 'title', 'regional'])
            ->when($search->isNotEmpty(), fn ($q) => $q->where(function ($q) use ($search) {
                $q->where('personas.documento_identidad', 'like', '%' . $search . '%')
                    ->orWhere('personas.nombre', 'like', '%' . $search . '%')
                    ->orWhere('personas.full_name', 'like', '%' . $search . '%');
            }))
            ->when(in_array($accountStatus, ['0', '1'], true), fn ($q) => $q->where('personas.account_status', $accountStatus))
            ->when($regionalId > 0, fn ($q) => $q->where('personas.regional_id', $regionalId))
            ->when($departmentId > 0, fn ($q) => $q->where('personas.department_id', $departmentId))
            ->orderBy('personas.nombre')
            ->paginate(15)
            ->withQueryString();

        return view('activos.personas.index', compact('personas', 'departments', 'regionales', 'personasPorDepartamentoEnRegional', 'regionalSeleccionada'));
    }

    public function create(): View
    {
        $departments = Department::orderBy('nombre')->get();
        $offices = Office::orderBy('nombre')->get();
        $titles = Title::orderBy('nombre')->get();
        return view('activos.personas.create', compact('departments', 'offices', 'titles'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'documento_identidad' => 'required|string|max:50|unique:personas,documento_identidad',
            'nombre' => 'nullable|string|max:255',
            'department_id' => 'nullable|exists:departments,id',
            'account_status' => 'nullable|string|in:0,1',
            'office_id' => 'nullable|exists:offices,id',
            'title_id' => 'nullable|exists:titles,id',
            'full_name' => 'nullable|string|max:255',
            'email_address' => 'nullable|email|max:255',
        ]);
        Persona::create($validated);
        return redirect()->route('personas.index')->with('success', __('Persona creada.'));
    }

    public function show(Persona $persona): View
    {
        $persona->load(['department', 'office', 'title', 'cpus', 'activosCrv' => ['producto']]);
        $persona->setRelation('activosCrv', $persona->activosCrv->sortBy(
            fn (ActivoCrv $a) => mb_strtoupper($a->producto?->nombre ?? $a->producto?->codigo ?? ''),
            SORT_NATURAL | SORT_FLAG_CASE
        )->values());
        return view('activos.personas.show', compact('persona'));
    }

    public function edit(Persona $persona): View
    {
        $departments = Department::orderBy('nombre')->get();
        $offices = Office::orderBy('nombre')->get();
        $titles = Title::orderBy('nombre')->get();
        return view('activos.personas.edit', compact('persona', 'departments', 'offices', 'titles'));
    }

    public function update(Request $request, Persona $persona): RedirectResponse
    {
        $validated = $request->validate([
            'documento_identidad' => 'required|string|max:50|unique:personas,documento_identidad,' . $persona->id,
            'nombre' => 'nullable|string|max:255',
            'department_id' => 'nullable|exists:departments,id',
            'account_status' => 'nullable|string|in:0,1',
            'office_id' => 'nullable|exists:offices,id',
            'title_id' => 'nullable|exists:titles,id',
            'full_name' => 'nullable|string|max:255',
            'email_address' => 'nullable|email|max:255',
        ]);
        $persona->update($validated);
        return redirect()->route('personas.index')->with('success', __('Persona actualizada.'));
    }

    public function destroy(Persona $persona): RedirectResponse
    {
        $persona->delete();
        return redirect()->route('personas.index')->with('success', __('Persona eliminada.'));
    }
}
