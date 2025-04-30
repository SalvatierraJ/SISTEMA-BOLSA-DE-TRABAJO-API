<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\CompanySeeder;
use Database\Seeders\SectorSeeder;
use Database\Seeders\TelefonoSeeder;
use Database\Seeders\Usuario;
use Database\Seeders\JobSeeder;
use Database\Seeders\RolSeeder;
use Database\Seeders\PersonaSeeder;
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call([
            PersonaSeeder::class,
            RolSeeder::class,
            SectorSeeder::class,
            Usuario::class,
            CompanySeeder::class,
            TelefonoSeeder::class,
            JobSeeder::class,
        ]);
   /*     User::factory()->create([ 
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]); */
    }
}