<?php

namespace App\Exports;

use App\Models\Cpu;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

/**
 * Exporta reporte final en formato Toma_Parque (Data Equipos): encabezados A→AR, una fila por CPU con persona, monitor, teclado, mouse.
 */
class ParqueExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection(): Collection
    {
        return Cpu::query()
            ->with(['persona', 'activoCrv.producto', 'activoCrv.regional', 'monitor', 'teclado', 'mouse'])
            ->orderBy('id')
            ->get();
    }

    public function headings(): array
    {
        return [
            'NOMBRE INGENIERO REGIONAL QUE DILIGENCIO',  // A
            'REGIONAL',                                   // B
            'DEPENDENCIAS',                               // C
            'NOMBRE DE MAQUINA',                          // D
            'NOMBRE COMPLETO DEL USUARIO',                // E
            'CEDULA',                                     // F
            'TIPO DE VINCULACIÓN USUARIO',                // H
            'CARGO',                                      // I
            'TIPO EQUIPO',                                // J
            'REFERENCIA EQUIPO',                          // K
            'FECHA ADQUISICIÓN DE EQUIPO',                 // L
            'MEMORIA RAM',                                // M
            'SO',                                         // N
            'PROCESADOR',                                 // O
            'TIPO SO',                                    // P
            'BITS',                                       // Q
            'NºDISCOS FISICOS',                           // R
            'CAPACIDAD DISCO DURO',                        // S
            'DIRECCION IP',                               // T
            'MAC ADDRESS',                                // U
            'Serial',                                     // V
            'PLACA CPU / PORTATIL',                       // W
            'ESTADO CPU/PORTATIL',                        // X
            'ELEMENTO SE ENCUENTRA EN GARANTÍA',          // Y
            'AÑO DE ADQUISICIÓN',                         // Z
            'MARCA MONITOR',                               // AA
            'MODELO MONITOR',                              // AB
            'SERIAL MONITOR',                             // AC
            'PLACA MONITOR',                               // AD
            'ESTADO MONITOR',                              // AE
            '',                                           // AF
            '',                                           // AG
            'MARCA TECLADO',                               // AH
            'SERIAL TECLADO',                              // AI
            'MODELO TECLADO',                             // AJ
            'PLACA TECLADO',                               // AK
            'ESTADO TECLADO',                              // AL
            '',                                           // AM
            '',                                           // AN
            'OFFICE VERSION',                              // AO
            'EQUIPO CUENTA CON TARJETA DE RED INALÁMBRICA', // AP
            'FECHA INVENTARIO',                            // AQ
            'OBSERVACIONES',                               // AR
        ];
    }

    /**
     * @param  Cpu  $cpu
     * @return array
     */
    public function map($cpu): array
    {
        $p = $cpu->persona;
        $mon = $cpu->monitor;
        $tec = $cpu->teclado;

        return [
            $cpu->nombre_ingeniero_diligencio ?? '',
            $cpu->regional ?? '',
            $cpu->dependencias ?? '',
            $cpu->nombre_maquina ?? '',
            $p?->nombre ?? $p?->full_name ?? '',
            $p?->documento_identidad ?? '',
            '', // tipo vinculación
            $p?->title?->nombre ?? '',
            $cpu->tipo_equipo ?? '',
            $cpu->referencia_equipo ?? '',
            $cpu->fecha_adquisicion?->format('Y-m-d') ?? '',
            $cpu->memoria_ram ?? '',
            $cpu->so ?? '',
            $cpu->procesador ?? '',
            $cpu->tipo_so ?? '',
            $cpu->bits ?? '',
            $cpu->n_discos_fisicos ?? '',
            $cpu->capacidad_disco ?? '',
            $cpu->direccion_ip ?? '',
            $cpu->mac_address ?? '',
            $cpu->serial ?? '',
            $cpu->placa ?? '',
            $cpu->estado ?? '',
            $cpu->en_garantia ?? '',
            $cpu->año_adquisicion ?? '',
            $mon?->marca ?? '',
            $mon?->modelo ?? '',
            $mon?->serial ?? '',
            $mon?->placa ?? '',
            $mon?->estado ?? '',
            '',
            '',
            $tec?->marca ?? '',
            $tec?->serial ?? '',
            $tec?->modelo ?? '',
            $tec?->placa ?? '',
            $tec?->estado ?? '',
            '',
            '',
            $cpu->office_version ?? '',
            $cpu->tarjeta_red_inalambrica ?? '',
            $cpu->fecha_inventario?->format('Y-m-d') ?? '',
            $cpu->observaciones ?? '',
        ];
    }
}
