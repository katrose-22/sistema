<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pasantia', function (Blueprint $table) {
            $table->integer('id_jefe')->nullable();
            $table->string('documento_path')->nullable();
            $table->string('documento_nombre')->nullable();

            $table->foreign('id_jefe')
                ->references('id_usuario')
                ->on('jefe_pasante')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pasantia', function (Blueprint $table) {
            $table->dropForeign(['id_jefe']);
            $table->dropColumn(['id_jefe', 'documento_path', 'documento_nombre']);
        });
    }
};
