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
        Schema::table('postulacion', function (Blueprint $table) {
            $table->foreign(['Id_Estudiante'], 'postulacion_ibfk_1')->references(['Id_Estudiante'])->on('estudiante')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['Id_Trabajo'], 'postulacion_ibfk_2')->references(['Id_Trabajo'])->on('trabajo')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('postulacion', function (Blueprint $table) {
            $table->dropForeign('postulacion_ibfk_1');
            $table->dropForeign('postulacion_ibfk_2');
        });
    }
};
