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
        Schema::create('postulacion', function (Blueprint $table) {
            $table->integer('Id_Postulacion', true);
            $table->date('Fecha_Postulacion')->nullable();
            $table->enum('Estado', ['Aceptado', 'Pendiente', 'Descartado'])->nullable()->default('Pendiente');
            $table->integer('Id_Estudiante')->nullable()->index('id_estudiante');
            $table->integer('Id_Trabajo')->nullable()->index('id_trabajo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('postulacion');
    }
};
