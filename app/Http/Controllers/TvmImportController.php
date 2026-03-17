<?php

namespace App\Http\Controllers;

use App\Imports\TvmAssetsImport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Importación de activos TVM (Windows Defender) desde CSV.
 * Llave: Name (CSV) ↔ nombre_maquina (cpus).
 */
class TvmImportController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:tvm.import');
    }

    public function show(): View
    {
        return view('activos.import-tvm');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'archivo' => ['required', 'file', 'mimes:csv,txt', 'max:10240'],
        ]);

        $import = new TvmAssetsImport();
        try {
            $stats = $import->import($request->file('archivo'));
        } catch (\Throwable $e) {
            return back()->with('error', 'Error al importar: ' . $e->getMessage())->withInput();
        }

        $message = __('Importación TVM completada. Filas procesadas: :rows, CPUs vinculados: :linked.', [
            'rows' => $stats['rows'],
            'linked' => $stats['linked'],
        ]);

        return redirect()->route('tvm.import')->with('success', $message);
    }
}
