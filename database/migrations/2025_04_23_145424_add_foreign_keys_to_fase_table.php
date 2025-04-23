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
        Schema::table('fase', function (Blueprint $table) {
            $table->foreign(['Id_Postulacion'], 'fase_ibfk_1')->references(['Id_Postulacion'])->on('postulacion')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fase', function (Blueprint $table) {
            $table->dropForeign('fase_ibfk_1');
        });
    }
};
