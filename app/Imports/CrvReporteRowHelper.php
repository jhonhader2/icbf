<?php

namespace App\Imports;

use App\Support\StringNormalizer;
use Illuminate\Support\Collection;

/**
 * Lectura y normalización de celdas/filas del reporte CRV (placa, código producto, valores numéricos).
 */
class CrvReporteRowHelper
{
    public function rowToArray($row): array
    {
        if (is_array($row)) {
            return $row;
        }
        if ($row instanceof Collection) {
            return $row->all();
        }
        return [];
    }

    public function rowHasAnyData($row): bool
    {
        $arr = $this->rowToArray($row);
        for ($i = 0; $i <= 10; $i++) {
            $v = $arr[$i] ?? null;
            if ($v !== null && $v !== '') {
                return true;
            }
        }
        return false;
    }

    public function val(array $row, int $index): string
    {
        $v = $row[$index] ?? null;
        if ($v === null) {
            return '';
        }
        if (is_numeric($v) && (float) $v == (int) $v) {
            $v = (string) (int) $v;
        } else {
            $v = (string) $v;
        }
        return StringNormalizer::normalize($v);
    }

    public function celda(array $row, int $index): string
    {
        return StringNormalizer::normalize((string) ($row[$index] ?? ''));
    }

    /** Primer valor no vacío en los índices indicados. */
    public function primeraCeldaNoVacia(array $row, array $indices): string
    {
        foreach ($indices as $i) {
            $v = $this->celda($row, $i);
            if ($v !== '') {
                return $v;
            }
        }
        return '';
    }

    public function num(array $row, int $index): ?float
    {
        $v = $row[$index] ?? null;
        if ($v === null || $v === '') {
            return null;
        }
        if (is_numeric($v)) {
            return (float) $v;
        }
        return null;
    }

    /** Placa (4+ dígitos) en columnas B–E (índices 1–4). */
    public function findPlacaInRow(array $row): ?string
    {
        foreach ([1, 2, 3, 4] as $i) {
            $raw = $row[$i] ?? null;
            if ($raw === null || $raw === '') {
                continue;
            }
            if (is_numeric($raw)) {
                $v = (string) (int) (float) $raw;
            } else {
                $v = $this->val($row, $i);
            }
            if ($v !== '' && strlen($v) >= 4 && ctype_digit($v)) {
                return $v;
            }
        }
        return null;
    }

    /** Código de producto numérico en columnas G/H/I (6,7,8) y fallbacks. Retorna ['codigo' => string, 'offset' => int]. */
    public function findCodigoProductoInRow(array $row): array
    {
        $columnasCodigo = [6, 7, 8, 5, 9, 4, 10, 3, 11];
        foreach ($columnasCodigo as $offset) {
            $raw = $row[$offset] ?? null;
            if ($raw === null || $raw === '') {
                continue;
            }
            $codigo = $this->extractNumericCode($raw);
            if ($codigo !== '' && strlen($codigo) >= 3) {
                return ['codigo' => $codigo, 'offset' => $offset];
            }
        }
        return ['codigo' => '', 'offset' => 7];
    }

    public function extractNumericCode(mixed $raw): string
    {
        if (is_numeric($raw)) {
            return (string) (int) (float) $raw;
        }
        $s = StringNormalizer::normalize((string) $raw);
        return ($s !== '' && ctype_digit($s)) ? $s : '';
    }
}
