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
        Schema::create('empresas', function (Blueprint $table) {
            $table->integer('Id_Empresa', true);
            $table->string('Nombre');
            $table->string('Sector', 100)->nullable();
            $table->string('Correo', 100)->unique('correo');
            $table->string('Direccion')->nullable();
            $table->string('Contacto', 50)->nullable();
            $table->string('Direccion_Web')->nullable();
            $table->string('logotipo',255)->nullable();
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
        Schema::dropIfExists('empresas');
    }
};
