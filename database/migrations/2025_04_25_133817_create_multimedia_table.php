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
        Schema::create('multimedia', function (Blueprint $table) {
            $table->integer('Id_Multimedia', true);
            $table->integer('Id_Usuario')->nullable()->index('id_usuario');
            $table->integer('Id_Trabajo')->nullable()->index('id_trabajo');
            $table->string('Tipo')->nullable();
            $table->string('Nombre')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('multimedia');
    }
};
