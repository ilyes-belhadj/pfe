<?php

namespace Database\Seeders;

use App\Models\Formation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FormationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $formations = [
            [
                'titre' => 'Formation Laravel Avancé',
                'description' => 'Formation complète sur Laravel avec les bonnes pratiques et les fonctionnalités avancées',
                'formateur' => 'Jean Dupont',
                'date_debut' => '2025-08-15',
                'date_fin' => '2025-08-17',
                'duree_heures' => 24,
                'cout' => 1500.00,
                'statut' => 'planifie',
                'lieu' => 'Salle de formation A',
                'nombre_places' => 15
            ],
            [
                'titre' => 'Formation Vue.js pour débutants',
                'description' => 'Introduction à Vue.js et développement frontend moderne',
                'formateur' => 'Marie Martin',
                'date_debut' => '2025-09-10',
                'date_fin' => '2025-09-12',
                'duree_heures' => 18,
                'cout' => 1200.00,
                'statut' => 'en_cours',
                'lieu' => 'Salle de formation B',
                'nombre_places' => 12
            ],
            [
                'titre' => 'Formation DevOps et CI/CD',
                'description' => 'Mise en place de pipelines CI/CD avec Docker et Kubernetes',
                'formateur' => 'Pierre Durand',
                'date_debut' => '2025-10-05',
                'date_fin' => '2025-10-07',
                'duree_heures' => 21,
                'cout' => 2000.00,
                'statut' => 'planifie',
                'lieu' => 'Salle de formation C',
                'nombre_places' => 10
            ]
        ];

        foreach ($formations as $formation) {
            Formation::create($formation);
        }
    }
}
