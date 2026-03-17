<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TvmAsset extends Model
{
    public const STATE_OPEN = 'OPEN';
    public const STATE_RESOLVED = 'RESOLVED';

    protected $table = 'tvm_assets';

    protected $fillable = [
        'cpu_id',
        'name',
        'dns_name',
        'last_seen',
        'operating_system',
        'state',
        'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'last_seen' => 'datetime',
            'resolved_at' => 'datetime',
        ];
    }

    /** Guarda los strings en mayúsculas. */
    protected static function booted(): void
    {
        static::saving(function (TvmAsset $model) {
            foreach (['name', 'dns_name', 'operating_system', 'state'] as $attr) {
                $v = $model->getAttribute($attr);
                if (is_string($v) && $v !== '') {
                    $model->setAttribute($attr, mb_strtoupper($v));
                }
            }
        });
    }

    public function scopeOpen(Builder $query): Builder
    {
        return $query->where('state', self::STATE_OPEN);
    }

    public function scopeResolved(Builder $query): Builder
    {
        return $query->where('state', self::STATE_RESOLVED);
    }

    public function isOpen(): bool
    {
        return $this->state === self::STATE_OPEN;
    }

    public function isResolved(): bool
    {
        return $this->state === self::STATE_RESOLVED;
    }

    /** Marca esta ocurrencia como resuelta. */
    public function markResolved(): void
    {
        $this->update([
            'state' => self::STATE_RESOLVED,
            'resolved_at' => now(),
        ]);
    }

    public function cpu(): BelongsTo
    {
        return $this->belongsTo(Cpu::class);
    }

    /**
     * Texto amigable para mostrar "Last seen" (ej: "2026-03-12 19:54" o relativo).
     */
    protected function lastSeenTexto(): Attribute
    {
        return Attribute::get(function () {
            /** @var Carbon|null $dt */
            $dt = $this->last_seen;
            if (! $dt instanceof Carbon) {
                return null;
            }

            return $dt->format('Y-m-d H:i');
        });
    }
}

