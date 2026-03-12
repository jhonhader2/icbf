<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Añade tipo_equipo a cpus para importación desde Formato Toma Parque (columna J).
     */
    public function up(): void
    {
        if (Schema::hasTable('cpus') && ! Schema::hasColumn('cpus', 'tipo_equipo')) {
            Schema::table('cpus', function (Blueprint $table) {
                $table->string('tipo_equipo')->nullable()->after('estado');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('cpus', 'tipo_equipo')) {
            Schema::table('cpus', function (Blueprint $table) {
                $table->dropColumn('tipo_equipo');
            });
        }
    }
};
