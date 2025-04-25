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
        Schema::table('estudiante', function (Blueprint $table) {
            $table->foreign(['Id_Persona'], 'estudiante_ibfk_1')->references(['Id_Persona'])->on('persona')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('estudiante', function (Blueprint $table) {
            $table->dropForeign('estudiante_ibfk_1');
        });
    }
};
