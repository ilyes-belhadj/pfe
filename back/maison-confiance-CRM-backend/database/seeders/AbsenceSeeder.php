<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Absence;

class AbsenceSeeder extends Seeder
{
    public function run(): void
    {
        Absence::create([
            'date_debut' => '2025-07-01',
            'date_fin' => '2025-07-05',
            'motif' => 'Congé annuel',
            'employe_id' => 1, // Assure-toi que l'employé existe
        ]);

        Absence::create([
            'date_debut' => '2025-06-15',
            'date_fin' => '2025-06-18',
            'motif' => 'Maladie',
            'employe_id' => 2,
        ]);
    }
}
