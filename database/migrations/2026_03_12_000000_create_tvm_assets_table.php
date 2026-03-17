<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tvm_assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cpu_id')
                ->nullable()
                ->constrained('cpus')
                ->nullOnDelete();
            $table->string('name')->unique();
            $table->string('dns_name')->nullable();
            $table->dateTime('last_seen')->nullable();
            $table->string('operating_system')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tvm_assets');
    }
};

