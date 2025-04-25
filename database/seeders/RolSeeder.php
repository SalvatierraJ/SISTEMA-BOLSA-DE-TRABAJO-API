<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
Use App\Models\Rol;
class RolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Rol::create(["Nombre" => "Estudiante"]);
        Rol::create(["Nombre" => "Empresa"]);
        Rol::create(["Nombre" => "Administrador"]);
        
    }
}