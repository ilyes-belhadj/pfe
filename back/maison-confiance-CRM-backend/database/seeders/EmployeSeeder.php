<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employe;
use App\Models\Departement;

class EmployeSeeder extends Seeder
{
    public function run(): void
    {
        // Assurez-vous qu'il y a au moins un département
        $departement = Departement::first();
        
        if (!$departement) {
            $departement = Departement::create([
                'nom' => 'Département Général',
                'description' => 'Département par défaut'
            ]);
        }

        Employe::create([
            'nom' => 'Dupont',
            'prenom' => 'Jean',
            'email' => 'jean.dupont@example.com',
            'date_embauche' => '2023-01-15',
            'salaire' => 3500.00,
            'departement_id' => $departement->id,
        ]);

        Employe::create([
            'nom' => 'Martin',
            'prenom' => 'Marie',
            'email' => 'marie.martin@example.com',
            'date_embauche' => '2023-03-20',
            'salaire' => 3200.00,
            'departement_id' => $departement->id,
        ]);

        Employe::create([
            'nom' => 'Bernard',
            'prenom' => 'Pierre',
            'email' => 'pierre.bernard@example.com',
            'date_embauche' => '2022-11-10',
            'salaire' => 3800.00,
            'departement_id' => $departement->id,
        ]);
    }
}
