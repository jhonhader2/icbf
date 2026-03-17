<?php

namespace App\Http\Controllers;

use App\Exports\ParqueExport;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Controlador único para exportar el reporte final en formato Toma_Parque (Data Equipos).
 */
class ParqueExportController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:parque.export');
    }

    public function __invoke(): BinaryFileResponse
    {
        $filename = 'Toma_Parque_' . date('Y') . '.xlsx';
        return Excel::download(new ParqueExport(), $filename, \Maatwebsite\Excel\Excel::XLSX);
    }
}
