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
            $table->integer('id_multimedia', true);
            $table->integer('id_estudiante')->nullable()->index('id_estudiante');
            $table->integer('id_empresa')->nullable()->index('id_empresa');
            $table->integer('id_trabajo')->nullable()->index('id_trabajo');
            $table->string('tipo', 50)->nullable();
            $table->text('direccion')->nullable();
            $table->timestamp('created_at');
            $table->timestamp('updated_at');
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
