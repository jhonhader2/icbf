<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bodega extends Model
{
    protected $table = 'bodegas';

    protected $primaryKey = 'codigo';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = ['codigo', 'nombre'];

    public function getRouteKeyName(): string
    {
        return 'codigo';
    }

    public function activosCrv(): HasMany
    {
        return $this->hasMany(ActivoCrv::class, 'bodega_codigo', 'codigo');
    }
}
