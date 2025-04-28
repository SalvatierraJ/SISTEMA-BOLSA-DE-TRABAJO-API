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
        Schema::table('empresa', function (Blueprint $table) {
            $table->foreign(['Id_Usuario'], 'empresa_ibfk_2')->references(['Id_Usuario'])->on('usuario')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['Id_Sector'], 'empresa_ibfk_3')->references(['Id_Sector'])->on('sector')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('empresa', function (Blueprint $table) {
            $table->dropForeign('empresa_ibfk_2');
            $table->dropForeign('empresa_ibfk_3');
        });
    }
};
