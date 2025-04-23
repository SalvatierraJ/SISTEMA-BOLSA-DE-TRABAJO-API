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
        Schema::create('usuario', function (Blueprint $table) {
            $table->integer('Id_Usuario', true);
            $table->string('Usuario');
            $table->string('Clave');
            $table->enum('Estado', ['Activo', 'Inactivo'])->nullable()->default('Activo');
            $table->integer('Id_Rol')->nullable()->index('id_rol');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuario');
    }
};
