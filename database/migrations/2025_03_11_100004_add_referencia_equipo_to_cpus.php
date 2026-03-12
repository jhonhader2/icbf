<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Añade referencia_equipo a cpus para ver y editar (y opcionalmente importar desde Toma Parque).
     */
    public function up(): void
    {
        if (Schema::hasTable('cpus') && ! Schema::hasColumn('cpus', 'referencia_equipo')) {
            Schema::table('cpus', function (Blueprint $table) {
                $table->string('referencia_equipo')->nullable()->after('tipo_equipo');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('cpus', 'referencia_equipo')) {
            Schema::table('cpus', function (Blueprint $table) {
                $table->dropColumn('referencia_equipo');
            });
        }
    }
};
