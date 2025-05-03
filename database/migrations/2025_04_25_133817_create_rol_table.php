<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('rol', function (Blueprint $table) {
            $table->integer('Id_Rol', true);
            $table->string('Nombre')->nullable();
            $table->timestamps();
        });


          DB::table('rol')->insert([
            ['Nombre' => 'Administrador', 'created_at' => now(), 'updated_at' => now()],
            ['Nombre' => 'Empresa', 'created_at' => now(), 'updated_at' => now()],
            ['Nombre' => 'Estudiante', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rol');
    }
};
