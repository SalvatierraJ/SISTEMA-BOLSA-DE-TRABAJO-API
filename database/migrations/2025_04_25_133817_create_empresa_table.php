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
        Schema::create('empresa', function (Blueprint $table) {
            $table->integer('Id_Empresa', true);
            $table->text('Descripcion')->nullable();
            $table->string('Nombre')->nullable();
            $table->text('Direccion')->nullable();
            $table->string('Direccion_Web')->nullable();
            $table->string('Correo')->nullable();
            $table->string('Redes_Sociales')->nullable();
            $table->integer('Id_Usuario')->nullable()->index('id_usuario');
            $table->integer('Id_Sector')->nullable()->index('id_sector');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empresa');
    }
};
