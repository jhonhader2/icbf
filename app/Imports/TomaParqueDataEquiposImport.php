<?php

namespace App\Imports;

use App\Models\ActivoCrv;
use App\Models\Cpu;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithStartRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

/**
 * Importa hoja "Data Equipos" del Formato_Toma_Parque_2025.xlsx.
 * Llave: PLACA CPU / PORTATIL (columna U). Actualiza o crea registros en cpus.
 */
class TomaParqueDataEquiposImport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            'Data Equipos' => new TomaParqueDataEquiposSheetImport,
        ];
    }
}

class TomaParqueDataEquiposSheetImport implements ToCollection, WithStartRow
{
    /** Columna U = PLACA CPU / PORTATIL (índice 20 en fila). */
    private const COL_PLACA = 20;

    /** Mapeo índice columna (0-based) => atributo en cpus. */
    private const MAP = [
        3 => 'nombre_maquina',   // D = NOMBRE MAQUINA
        9 => 'tipo_equipo',      // J = TIPO EQUIPO
        10 => 'referencia_equipo', // K = REFERENCIA EQUIPO
        11 => 'memoria_ram',     // L = MEMORIA RAM
        12 => 'so',              // M = SO
        13 => 'tipo_so',         // N = TIPO SO
        14 => 'bits',            // O = BITS
        15 => 'n_discos_fisicos', // P = N. DISCOS FISICOS
        16 => 'capacidad_disco', // Q = CAPACIDAD DISCO DURO
        17 => 'direccion_ip',    // R = DIRECCION IP
        18 => 'mac_address',     // S = MAC ADDRESS
        19 => null,              // T = SERIAL (se actualiza en activos_crv si hay activo_crv_id)
        22 => 'en_garantia',     // W = EN GARANTIA
        37 => 'office_version',  // AL = OFFICE VERSION
        38 => 'tarjeta_red_inalambrica', // AM = TARJETA RED INALAMBRICA
        39 => 'fecha_inventario', // AN = FECHA INVENTARIO
        40 => 'observaciones',   // AO = OBSERVACIONES
    ];

    public function startRow(): int
    {
        return 2;
    }

    public function collection(Collection $rows): void
    {
        foreach ($rows as $row) {
            $row = $row instanceof Collection ? $row->all() : $row;
            if (! is_array($row)) {
                continue;
            }
            $placa = $this->valor($row, self::COL_PLACA);
            if ($placa === '') {
                continue;
            }
            $this->actualizarCpu($row, $placa);
        }
    }

    private function valor(array $row, int $index): string
    {
        $v = $row[$index] ?? null;
        if ($v === null) {
            return '';
        }
        if (is_numeric($v) && (float) $v === (int) $v) {
            $v = (string) (int) $v;
        } else {
            $v = trim((string) $v);
        }
        return $v;
    }

    private function actualizarCpu(array $row, string $placa): void
    {
        $data = ['placa' => $placa];

        foreach (self::MAP as $colIndex => $attribute) {
            if ($attribute === null) {
                continue;
            }
            $val = $this->valor($row, $colIndex);
            if ($attribute === 'fecha_inventario') {
                $data[$attribute] = $this->parsearFecha($row, $colIndex);
            } else {
                $data[$attribute] = $val !== '' ? $val : null;
            }
        }

        $cpu = Cpu::query()->updateOrCreate(['placa' => $placa], $data);

        $serial = $this->valor($row, 19);
        if ($serial !== '' && $cpu->activo_crv_id) {
            ActivoCrv::query()->where('id', $cpu->activo_crv_id)->update(['serie' => $serial]);
        }
    }

    private function parsearFecha(array $row, int $colIndex): ?string
    {
        $v = $row[$colIndex] ?? null;
        $result = null;

        if ($v !== null && $v !== '') {
            if (is_numeric($v)) {
                try {
                    $result = Date::excelToDateTimeObject($v)->format('Y-m-d');
                } catch (\Throwable $e) {
                    // deja $result en null
                }
            } else {
                $parsed = date_parse($v);
                if ($parsed['error_count'] === 0 && ! empty($parsed['year']) && ! empty($parsed['month']) && ! empty($parsed['day'])) {
                    $result = sprintf('%04d-%02d-%02d', $parsed['year'], $parsed['month'], $parsed['day']);
                }
            }
        }

        return $result;
    }
}
