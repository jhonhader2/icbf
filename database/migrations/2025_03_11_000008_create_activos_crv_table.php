<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activos_crv', function (Blueprint $table) {
            $table->id();
            $table->string('placa')->nullable();
            $table->foreignId('producto_id')->nullable()->constrained('productos')->nullOnDelete();
            $table->date('fecha_adquisicion')->nullable();
            $table->string('serie')->nullable();
            $table->decimal('costo_historico', 15, 2)->nullable();
            $table->decimal('depreciacion', 15, 2)->nullable();
            $table->foreignId('persona_id')->nullable()->constrained('personas')->nullOnDelete();
            $table->foreignId('regional_id')->nullable()->constrained('regionales')->nullOnDelete();
            $table->string('bodega_codigo', 50)->nullable();
            $table->timestamps();
            $table->foreign('bodega_codigo')->references('codigo')->on('bodegas')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activos_crv');
    }
};
