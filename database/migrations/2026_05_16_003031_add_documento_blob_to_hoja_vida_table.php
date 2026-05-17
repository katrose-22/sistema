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
            $table->binary('documento_blob')->nullable();
            $table->string('documento_mime')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hoja_vida', function (Blueprint $table) {
            $table->dropColumn(['documento_blob', 'documento_mime']);
        });
    }
};
