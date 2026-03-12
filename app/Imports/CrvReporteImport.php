<?php

namespace App\Imports;

use App\Models\ActivoCrv;
use App\Models\Bodega;
use App\Models\Cpu;
use App\Models\Monitor;
use App\Models\Persona;
use App\Models\Producto;
use App\Models\Regional;
use App\Support\StringNormalizer;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

/**
 * Importa 20260310_crvReporte.xls. Encabezados fila 13. Estructura jerárquica + filas de activo.
 * Jerarquía: código en D (3), nombre en F (5): REGIONAL / BODEGA / Tercero (persona). Filas "Subtotal" se ignoran.
 * Fila de activo: Placa en D (3), Producto en H (7). Primer producto suele estar en fila Excel 19 (índice 5).
 * Si el código de producto es 203002000005 (CPU), se registra también en la tabla cpus.
 * Si el código es 203002000004 (Monitor), se registra también en la tabla monitores.
 */
class CrvReporteImport implements ToCollection, WithStartRow
{
    /** Código de producto que identifica CPU en el reporte CRV. */
    public const CODIGO_PRODUCTO_CPU = '203002000005';

    /** Código de producto que identifica Monitor en el reporte CRV. */
    public const CODIGO_PRODUCTO_MONITOR = '203002000004';

    private CrvReporteRowHelper $rowHelper;

    public function __construct()
    {
        $this->rowHelper = new CrvReporteRowHelper;
    }

    /** Estadísticas para mostrar al usuario tras la importación. */
    public array $stats = [
        'rows' => 0,
        'regionales' => 0,
        'bodegas' => 0,
        'personas' => 0,
        'productos' => 0,
        'activos' => 0,
    ];

    /** Fila Excel (1-based). Encabezados en 13, datos desde 14. */
    public function startRow(): int
    {
        return 14;
    }

    public function getStats(): array
    {
        return $this->stats;
    }

    public function collection(Collection $rows): void
    {
        $regionalId = null;
        $bodegaCodigo = null;
        $personaId = null;
        $this->stats['rows'] = $rows->count();

        foreach ($rows as $row) {
            $row = $row instanceof Collection ? $row->all() : $row;
            if (! is_array($row)) {
                continue;
            }
            if (! $this->rowHelper->rowHasAnyData($row)) {
                continue;
            }
            $colCodigo = $this->rowHelper->val($row, 2);
            $colNombre = $this->rowHelper->celda($row, 5);

            [$regionalId, $bodegaCodigo, $personaId] = $this->applyHierarchy($colCodigo, $colNombre, $regionalId, $bodegaCodigo, $personaId);

            $colNombreUpper = mb_strtoupper($colNombre);
            $esFilaJerarquia = $colNombre !== '' && (str_starts_with($colNombreUpper, 'REGIONAL') || str_contains($colNombreUpper, 'BODEGA'));
            $placaVal = $esFilaJerarquia ? null : $this->rowHelper->findPlacaInRow($row);
            $productoVal = $this->rowHelper->findCodigoProductoInRow($row);
            $this->persistirProductoSiHayYActivoSiAplica($row, $productoVal, $esFilaJerarquia, $placaVal, $personaId, $regionalId, $bodegaCodigo);
        }

        $this->logDebugIfEmpty($rows, $personaId);
    }

    /** Recorre el archivo: crea/actualiza TODOS los productos con código numérico; luego crea activo si hay placa+producto. */
    private function persistirProductoSiHayYActivoSiAplica(array $row, array $productoVal, bool $esFilaJerarquia, ?string $placaVal, ?int $personaId, ?int $regionalId, ?string $bodegaCodigo): void
    {
        if ($productoVal['codigo'] !== '') {
            $this->ensureProductoFromRow($row, $productoVal['offset'], $productoVal['codigo']);
            $this->stats['productos']++;
        }
        if ($this->debePersistirActivo($esFilaJerarquia, $placaVal, $productoVal['codigo'])) {
            $this->createOrUpdateActivo($row, $personaId, $regionalId, $bodegaCodigo, $placaVal, $productoVal);
            $this->stats['activos']++;
        }
    }

