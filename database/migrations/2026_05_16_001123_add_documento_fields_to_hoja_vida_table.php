<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('hoja_vida', function (Blueprint $table) {
            $table->string('documento_path')->nullable()->after('id_pasante');
            $table->string('documento_nombre')->nullable()->after('documento_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hoja_vida', function (Blueprint $table) {
            $table->dropColumn(['documento_path', 'documento_nombre']);
        });
    }
};
