<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('tvm_assets')->update([
            'name' => DB::raw('UPPER(name)'),
            'dns_name' => DB::raw('CASE WHEN dns_name IS NOT NULL AND dns_name != "" THEN UPPER(dns_name) ELSE dns_name END'),
            'operating_system' => DB::raw('CASE WHEN operating_system IS NOT NULL AND operating_system != "" THEN UPPER(operating_system) ELSE operating_system END'),
            'state' => DB::raw('UPPER(COALESCE(NULLIF(TRIM(state), ""), "OPEN"))'),
        ]);
    }

    public function down(): void
    {
        // No se revierte; los valores quedarían en mayúsculas.
    }
};
