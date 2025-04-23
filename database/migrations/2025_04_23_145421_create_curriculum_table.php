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
        Schema::create('curriculum', function (Blueprint $table) {
            $table->integer('Id_Curriculum', true);
            $table->json('Descripcion')->nullable();
            $table->json('Habilidades')->nullable();
            $table->json('Certificados')->nullable();
            $table->json('Experiencia')->nullable();
            $table->json('Idiomas')->nullable();
            $table->json('Configuracion_CV')->nullable();
            $table->integer('Id_Estudiante')->nullable()->index('id_estudiante');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('curriculum');
    }
};
