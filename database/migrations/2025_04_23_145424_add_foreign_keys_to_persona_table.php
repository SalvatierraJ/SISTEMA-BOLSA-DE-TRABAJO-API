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
        Schema::table('persona', function (Blueprint $table) {
            $table->foreign(['Id_Telefono'], 'persona_ibfk_1')->references(['Id_Telefono'])->on('telefono')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['Id_Usuario'], 'persona_ibfk_2')->references(['Id_Usuario'])->on('usuario')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('persona', function (Blueprint $table) {
            $table->dropForeign('persona_ibfk_1');
            $table->dropForeign('persona_ibfk_2');
        });
    }
};
