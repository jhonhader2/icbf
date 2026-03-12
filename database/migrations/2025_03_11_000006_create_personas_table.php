<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('personas', function (Blueprint $table) {
            $table->id();
            $table->string('documento_identidad')->unique();
            $table->string('nombre')->nullable();
            $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->foreignId('regional_id')->nullable()->constrained('regionales')->nullOnDelete();
            $table->string('account_status', 1)->nullable();
            $table->string('employee_id')->nullable();
            $table->string('full_name')->nullable();
            $table->string('email_address')->nullable();
            $table->foreignId('office_id')->nullable()->constrained('offices')->nullOnDelete();
            $table->foreignId('title_id')->nullable()->constrained('titles')->nullOnDelete();
            $table->text('description')->nullable();
            $table->string('sam_account_name')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('personas');
    }
};