    private function debePersistirActivo(bool $esFilaJerarquia, ?string $placaVal, string $codigoProducto): bool
    {
        return ! $esFilaJerarquia && $placaVal !== null && $codigoProducto !== '';
    }

    private function logDebugIfEmpty(Collection $rows, ?int $lastPersonaId): void
    {
        if ($this->stats['activos'] > 0) {
            return;
        }
        $logFile = storage_path('logs/import-debug.log');
        $firstDataRow = $rows->first(fn ($r) => $this->rowHelper->rowHasAnyData($r));
        $firstDataArr = [];
        if ($firstDataRow !== null) {
            $firstDataArr = is_array($firstDataRow) ? $firstDataRow : $firstDataRow->all();
        }
        $dataPreview = $firstDataArr !== [] ? json_encode(array_slice($firstDataArr, 0, 12)) : 'ninguna';
        $firstPlacaRow = $rows->first(function ($r) {
            $arr = $this->rowHelper->rowToArray($r);
            $placa = trim((string) ($arr[2] ?? ''));
            return $placa !== '' && strlen($placa) >= 4 && is_numeric($placa);
        });
        $placaRowPreview = 'ninguna';
        if ($firstPlacaRow !== null) {
            $placaRowPreview = json_encode(array_slice($this->rowHelper->rowToArray($firstPlacaRow), 0, 25));
        }
        $fila19 = $rows->get(5);
        $fila19Preview = $fila19 !== null ? json_encode(array_slice($this->rowHelper->rowToArray($fila19), 0, 20)) : 'no existe';
        $line = date('Y-m-d H:i:s') . " CRV: 0 activos. Filas: {$this->stats['rows']}. Reg/Bod/Per: {$this->stats['regionales']}/{$this->stats['bodegas']}/{$this->stats['personas']}. Ult personaId: " . ($lastPersonaId ?? 'null') . ". Primera fila datos: {$dataPreview}. Fila Excel 19 (primer producto, 20 cols): {$fila19Preview}. Primera fila tipo placa (25 cols): {$placaRowPreview}\n";
        @file_put_contents($logFile, $line, FILE_APPEND | LOCK_EX);

        $this->logWhyNoActivos($rows);
    }

    /** Escribe en log por qué no se consideró ninguna fila como activo (muestras de filas con placa sin código y viceversa). */
    private function logWhyNoActivos(Collection $rows): void
    {
        $state = ['regionalId' => null, 'bodegaCodigo' => null, 'personaId' => null];
        $samples = ['con_placa_sin_codigo' => null, 'con_codigo_sin_placa' => null];

        foreach ($rows as $row) {
            $arr = $this->rowHelper->rowToArray($row);
            if ($arr === []) {
                continue;
            }
            $this->applyStateAndCollectSamples($arr, $state, $samples);
            if ($samples['con_placa_sin_codigo'] !== null && $samples['con_codigo_sin_placa'] !== null) {
                break;
            }
        }
        $logFile = storage_path('logs/import-debug.log');
        $out = "Diagnóstico: fila con placa sin código (cols 0-14): " . json_encode($samples['con_placa_sin_codigo']) . "\n";
        $out .= "Fila con código sin placa (cols 0-14): " . json_encode($samples['con_codigo_sin_placa']) . "\n";
        @file_put_contents($logFile, $out, FILE_APPEND | LOCK_EX);
    }

    private function applyStateAndCollectSamples(array $row, array &$state, array &$samples): void
    {
        $colCodigo = $this->rowHelper->val($row, 2);
        $colNombre = $this->rowHelper->val($row, 5);
        if ($colCodigo === '' && $colNombre === '' && $this->rowHelper->val($row, 7) === '' && $this->rowHelper->findPlacaInRow($row) === null) {
            return;
        }
        [$state['regionalId'], $state['bodegaCodigo'], $state['personaId']] = $this->applyHierarchy(
            $colCodigo, $colNombre, $state['regionalId'], $state['bodegaCodigo'], $state['personaId']
        );
        if ($state['personaId'] === null) {
            return;
        }
        $colNombreUpper = mb_strtoupper($colNombre);
        $esJerarquia = $colNombre !== '' && (str_starts_with($colNombreUpper, 'REGIONAL') || str_contains($colNombreUpper, 'BODEGA'));
        $placaVal = $esJerarquia ? null : $this->rowHelper->findPlacaInRow($row);
        $productoVal = $this->rowHelper->findCodigoProductoInRow($row);

        if ($placaVal !== null && $productoVal['codigo'] === '' && $samples['con_placa_sin_codigo'] === null) {
            $samples['con_placa_sin_codigo'] = array_slice($row, 0, 15);
        }
        if ($productoVal['codigo'] !== '' && $placaVal === null && $samples['con_codigo_sin_placa'] === null) {
            $samples['con_codigo_sin_placa'] = array_slice($row, 0, 15);
        }
    }

