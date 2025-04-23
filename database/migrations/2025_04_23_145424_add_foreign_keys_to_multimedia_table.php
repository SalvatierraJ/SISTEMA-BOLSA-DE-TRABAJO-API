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
        Schema::table('multimedia', function (Blueprint $table) {
            $table->foreign(['Id_Usuario'], 'multimedia_ibfk_1')->references(['Id_Usuario'])->on('usuario')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['Id_Trabajo'], 'multimedia_ibfk_2')->references(['Id_Trabajo'])->on('trabajo')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('multimedia', function (Blueprint $table) {
            $table->dropForeign('multimedia_ibfk_1');
            $table->dropForeign('multimedia_ibfk_2');
        });
    }
};
