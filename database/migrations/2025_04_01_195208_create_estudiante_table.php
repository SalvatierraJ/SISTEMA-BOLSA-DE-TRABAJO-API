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
        Schema::create('estudiante', function (Blueprint $table) {
            $table->integer('Id_Estudiante', true);
            $table->string('Nro_Registro', 50)->unique('nro_registro');
            $table->string('Carnet', 50)->unique('carnet');
            $table->string('Nombre', 100);
            $table->string('Apellido', 100);
            $table->string('Correo', 100)->unique('correo');
            $table->string('Carrera', 100)->nullable();
            $table->integer('Id_Usuario')->nullable()->unique('id_usuario');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estudiante');
    }
};
