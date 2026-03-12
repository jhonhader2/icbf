<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Vincula CPU al activo CRV (serie, fecha_adquisicion, producto) y elimina columnas
     * que ya están en activos_crv o en productos.
     */
    public function up(): void
    {
        Schema::table('cpus', function (Blueprint $table) {
            $table->foreignId('activo_crv_id')->nullable()->after('id')->constrained('activos_crv')->nullOnDelete();
        });

        Schema::table('cpus', function (Blueprint $table) {
            $table->dropColumn([
                'serial',              // activos_crv.serie
                'fecha_adquisicion',    // activos_crv.fecha_adquisicion
                'tipo_equipo',          // producto.nombre
                'referencia_equipo',    // producto.marca/modelo
                'regional',             // activos_crv.regional_id
                'año_adquisicion',      // derivable de fecha_adquisicion
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('cpus', function (Blueprint $table) {
            $table->string('serial')->nullable()->after('placa');
            $table->date('fecha_adquisicion')->nullable()->after('tarjeta_red_inalambrica');
            $table->string('tipo_equipo')->nullable()->after('estado');
            $table->string('referencia_equipo')->nullable()->after('tipo_equipo');
            $table->string('regional')->nullable()->after('observaciones');
            $table->string('año_adquisicion')->nullable()->after('fecha_adquisicion');
        });

        Schema::table('cpus', function (Blueprint $table) {
            $table->dropForeign(['activo_crv_id']);
        });
    }
};
