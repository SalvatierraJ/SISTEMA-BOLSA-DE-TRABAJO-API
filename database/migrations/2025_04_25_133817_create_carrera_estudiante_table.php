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
        Schema::create('carrera_estudiante', function (Blueprint $table) {
            $table->integer('Id_Carrera_Estudiante', true);
            $table->integer('Id_Carrera')->nullable()->index('id_carrera');
            $table->integer('Id_Estudiante')->nullable()->index('id_estudiante');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carrera_estudiante');
    }
};
