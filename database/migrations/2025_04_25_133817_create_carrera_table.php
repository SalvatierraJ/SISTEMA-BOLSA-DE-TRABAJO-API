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
        Schema::create('carrera', function (Blueprint $table) {
            $table->integer('Id_Carrera', true);
            $table->string('Nombre')->nullable();
            $table->timestamps();
        });
        DB::table('carrera')->insert([
            ['Nombre' => 'Ciencias Empresariales', 'created_at' => now(), 'updated_at' => now()],
            ['Nombre' => 'Administración General', 'created_at' => now(), 'updated_at' => now()],
            ['Nombre' => 'Administración de Turismo', 'created_at' => now(), 'updated_at' => now()],
            ['Nombre' => 'Ingeniería Comercial', 'created_at' => now(), 'updated_at' => now()],
            ['Nombre' => 'Comercio Internacional', 'created_at' => now(), 'updated_at' => now()],
            ['Nombre' => 'Ingeniería en Marketing y Publicidad', 'created_at' => now(), 'updated_at' => now()],
            ['Nombre' => 'Contaduría Pública', 'created_at' => now(), 'updated_at' => now()],
            ['Nombre' => 'Ingeniería Financiera', 'created_at' => now(), 'updated_at' => now()],
            ['Nombre' => 'Comunicación Estratégica y Digital', 'created_at' => now(), 'updated_at' => now()],
            ['Nombre' => 'Ciencias y Tecnología', 'created_at' => now(), 'updated_at' => now()],
            ['Nombre' => 'Ingeniería Industrial y Comercial', 'created_at' => now(), 'updated_at' => now()],
            ['Nombre' => 'Ingeniería en Administración Petrolera', 'created_at' => now(), 'updated_at' => now()],
            ['Nombre' => 'Ingeniería Electrónica y Sistemas', 'created_at' => now(), 'updated_at' => now()],
            ['Nombre' => 'Ingeniería Mecánica Automotriz y Agroindustrial', 'created_at' => now(), 'updated_at' => now()],
            ['Nombre' => 'Ingeniería de Sistemas', 'created_at' => now(), 'updated_at' => now()],
            ['Nombre' => 'Ingeniería en Redes y Telecomunicaciones', 'created_at' => now(), 'updated_at' => now()],
            ['Nombre' => 'Ingeniería Eléctrica', 'created_at' => now(), 'updated_at' => now()],
            ['Nombre' => 'Ciencias Jurídicas, Sociales y Humanísticas', 'created_at' => now(), 'updated_at' => now()],
            ['Nombre' => 'Derecho', 'created_at' => now(), 'updated_at' => now()],
            ['Nombre' => 'Relaciones Internacionales', 'created_at' => now(), 'updated_at' => now()],
            ['Nombre' => 'Psicología', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carrera');
    }
};
