<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ActivoCrv extends Model
{
    protected $table = 'activos_crv';

    protected $fillable = [
        'placa',
        'producto_id',
        'fecha_adquisicion',
        'serie',
        'costo_historico',
        'depreciacion',
        'persona_id',
        'regional_id',
        'bodega_codigo',
    ];

    protected function casts(): array
    {
        return [
            'fecha_adquisicion' => 'date',
            'costo_historico' => 'decimal:2',
            'depreciacion' => 'decimal:2',
        ];
    }

    public function persona(): BelongsTo
    {
        return $this->belongsTo(Persona::class);
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    public function regional(): BelongsTo
    {
        return $this->belongsTo(Regional::class, 'regional_id');
    }

    public function bodega(): BelongsTo
    {
        return $this->belongsTo(Bodega::class, 'bodega_codigo', 'codigo');
    }

    public function cpu(): HasOne
    {
        return $this->hasOne(Cpu::class, 'activo_crv_id');
    }
}
