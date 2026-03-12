<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Persona extends Model
{
    protected $table = 'personas';

    protected $fillable = [
        'documento_identidad',
        'nombre',
        'department_id',
        'regional_id',
        'account_status',
        'employee_id',
        'full_name',
        'email_address',
        'office_id',
        'title_id',
        'description',
        'sam_account_name',
    ];

    protected function documentoIdentidad(): Attribute
    {
        return Attribute::set(fn (?string $v) => $v !== null && $v !== '' ? mb_strtoupper($v, 'UTF-8') : $v);
    }

    protected function nombre(): Attribute
    {
        return Attribute::set(fn (?string $v) => $v !== null && $v !== '' ? mb_strtoupper($v, 'UTF-8') : $v);
    }

    protected function accountStatus(): Attribute
    {
        return Attribute::set(fn (?string $v) => $v !== null && $v !== '' ? mb_strtoupper($v, 'UTF-8') : $v);
    }

    protected function fullName(): Attribute
    {
        return Attribute::set(fn (?string $v) => $v !== null && $v !== '' ? mb_strtoupper($v, 'UTF-8') : $v);
    }

    protected function emailAddress(): Attribute
    {
        return Attribute::set(fn (?string $v) => $v !== null && $v !== '' ? mb_strtoupper($v, 'UTF-8') : $v);
    }

    protected function description(): Attribute
    {
        return Attribute::set(fn (?string $v) => $v !== null && $v !== '' ? mb_strtoupper($v, 'UTF-8') : $v);
    }

    protected function samAccountName(): Attribute
    {
        return Attribute::set(fn (?string $v) => $v !== null && $v !== '' ? mb_strtoupper($v, 'UTF-8') : $v);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function regional(): BelongsTo
    {
        return $this->belongsTo(Regional::class, 'regional_id');
    }

    public function office(): BelongsTo
    {
        return $this->belongsTo(Office::class);
    }

    public function title(): BelongsTo
    {
        return $this->belongsTo(Title::class);
    }

    public function cpus(): HasMany
    {
        return $this->hasMany(Cpu::class, 'persona_id');
    }

    public function activosCrv(): HasMany
    {
        return $this->hasMany(ActivoCrv::class, 'persona_id');
    }
}
