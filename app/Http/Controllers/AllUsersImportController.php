<?php

namespace App\Http\Controllers;

use App\Imports\AllUsersImport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Controlador único para importar All Users.xlsx (directorio activo → Personas).
 */
class AllUsersImportController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:personas.import');
    }

    public function show(): View
    {
        return view('activos.import-all-users');
    }

    public function store(Request $request): RedirectResponse
    {
        ini_set('memory_limit', '1024M');

        $request->validate([
            'archivo' => ['required', 'file', 'mimes:xlsx,xls', 'max:20480'],
        ]);

        try {
            DB::transaction(function () use ($request) {
                Excel::import(new AllUsersImport(), $request->file('archivo'));
            });
        } catch (\Throwable $e) {
            Log::error('AllUsersImport error', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            $displayMsg = strlen($e->getMessage()) > 200 ? substr($e->getMessage(), 0, 197) . '...' : $e->getMessage();
            return redirect()->route('personas.import')
                ->with('error', 'Error al importar: ' . $displayMsg)
                ->withInput();
        }

        return redirect()->route('personas.import')->with('success', 'Importación completada.');
    }
}
