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
        Schema::table('carrera_estudiante', function (Blueprint $table) {
            $table->foreign(['Id_Carrera'], 'carrera_estudiante_ibfk_1')->references(['Id_Carrera'])->on('carrera')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['Id_Estudiante'], 'carrera_estudiante_ibfk_2')->references(['Id_Estudiante'])->on('estudiante')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('carrera_estudiante', function (Blueprint $table) {
            $table->dropForeign('carrera_estudiante_ibfk_1');
            $table->dropForeign('carrera_estudiante_ibfk_2');
        });
    }
};
