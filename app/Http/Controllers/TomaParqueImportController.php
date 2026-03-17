<?php

namespace App\Http\Controllers;

use App\Imports\TomaParqueDataEquiposImport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Importa Formato_Toma_Parque_2025.xlsx, hoja "Data Equipos", para completar datos de la tabla cpus.
 * Llave: PLACA CPU / PORTATIL (columna U).
 */
class TomaParqueImportController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:cpus.import');
    }

    public function show(): View
    {
        return view('activos.import-toma-parque');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'archivo' => ['required', 'file', 'mimes:xlsx,xls', 'max:20480'],
        ]);

        $import = new TomaParqueDataEquiposImport();
        try {
            DB::transaction(function () use ($request, $import) {
                Excel::import($import, $request->file('archivo'));
            });
        } catch (\Throwable $e) {
            return back()->with('error', 'Error al importar: ' . $e->getMessage())->withInput();
        }

        return redirect()->route('toma-parque.import')->with('success', __('Importación Toma Parque completada. Se actualizaron los CPUs por placa (columna U).'));
    }
}
