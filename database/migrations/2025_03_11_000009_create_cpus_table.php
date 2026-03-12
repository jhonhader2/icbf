<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cpus', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_maquina')->nullable();
            $table->string('serial')->nullable();
            $table->string('placa')->nullable();
            $table->string('estado')->nullable();
            $table->string('tipo_equipo')->nullable();
            $table->string('referencia_equipo')->nullable();
            $table->string('memoria_ram')->nullable();
            $table->string('so')->nullable();
            $table->string('procesador')->nullable();
            $table->string('tipo_so')->nullable();
            $table->string('bits')->nullable();
            $table->string('n_discos_fisicos')->nullable();
            $table->string('capacidad_disco')->nullable();
            $table->string('direccion_ip')->nullable();
            $table->string('mac_address')->nullable();
            $table->string('office_version')->nullable();
            $table->string('tarjeta_red_inalambrica')->nullable();
            $table->date('fecha_adquisicion')->nullable();
            $table->string('año_adquisicion')->nullable();
            $table->string('en_garantia')->nullable();
            $table->date('fecha_inventario')->nullable();
            $table->text('observaciones')->nullable();
            $table->string('regional')->nullable();
            $table->string('dependencias')->nullable();
            $table->string('nombre_ingeniero_diligencio')->nullable();
            $table->foreignId('persona_id')->nullable()->constrained('personas')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cpus');
    }
};
