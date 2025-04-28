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
        Schema::create('persona', function (Blueprint $table) {
            $table->integer('Id_Persona', true);
            $table->string('Nombre');
            $table->string('Apellido1');
            $table->string('Apellido2')->nullable();
            $table->integer('CI')->nullable();
            $table->boolean('Genero')->nullable();
            $table->string('Correo')->nullable();
            $table->integer('Id_Usuario')->nullable()->index('id_usuario');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('persona');
    }
};
