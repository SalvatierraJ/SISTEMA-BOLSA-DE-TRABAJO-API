<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Sector;
class SectorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Sector::create(["Nombre" => "Tecnologia"]);
        Sector::create(["Nombre" => "Salud"]);
        Sector::create(["Nombre" => "Educacion"]);
        Sector::create(["Nombre" => "Comercio"]);
        Sector::create(["Nombre" => "Construccion"]);
        Sector::create(["Nombre" => "Turismo"]);
        Sector::create(["Nombre" => "Transporte"]);
        Sector::create(["Nombre" => "Agricultura"]);
        Sector::create(["Nombre" => "Energia"]);
        Sector::create(["Nombre" => "Finanzas"]);
        Sector::create(["Nombre" => "Telecomunicaciones"]);
        Sector::create(["Nombre" => "Medios de comunicacion"]);
        Sector::create(["Nombre" => "Entretenimiento"]);
        Sector::create(["Nombre" => "Alimentacion"]);
        Sector::create(["Nombre" => "Servicios Publicos"]);
        Sector::create(["Nombre" => "Seguridad"]);
        Sector::create(["Nombre" => "Inmobiliaria"]);
        Sector::create(["Nombre" => "Automotriz"]);
        Sector::create(["Nombre" => "Moda"]);
        Sector::create(["Nombre" => "Arte"]);
        Sector::create(["Nombre" => "Ciencia"]);
        Sector::create(["Nombre" => "Investigacion"]);
        Sector::create(["Nombre" => "Logistica"]);
        Sector::create(["Nombre" => "Recursos Humanos"]);
        Sector::create(["Nombre" => "Marketing"]);
        Sector::create(["Nombre" => "Publicidad"]);
        Sector::create(["Nombre" => "Consultoria"]);
        Sector::create(["Nombre" => "Asesoramiento"]);
        Sector::create(["Nombre" => "Desarrollo de Software"]);
        Sector::create(["Nombre" => "Analisis de Datos"]);
        Sector::create(["Nombre" => "Inteligencia Artificial"]);
        Sector::create(["Nombre" => "Blockchain"]);
        Sector::create(["Nombre" => "Realidad Aumentada"]);
        Sector::create(["Nombre" => "Realidad Virtual"]);
        Sector::create(["Nombre" => "Ciberseguridad"]);
        Sector::create(["Nombre" => "Big Data"]);
        Sector::create(["Nombre" => "Cloud Computing"]);
        Sector::create(["Nombre" => "Internet de las Cosas"]);
        Sector::create(["Nombre" => "Desarrollo Web"]);
        Sector::create(["Nombre" => "Desarrollo Movil"]);
        Sector::create(["Nombre" => "Desarrollo de Videojuegos"]);
        Sector::create(["Nombre" => "Desarrollo de Aplicaciones"]);
        Sector::create(["Nombre" => "Desarrollo de Sistemas"]);
        Sector::create(["Nombre" => "Desarrollo de Bases de Datos"]);
        Sector::create(["Nombre" => "Desarrollo de Infraestructura"]);
    }
}