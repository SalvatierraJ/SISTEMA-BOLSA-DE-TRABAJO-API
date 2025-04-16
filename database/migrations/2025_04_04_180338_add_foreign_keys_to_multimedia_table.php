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
        Schema::table('multimedia', function (Blueprint $table) {
            $table->foreign(['id_estudiante'], 'multimedia_ibfk_1')->references(['Id_Estudiante'])->on('estudiante')->onUpdate('cascade')->onDelete('set null');
            $table->foreign(['id_empresa'], 'multimedia_ibfk_2')->references(['Id_Empresa'])->on('empresas')->onUpdate('cascade')->onDelete('set null');
            $table->foreign(['id_trabajo'], 'multimedia_ibfk_3')->references(['Id_Trabajo'])->on('trabajo')->onUpdate('cascade')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('multimedia', function (Blueprint $table) {
            $table->dropForeign('multimedia_ibfk_1');
            $table->dropForeign('multimedia_ibfk_2');
            $table->dropForeign('multimedia_ibfk_3');
        });
    }
};
