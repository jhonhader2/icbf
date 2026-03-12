<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('monitores', function (Blueprint $table) {
            $table->foreignId('activo_crv_id')->nullable()->after('id')->constrained('activos_crv')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('monitores', function (Blueprint $table) {
            $table->dropForeign(['activo_crv_id']);
        });
    }
};
