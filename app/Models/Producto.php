<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Producto extends Model
{
    protected $table = 'productos';

    protected $fillable = [
        'codigo',
        'nombre',
        'marca',
        'modelo',
    ];

    protected function nombre(): Attribute
    {
        return Attribute::set(fn (?string $v) => $v !== null && $v !== '' ? mb_strtoupper($v, 'UTF-8') : $v);
    }

    protected function marca(): Attribute
    {
        return Attribute::set(fn (?string $v) => $v !== null && $v !== '' ? mb_strtoupper($v, 'UTF-8') : $v);
    }

    protected function modelo(): Attribute
    {
        return Attribute::set(fn (?string $v) => $v !== null && $v !== '' ? mb_strtoupper($v, 'UTF-8') : $v);
    }

    public function activosCrv(): HasMany
    {
        return $this->hasMany(ActivoCrv::class, 'producto_id');
    }
}
