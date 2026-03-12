<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Mouse extends Model
{
    protected $table = 'mice';

    protected $fillable = ['marca', 'modelo', 'serial', 'estado', 'cpu_id'];

    public function cpu(): BelongsTo
    {
        return $this->belongsTo(Cpu::class, 'cpu_id');
    }
}
