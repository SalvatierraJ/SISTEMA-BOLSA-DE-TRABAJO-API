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
        Schema::create('fase', function (Blueprint $table) {
            $table->integer('Id_Fase', true);
            $table->string('Titulo')->nullable();
            $table->integer('Archivo')->nullable();
            $table->integer('Resultado')->nullable();
            $table->string('Etapa')->nullable();
            $table->integer('Id_Postulacion')->nullable()->index('id_postulacion');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fase');
    }
};
