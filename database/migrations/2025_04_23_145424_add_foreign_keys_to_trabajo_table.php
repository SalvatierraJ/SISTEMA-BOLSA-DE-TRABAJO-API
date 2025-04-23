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
        Schema::table('trabajo', function (Blueprint $table) {
            $table->foreign(['Id_Empresa'], 'trabajo_ibfk_1')->references(['Id_Empresa'])->on('empresa')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trabajo', function (Blueprint $table) {
            $table->dropForeign('trabajo_ibfk_1');
        });
    }
};
