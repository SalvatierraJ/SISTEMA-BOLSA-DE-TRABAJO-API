<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Empresa;
class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Empresa::create(["NIT" => "1221212", "Nombre"=> "Coca Cola", "Direccion" => "Av Cumavi", "Direccion_Web" => "cocacola.org", "Correo" => "cocacola@coca.com", "Id_Usuario" => 10, "Id_Sector" => 3]);
        Empresa::create(["NIT" => "1223212", "Nombre"=> "Tigo", "Direccion" => "Santos dusmont", "Direccion_Web" => "tigo.org", "Correo" => "tigo@coca.com","Id_Usuario" => 9, "Id_Sector" => 10]);
        Empresa::create(["NIT" => "1243212", "Nombre"=> "Entel", "Direccion" => "Av alemana", "Direccion_Web" => "Entel.org", "Correo" => "entel@entel.com","Id_Usuario" => 8, "Id_Sector" => 10]);
        Empresa::create(["NIT" => "1243212", "Nombre"=>"Viva", "Direccion" => "Av Irala", "Direccion_Web" => "Viva.org", "Correo" => "Viva@Viva.com",  "Id_Usuario" => 7, "Id_Sector" => 10]);

    }   
}