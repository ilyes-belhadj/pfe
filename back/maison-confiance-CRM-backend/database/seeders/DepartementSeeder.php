<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Departement;

class DepartementSeeder extends Seeder
{
    public function run(): void
    {
        Departement::create([
            'nom' => 'Informatique',
            'description' => 'Gère les systèmes d’information',
        ]);

        Departement::create([
            'nom' => 'Ressources Humaines',
            'description' => 'Gère le personnel et les recrutements',
        ]);

        Departement::create([
            'nom' => 'Finance',
            'description' => 'Gère les finances et la comptabilité',
        ]);
    }
}
