<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Restaura nombre_maquina en cpus por si fue eliminada en 100000.
     */
    public function up(): void
    {
        if (Schema::hasTable('cpus') && ! Schema::hasColumn('cpus', 'nombre_maquina')) {
            Schema::table('cpus', function (Blueprint $table) {
                $table->string('nombre_maquina')->nullable()->after('activo_crv_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('cpus', 'nombre_maquina')) {
            Schema::table('cpus', function (Blueprint $table) {
                $table->dropColumn('nombre_maquina');
            });
        }
    }
};
