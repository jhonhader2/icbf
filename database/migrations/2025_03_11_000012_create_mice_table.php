<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mice', function (Blueprint $table) {
            $table->id();
            $table->string('marca')->nullable();
            $table->string('modelo')->nullable();
            $table->string('serial')->nullable();
            $table->string('estado')->nullable();
            $table->foreignId('cpu_id')->nullable()->constrained('cpus')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mice');
    }
};