    /**
     * Actualiza regional/bodega/persona según jerarquía (D=código, F=nombre). Ignora filas "Subtotal". Retorna [regionalId, bodegaCodigo, personaId].
     */
    private function applyHierarchy(string $colCodigo, string $colNombre, ?int $regionalId, ?string $bodegaCodigo, ?int $personaId): array
    {
        $r = $regionalId;
        $b = $bodegaCodigo;
        $p = $personaId;
        if ($colCodigo !== '' && $colNombre !== '') {
            $colNombreUpper = mb_strtoupper($colNombre);
            if (str_contains($colNombreUpper, 'SUBTOTAL')) {
                return [$r, $b, $p];
            }
            if (str_starts_with($colNombreUpper, 'REGIONAL')) {
                $r = $this->ensureRegional($colCodigo, $colNombre) ?? $regionalId;
                $this->stats['regionales']++;
            } elseif (str_contains($colNombreUpper, 'BODEGA')) {
                $b = $this->ensureBodega($colCodigo, $colNombre);
                $this->stats['bodegas']++;
            } else {
                $personaIdResuelto = $this->resolvePersonaIdFromHierarchy($colCodigo, $colNombre);
                if ($personaIdResuelto !== null) {
                    $p = $personaIdResuelto;
                    $this->stats['personas']++;
                }
            }
        }
        return [$r, $b, $p];
    }

    private function resolvePersonaIdFromHierarchy(string $colCodigo, string $colNombre): ?int
    {
        $colCodigoLower = mb_strtolower(trim($colCodigo));
        $esEncabezado = in_array($colCodigoLower, ['documento', 'cédula', 'cedula', 'doc', 'codigo', 'código', 'placa', 'nombre', 'descripción', 'descripcion'], true)
            || (strlen($colCodigo) < 3 && trim($colNombre) === '');
        $nombreTrim = trim($colNombre);
        $codigoTrim = trim($colCodigo);
        // No crear persona si es sección/categoría: nombre "95 TERRENOS", "95-RECIBIDOS..."; código "95", "95-..."
        $nombreEsSeccion = $nombreTrim !== '' && preg_match('/^\d{2,}/u', $nombreTrim);
        $codigoEsSeccion = $codigoTrim !== '' && (
            preg_match('/^\d{2,}[-.]/u', $codigoTrim)
            || (strlen($codigoTrim) <= 4 && preg_match('/^\d+$/u', $codigoTrim))
        );
        $esSeccionNumerica = $nombreEsSeccion || $codigoEsSeccion;
        if ($esEncabezado || $esSeccionNumerica) {
            return null;
        }
        $persona = Persona::query()->updateOrCreate(
            ['documento_identidad' => $colCodigo],
            ['nombre' => $colNombre !== '' ? $colNombre : null]
        );
        return $persona->id;
    }

    private function ensureRegional(string $codigo, string $nombre): ?int
    {
        $nombre = StringNormalizer::normalize($nombre ?: $codigo);
        if ($nombre === '') {
            return null;
        }
        $regional = Regional::firstOrCreate(
            ['nombre' => $nombre],
            ['nombre' => $nombre]
        );
        return $regional->id;
    }

    private function ensureBodega(string $codigo, string $nombre): string
    {
        $codigo = StringNormalizer::normalize($codigo);
        $nombre = StringNormalizer::normalize($nombre);
        Bodega::query()->firstOrCreate(
            ['codigo' => $codigo],
            ['nombre' => $nombre ?: $codigo]
        );
        return $codigo;
    }

