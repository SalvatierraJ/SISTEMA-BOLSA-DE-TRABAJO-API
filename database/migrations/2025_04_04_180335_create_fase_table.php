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
            $table->string('Titulo', 100);
            $table->string('Archivo')->nullable();
            $table->string('Etapa', 50);
            $table->integer('Id_Postulacion')->index('id_postulacion');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
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
