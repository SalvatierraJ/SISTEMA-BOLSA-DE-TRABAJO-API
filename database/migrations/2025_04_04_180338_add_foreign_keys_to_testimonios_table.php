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
        Schema::table('testimonios', function (Blueprint $table) {
            $table->foreign(['Id_Estudiante'], 'testimonios_ibfk_1')->references(['Id_Estudiante'])->on('estudiante')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('testimonios', function (Blueprint $table) {
            $table->dropForeign('testimonios_ibfk_1');
        });
    }
};
