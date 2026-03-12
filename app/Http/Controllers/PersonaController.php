<?php

namespace App\Http\Controllers;

use App\Models\ActivoCrv;
use App\Models\Department;
use App\Models\Office;
use App\Models\Persona;
use App\Models\Regional;
use App\Models\Title;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class PersonaController extends Controller
{
    public function index(Request $request): View
    {
        $userRegionalId = $this->userRegionalId();
        $regionales = Regional::orderBy('nombre')->get();
        $regionalId = $userRegionalId ?? ($request->filled('regional_id') ? (int) $request->regional_id : null);
        $departmentId = $request->filled('department_id') ? (int) $request->department_id : null;

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
            ->when($userRegionalId !== null, fn ($q) => $q->where('personas.regional_id', $userRegionalId))
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

        $emailsConUsuario = User::whereIn('email', $personas->pluck('email_address')->filter()->unique()->values()->toArray())->pluck('email')->flip();

        $hayPersonasSinUsuario = Persona::query()
            ->when($userRegionalId !== null, fn ($q) => $q->where('regional_id', $userRegionalId))
            ->whereNotNull('email_address')
            ->where('email_address', '!=', '')
            ->whereNotIn('email_address', User::pluck('email'))
            ->exists();

        return view('activos.personas.index', compact('personas', 'departments', 'regionales', 'personasPorDepartamentoEnRegional', 'regionalSeleccionada', 'emailsConUsuario', 'hayPersonasSinUsuario'));
    }

    public function create(): View
    {
        $departments = Department::orderBy('nombre')->get();
        $offices = Office::orderBy('nombre')->get();
        $titles = Title::orderBy('nombre')->get();
        $regionales = Regional::orderBy('nombre')->get();
        return view('activos.personas.create', compact('departments', 'offices', 'titles', 'regionales'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'documento_identidad' => 'required|string|max:50|unique:personas,documento_identidad',
            'nombre' => 'nullable|string|max:255',
            'department_id' => 'nullable|exists:departments,id',
            'regional_id' => 'nullable|exists:regionales,id',
            'account_status' => 'nullable|string|in:0,1',
            'office_id' => 'nullable|exists:offices,id',
            'title_id' => 'nullable|exists:titles,id',
            'full_name' => 'nullable|string|max:255',
            'email_address' => 'required|email|max:255|unique:users,email',
        ]);

        $userRegionalId = $this->userRegionalId();
        if ($userRegionalId !== null) {
            $validated['regional_id'] = $userRegionalId;
        }

        DB::transaction(function () use ($validated) {
            Persona::create($validated);
            User::create([
                'name' => $validated['nombre'] ?? $validated['full_name'] ?? $validated['email_address'],
                'email' => $validated['email_address'],
                'password' => $validated['documento_identidad'],
                'regional_id' => $validated['regional_id'] ?? null,
            ]);
        });

        return redirect()->route('personas.index')->with('success', __('Persona y usuario creados.'));
    }

    public function show(Persona $persona): View
    {
        $rid = $this->userRegionalId();
        if ($rid !== null && (int) $persona->regional_id !== $rid) {
            abort(404);
        }
        $persona->load(['department', 'office', 'title', 'cpus', 'activosCrv' => ['producto']]);
        $persona->setRelation('activosCrv', $persona->activosCrv->sortBy(
            fn (ActivoCrv $a) => mb_strtoupper($a->producto?->nombre ?? $a->producto?->codigo ?? ''),
            SORT_NATURAL | SORT_FLAG_CASE
        )->values());
        $tieneUsuario = $persona->email_address && User::where('email', $persona->email_address)->exists();
        $puedeCrearUsuario = $persona->email_address && ! $tieneUsuario;
        return view('activos.personas.show', compact('persona', 'tieneUsuario', 'puedeCrearUsuario'));
    }

    public function edit(Persona $persona): View
    {
        $rid = $this->userRegionalId();
        if ($rid !== null && (int) $persona->regional_id !== $rid) {
            abort(404);
        }
        $departments = Department::orderBy('nombre')->get();
        $offices = Office::orderBy('nombre')->get();
        $titles = Title::orderBy('nombre')->get();
        $regionales = Regional::orderBy('nombre')->get();
        return view('activos.personas.edit', compact('persona', 'departments', 'offices', 'titles', 'regionales'));
    }

    public function update(Request $request, Persona $persona): RedirectResponse
    {
        $rid = $this->userRegionalId();
        if ($rid !== null && (int) $persona->regional_id !== $rid) {
            abort(404);
        }
        $validated = $request->validate([
            'documento_identidad' => 'required|string|max:50|unique:personas,documento_identidad,' . $persona->id,
            'nombre' => 'nullable|string|max:255',
            'department_id' => 'nullable|exists:departments,id',
            'regional_id' => 'nullable|exists:regionales,id',
            'account_status' => 'nullable|string|in:0,1',
            'office_id' => 'nullable|exists:offices,id',
            'title_id' => 'nullable|exists:titles,id',
            'full_name' => 'nullable|string|max:255',
            'email_address' => 'nullable|email|max:255',
        ]);
        if ($rid !== null) {
            $validated['regional_id'] = $rid;
        }
        $persona->update($validated);
        return redirect()->route('personas.index')->with('success', __('Persona actualizada.'));
    }

    public function destroy(Persona $persona): RedirectResponse
    {
        $rid = $this->userRegionalId();
        if ($rid !== null && (int) $persona->regional_id !== $rid) {
            abort(404);
        }
        $persona->delete();
        return redirect()->route('personas.index')->with('success', __('Persona eliminada.'));
    }

    /**
     * Crea un usuario de acceso para la persona (email = email_address, contraseña = documento_identidad).
     * Solo si la persona tiene email y aún no existe un usuario con ese correo.
     */
    public function crearUsuario(Persona $persona): RedirectResponse
    {
        $rid = $this->userRegionalId();
        if ($rid !== null && (int) $persona->regional_id !== $rid) {
            abort(404);
        }
        if (! $persona->email_address) {
            return redirect()->back()->with('error', __('La persona no tiene email; complételo en Editar para poder crear el usuario.'));
        }
        if (User::where('email', $persona->email_address)->exists()) {
            return redirect()->back()->with('error', __('Ya existe un usuario con el correo de esta persona.'));
        }
        User::create([
            'name' => $persona->nombre ?? $persona->full_name ?? $persona->email_address,
            'email' => $persona->email_address,
            'password' => $persona->documento_identidad,
            'regional_id' => $persona->regional_id,
        ]);
        return redirect()->back()->with('success', __('Usuario de acceso creado. Puede iniciar sesión con el email y el documento de identidad como contraseña.'));
    }

    /**
     * Crea usuarios de acceso para las personas seleccionadas (masivo).
     */
    public function crearUsuariosMasivo(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'persona_ids' => 'required|array|min:1',
            'persona_ids.*' => 'integer|exists:personas,id',
        ]);
        $rid = $this->userRegionalId();
        $personas = Persona::whereIn('id', $validated['persona_ids'])
            ->when($rid !== null, fn ($q) => $q->where('regional_id', $rid))
            ->get();
        $emailsExistentes = User::whereIn('email', $personas->pluck('email_address')->filter()->unique()->values()->toArray())->pluck('email')->flip();
        $creados = 0;
        $omitidos = 0;
        foreach ($personas as $persona) {
            if (! $persona->email_address) {
                $omitidos++;
                continue;
            }
            if (isset($emailsExistentes[$persona->email_address])) {
                $omitidos++;
                continue;
            }
            User::create([
                'name' => $persona->nombre ?? $persona->full_name ?? $persona->email_address,
                'email' => $persona->email_address,
                'password' => $persona->documento_identidad,
                'regional_id' => $persona->regional_id,
            ]);
            $creados++;
            $emailsExistentes[$persona->email_address] = true;
        }
        $msg = $creados > 0
            ? __(':count usuario(s) creado(s). Contraseña inicial: documento de identidad.', ['count' => $creados])
            : __('Ningún usuario creado (sin email o ya existían).');
        if ($omitidos > 0) {
            $msg .= ' ' . __(':count omitido(s).', ['count' => $omitidos]);
        }
        return redirect()->back()->with('success', $msg);
    }

    /**
     * Crea usuarios de acceso para todas las personas que tengan email y aún no tengan usuario.
     * Respeta el filtro por regional del usuario logueado.
     */
    public function crearUsuariosTodos(): RedirectResponse
    {
        $rid = $this->userRegionalId();
        $personas = Persona::query()
            ->whereNotNull('email_address')
            ->where('email_address', '!=', '')
            ->when($rid !== null, fn ($q) => $q->where('regional_id', $rid))
            ->get();
        $emailsExistentes = User::pluck('email')->flip();
        $creados = 0;
        foreach ($personas as $persona) {
            if (isset($emailsExistentes[$persona->email_address])) {
                continue;
            }
            User::create([
                'name' => $persona->nombre ?? $persona->full_name ?? $persona->email_address,
                'email' => $persona->email_address,
                'password' => $persona->documento_identidad,
                'regional_id' => $persona->regional_id,
            ]);
            $creados++;
            $emailsExistentes[$persona->email_address] = true;
        }
        $msg = $creados > 0
            ? __(':count usuario(s) creado(s). Contraseña inicial: documento de identidad.', ['count' => $creados])
            : __('No había personas sin usuario (todas con email ya tienen acceso).');
        return redirect()->back()->with('success', $msg);
    }
}
