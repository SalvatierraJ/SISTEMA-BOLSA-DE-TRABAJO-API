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
            $table->timestamp('Fecha_Postulacion')->nullable()->useCurrent();
            $table->string('Estado', 50)->nullable()->default('Pendiente');
            $table->integer('Id_Estudiante')->nullable()->index('id_estudiante');
            $table->integer('Id_Trabajo')->nullable()->index('id_trabajo');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
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
