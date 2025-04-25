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
        Schema::table('telefono', function (Blueprint $table) {
            $table->foreign(['Id_Persona'], 'telefono_ibfk_1')->references(['Id_Persona'])->on('persona')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign(['Id_Empresa'], 'telefono_ibfk_2')->references(['Id_Empresa'])->on('empresa')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('telefono', function (Blueprint $table) {
            $table->dropForeign('telefono_ibfk_1');
            $table->dropForeign('telefono_ibfk_2');
        });
    }
};
