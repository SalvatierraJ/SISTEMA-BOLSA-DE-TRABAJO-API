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
        Telefono::create(["Numero" => "123456789"]);
        Telefono::create(["Numero" => "555555555"]);
        Telefono::create(["Numero" => "987654321"]);
        Telefono::create(["Numero" => "111222333"]);
        Telefono::create(["Numero" => "444555666"]);
        Telefono::create(["Numero" => "777888999"]);
        Telefono::create(["Numero" => "000111222"]);
        Telefono::create(["Numero" => "333444555"]);
        Telefono::create(["Numero" => "666777888"]);
        Telefono::create(["Numero" => "999000111"]);
        Telefono::create(["Numero" => "222333444"]);
        Telefono::create(["Numero" => "555666777"]);
        Telefono::create(["Numero" => "888999000"]);
        Telefono::create(["Numero" => "123123123"]);
        Telefono::create(["Numero" => "456456456"]);
        Telefono::create(["Numero" => "789789789"]);
        Telefono::create(["Numero" => "321321321"]);
        Telefono::create(["Numero" => "654654654"]);        
    }
}