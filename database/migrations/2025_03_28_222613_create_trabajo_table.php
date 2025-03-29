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
            $table->string('Titulo');
            $table->text('Descripcion')->nullable();
            $table->text('Requisitos')->nullable();
            $table->text('Competencias')->nullable();
            $table->string('Ubicacion')->nullable();
            $table->decimal('Salario', 10)->nullable();
            $table->string('Categoria', 100)->nullable();
            $table->string('Modalidad', 50);
            $table->date('Fecha_Inicio')->nullable();
            $table->date('Fecha_Final')->nullable();
            $table->integer('Duracion')->nullable();
            $table->string('Nombre_Imagen')->nullable();
            $table->string('Tipo', 50);
            $table->integer('Id_Empresa')->nullable()->index('id_empresa');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
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
