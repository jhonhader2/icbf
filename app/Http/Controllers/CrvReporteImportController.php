<?php

namespace App\Http\Controllers;

use App\Imports\CrvReporteImport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Controlador único para importar 20260310_crvReporte.xls (activos CRV por propietario = Persona).
 */
class CrvReporteImportController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:activos_crv.import');
    }

    public function show(): View
    {
        return view('activos.import-crv');
    }

    public function store(Request $request): RedirectResponse
    {
        ini_set('memory_limit', '1024M');

        $request->validate([
            'archivo' => ['required', 'file', 'mimes:xls,xlsx', 'max:20480'],
        ]);

        $import = new CrvReporteImport();
        try {
            DB::transaction(function () use ($request, $import) {
                Excel::import($import, $request->file('archivo'));
            });
        } catch (\Throwable $e) {
            return back()->with('error', 'Error al importar: ' . $e->getMessage())->withInput();
        }

        $stats = $import->getStats();
        $message = 'Importación CRV completada. Filas leídas: ' . $stats['rows']
            . ', regionales: ' . $stats['regionales']
            . ', bodegas: ' . $stats['bodegas']
            . ', personas: ' . $stats['personas']
            . ', activos creados/actualizados: ' . $stats['activos'] . '.';
        if ($stats['activos'] === 0) {
            $message .= ' Si no se guardó ningún activo, revise que el archivo sea crvReporte con encabezados en fila 13, jerarquía (código/nombre) en C/F, placa numérica en C/D/E y código producto en G/H/I.';
        }

        return redirect()->route('crv.import')->with('success', $message);
    }
}
