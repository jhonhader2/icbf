<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Teclado extends Model
{
    protected $table = 'teclados';

    protected $fillable = ['marca', 'modelo', 'serial', 'placa', 'estado', 'cpu_id'];

    public function cpu(): BelongsTo
    {
        return $this->belongsTo(Cpu::class, 'cpu_id');
    }
}
