<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Persona;
class PersonaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Persona::create(["Nombre" => "Juan", "Apellido1" => "Pérez", "CI" => "12345678",     "Id_Usuario" => 10]);
        Persona::create(["Nombre" => "María", "Apellido1" => "Gómez", "CI" => "87654321",  "Id_Usuario" => 9 ]);
        Persona::create(["Nombre" => "Pedro", "Apellido1" => "López", "CI" => "23456789",  "Id_Usuario" => 8]);
        Persona::create(["Nombre" => "Ana", "Apellido1" => "Torres", "CI" => "34567890",  "Id_Usuario" => 7]);
        Persona::create(["Nombre" => "Luis", "Apellido1" => "Martínez", "CI" => "45678901",  "Id_Usuario" => 6]);
        Persona::create(["Nombre" => "Carlos", "Apellido1" => "García", "CI" => "56789012",  "Id_Usuario" => 5]);
        Persona::create(["Nombre" => "Tigo", "Apellido1" => "S.A.", "CI" => "67890123",  "Id_Usuario" => 4]);
        Persona::create(["Nombre" => "Entel", "Apellido1" => "S.A.", "CI" => "78901234", "Id_Usuario" => 3]);
        Persona::create(["Nombre" => "Viva", "Apellido1" => "S.A.", "CI" => "89012345",  "Id_Usuario" => 2]); 
    }
}