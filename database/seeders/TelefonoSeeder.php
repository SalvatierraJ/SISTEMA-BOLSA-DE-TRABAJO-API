<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Telefono;
class TelefonoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Telefono::create(["Numero" => "123456789", 'Id_Persona' =>1, 'Id_Empresa' => 1]);
        Telefono::create(["Numero" => "555555555" , 'Id_Persona' =>2, 'Id_Empresa' => 2]);
        Telefono::create(["Numero" => "987654321",  'Id_Persona' =>3, 'Id_Empresa' => 3]);
        Telefono::create(["Numero" => "111222333", 'Id_Persona' =>4, 'Id_Empresa' => 4]);
         
    }
}