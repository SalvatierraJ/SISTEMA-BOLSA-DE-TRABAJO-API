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
        Schema::create('trabajo', function (Blueprint $table) {
            $table->integer('Id_Trabajo', true);
            $table->string('Titulo')->nullable();
            $table->text('Descripcion')->nullable();
            $table->json('Requisitos')->nullable();
            $table->text('Competencia')->nullable();
            $table->string('Ubicacion')->nullable();
            $table->decimal('Salario', 10, 0)->nullable();
            $table->enum('Modalidad', ['Medio Tiempo', 'Tiempo Completo', 'Remoto', 'Hibrido'])->nullable();
            $table->date('Fecha_Inicio')->nullable();
            $table->date('Fecha_Fin')->nullable();
            $table->string('Duracion')->nullable();
            $table->enum('Estado', ['Activo', 'Inactivo'])->nullable()->default('Activo');
            $table->binary('Tipo_Trabajo')->nullable();
            $table->integer('Id_Empresa')->nullable()->index('id_empresa');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trabajo');
    }
};
