<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tvm_assets', function (Blueprint $table) {
            $table->string('state', 20)->default('open')->after('operating_system');
            $table->dateTime('resolved_at')->nullable()->after('state');
        });
    }

    public function down(): void
    {
        Schema::table('tvm_assets', function (Blueprint $table) {
            $table->dropColumn(['state', 'resolved_at']);
        });
    }
};