    private function createOrUpdateActivo(array $row, ?int $personaId, ?int $regionalId, ?string $bodegaCodigo, string $placa, array $productoVal): void
    {
        $productoId = $this->ensureProductoFromRow($row, $productoVal['offset'], $productoVal['codigo']);
        $serieEquipo = $this->rowHelper->val($row, $productoVal['offset'] + 9);
        $costo = $this->rowHelper->num($row, 25) ?? $this->rowHelper->num($row, 24);
        $depreciacion = $this->rowHelper->num($row, 29);

        $fecha = null;
        $colAdquisicion = $row[$productoVal['offset'] + 6] ?? $row[13] ?? null;
        if ($colAdquisicion !== null && is_numeric($colAdquisicion)) {
            try {
                $fecha = Date::excelToDateTimeObject($colAdquisicion)->format('Y-m-d');
            } catch (\Throwable $e) {
                // dejar null si falla la conversión
            }
        }

        $activo = ActivoCrv::query()->updateOrCreate(
            [
                'placa' => $placa,
                'persona_id' => $personaId,
            ],
            [
                'producto_id' => $productoId,
                'fecha_adquisicion' => $fecha,
                'serie' => $serieEquipo ?: null,
                'costo_historico' => $costo,
                'depreciacion' => $depreciacion,
                'regional_id' => $regionalId,
                'bodega_codigo' => $bodegaCodigo,
            ]
        );

        if ($productoVal['codigo'] === self::CODIGO_PRODUCTO_CPU) {
            $this->createOrUpdateCpu($placa, $personaId, $activo->id);
        }
        if ($productoVal['codigo'] === self::CODIGO_PRODUCTO_MONITOR) {
            $this->createOrUpdateMonitor($row, $placa, $activo->id, $productoVal);
        }
    }

    /**
     * Crea o actualiza registro en cpus cuando el activo importado es CPU (código 203002000005).
     * Vincula por activo_crv_id; placa y persona se mantienen para búsquedas/listados.
     */
    private function createOrUpdateCpu(string $placa, ?int $personaId, int $activoCrvId): void
    {
        Cpu::query()->updateOrCreate(
            ['placa' => $placa],
            [
                'persona_id' => $personaId,
                'activo_crv_id' => $activoCrvId,
            ]
        );
    }

    /**
     * Crea o actualiza registro en monitores cuando el activo importado es Monitor (código 203002000004).
     * Vincula por activo_crv_id; marca, modelo y serial desde la fila (misma estructura que CPU).
     */
    private function createOrUpdateMonitor(array $row, string $placa, int $activoCrvId, array $productoVal): void
    {
        $offset = $productoVal['offset'];
        $serie = $this->rowHelper->val($row, $offset + 9);
        $marca = $this->rowHelper->primeraCeldaNoVacia($row, [$offset + 11, $offset + 12, $offset + 13]);
        $modelo = $this->rowHelper->primeraCeldaNoVacia($row, [$offset + 14, $offset + 15, $offset + 16]);

        Monitor::query()->updateOrCreate(
            ['placa' => $placa],
            [
                'activo_crv_id' => $activoCrvId,
                'marca' => $marca ?: null,
                'modelo' => $modelo ?: null,
                'serial' => $serie ?: null,
            ]
        );
    }

    /**
     * Crea o actualiza producto: código ya validado; nombre/marca/modelo desde la fila.
     * La serie es por unidad y va en ActivoCrv, no en Producto.
     */
    private function ensureProductoFromRow(array $row, int $offset, string $codigoNumerico): ?int
    {
        if ($codigoNumerico === '') {
            return null;
        }
        $nombre = $this->rowHelper->celda($row, $offset + 3);
        $marca = $this->rowHelper->primeraCeldaNoVacia($row, [$offset + 11, $offset + 12, $offset + 13]);
        $modelo = $this->rowHelper->primeraCeldaNoVacia($row, [$offset + 14, $offset + 15, $offset + 16]);

        $producto = Producto::query()->updateOrCreate(
            ['codigo' => $codigoNumerico],
            [
                'nombre' => $nombre ?: null,
                'marca' => $marca ?: null,
                'modelo' => $modelo ?: null,
            ]
        );

        return $producto->id;
    }
}
