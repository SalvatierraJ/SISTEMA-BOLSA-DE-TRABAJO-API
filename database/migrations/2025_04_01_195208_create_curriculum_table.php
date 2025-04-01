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
            $table->text('Descripcion')->nullable();
            $table->json('Habilidades')->nullable();
            $table->json('Certificados')->nullable();
            $table->json('Experiencia')->nullable();
            $table->json('Idiomas')->nullable();
            $table->integer('Id_Estudiante')->nullable()->unique('id_estudiante');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
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
