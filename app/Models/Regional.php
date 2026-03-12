<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Regional extends Model
{
    protected $table = 'regionales';

    protected $fillable = ['nombre'];

    protected function nombre(): Attribute
    {
        return Attribute::get(fn (?string $v) => $v !== null && $v !== '' ? mb_strtoupper($v, 'UTF-8') : $v)
            ->set(fn (?string $v) => $v !== null && $v !== '' ? mb_strtoupper($v, 'UTF-8') : $v);
    }

    public function activosCrv(): HasMany
    {
        return $this->hasMany(ActivoCrv::class, 'regional_id');
    }

    public function personas(): HasMany
    {
        return $this->hasMany(Persona::class, 'regional_id');
    }
}
