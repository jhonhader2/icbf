<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Cpu extends Model
{
    protected $table = 'cpus';

    /**
     * Solo campos que no están en activos_crv ni en productos.
     * serial, fecha_adquisicion, tipo_equipo, referencia_equipo, regional, año_adquisicion
     * se obtienen vía activoCrv/producto (accessors). nombre_maquina se guarda en cpus.
     */
    protected $fillable = [
        'activo_crv_id',
        'nombre_maquina',
        'placa',
        'estado',
        'tipo_equipo',
        'referencia_equipo',
        'memoria_ram',
        'so',
        'procesador',
        'tipo_so',
        'bits',
        'n_discos_fisicos',
        'capacidad_disco',
        'direccion_ip',
        'mac_address',
        'office_version',
        'tarjeta_red_inalambrica',
        'en_garantia',
        'fecha_inventario',
        'observaciones',
        'dependencias',
        'nombre_ingeniero_diligencio',
        'persona_id',
    ];

    protected function casts(): array
    {
        return [
            'fecha_inventario' => 'date',
        ];
    }

    public function persona(): BelongsTo
    {
        return $this->belongsTo(Persona::class);
    }

    public function activoCrv(): BelongsTo
    {
        return $this->belongsTo(ActivoCrv::class, 'activo_crv_id');
    }

    public function monitor(): HasOne
    {
        return $this->hasOne(Monitor::class, 'cpu_id');
    }

    public function teclado(): HasOne
    {
        return $this->hasOne(Teclado::class, 'cpu_id');
    }

    public function mouse(): HasOne
    {
        return $this->hasOne(Mouse::class, 'cpu_id');
    }

    // ——— Accessors: datos que viven en activos_crv o producto ———

    protected function serial(): Attribute
    {
        return Attribute::get(fn () => $this->activoCrv?->serie);
    }

    protected function fechaAdquisicion(): Attribute
    {
        return Attribute::get(fn () => $this->activoCrv?->fecha_adquisicion);
    }

    /**
     * Antigüedad desde fecha_adquisicion hasta hoy (ej: "5 años, 4 meses, 2 días").
     */
    public function getAntiguedadTextoAttribute(): ?string
    {
        $fecha = $this->fecha_adquisicion;
        if (! $fecha instanceof \DateTimeInterface) {
            return null;
        }
        $now = Carbon::now();
        $diff = $fecha->diff($now);
        $partes = [];
        if ($diff->y > 0) {
            $partes[] = $diff->y . ' ' . ($diff->y === 1 ? __('año') : __('años'));
        }
        if ($diff->m > 0) {
            $partes[] = $diff->m . ' ' . ($diff->m === 1 ? __('mes') : __('meses'));
        }
        if ($diff->d > 0) {
            $partes[] = $diff->d . ' ' . ($diff->d === 1 ? __('día') : __('días'));
        }

        return $partes !== [] ? implode(', ', $partes) : __('menos de un día');
    }

    protected function tipoEquipo(): Attribute
    {
        return Attribute::get(fn () => $this->attributes['tipo_equipo'] ?? $this->activoCrv?->producto?->nombre)
            ->set(fn (?string $v) => $v);
    }

    protected function referenciaEquipo(): Attribute
    {
        return Attribute::get(function () {
            $col = $this->attributes['referencia_equipo'] ?? null;
            if ($col !== null && $col !== '') {
                return $col;
            }
            $p = $this->activoCrv?->producto;
            if (! $p) {
                return null;
            }
            $parts = array_filter([$p->marca, $p->modelo]);

            return $parts ? implode(' ', $parts) : null;
        })->set(fn (?string $v) => $v);
    }

    protected function regional(): Attribute
    {
        return Attribute::get(fn () => $this->activoCrv?->regional?->nombre);
    }

    protected function añoAdquisicion(): Attribute
    {
        return Attribute::get(fn () => $this->activoCrv?->fecha_adquisicion?->format('Y'));
    }
}
