<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jefe_pasante', function (Blueprint $table) {
            $table->integer('id_empresa')->nullable();

            $table->foreign('id_empresa')
                ->references('id_empresa')
                ->on('empresa');
        });
    }

    public function down(): void
    {
        Schema::table('jefe_pasante', function (Blueprint $table) {
            $table->dropForeign(['id_empresa']);
            $table->dropColumn('id_empresa');
        });
    }
};
