<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Usuario as User;
use Illuminate\Support\Facades\Hash;
class Usuario extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create(['Usuario' => "Pedro_Aguirre", 'Clave' => Hash::make("123456"), 'Id_Rol' => 1, 'Estado' => 1]);
        User::create(['Usuario' => "Juan_Perez", 'Clave' => Hash::make("123456"), 'Id_Rol' => 1, 'Estado' => 1]);
        User::create(['Usuario' => "MariaLopez", 'Clave' => Hash::make("123456"), 'Id_Rol' => 1, 'Estado' => 1]);
        User::create(['Usuario' => "Ana_Torres", 'Clave' => Hash::make("123456"), 'Id_Rol' => 1, 'Estado' => 1]);
        User::create(['Usuario' => "LuisGarcia", 'Clave' => Hash::make("123456"), 'Id_Rol' => 1, 'Estado' => 1]);
        User::create(['Usuario' => "CarlosMartinez", 'Clave' => Hash::make("123456"), 'Id_Rol' => 1, 'Estado' => 1]);
    User::create(['Usuario' => "Tigo", 'Clave' => Hash::make("123456"), 'Id_Rol' => 2, 'Estado' => 1]);
        User::create(['Usuario' => "Entel", 'Clave' => Hash::make("123456"), 'Id_Rol' => 2, 'Estado' => 1]);
        User::create(['Usuario' => "Viva", 'Clave' => Hash::make("123456"), 'Id_Rol' => 2, 'Estado' => 1]);
        User::create(['Usuario' => "Cocacola", 'Clave' => Hash::make("123456"), 'Id_Rol' => 2, 'Estado' => 1]);

    }
}