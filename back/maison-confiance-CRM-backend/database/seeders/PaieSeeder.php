<?php

namespace Database\Seeders;

use App\Models\Paie;
use App\Models\Employe;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaieSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $employes = Employe::all();

        if ($employes->isEmpty()) {
            $this->command->info('Aucun employé trouvé. Création des paies ignorée.');
            return;
        }

        $periodes = [
            '2025-06',
            '2025-07',
            '2025-08'
        ];

        $statuts = ['en_attente', 'paye'];
        $modesPaiement = ['virement', 'cheque', 'especes'];

        foreach ($employes as $employe) {
            foreach ($periodes as $periode) {
                $salaireBase = rand(2500, 4500);
                $heuresTravaillees = rand(150, 180);
                $tauxHoraire = rand(15, 25);
                $salaireHoraire = $heuresTravaillees * $tauxHoraire;
                $salaireBrut = $salaireBase + $salaireHoraire;
                
                $primes = rand(0, 500);
                $deductions = rand(0, 200);
                $cotisationsSociales = $salaireBrut * 0.15; // 15% de cotisations
                $impots = $salaireBrut * 0.10; // 10% d'impôts
                
                $totalDeductions = $deductions + $cotisationsSociales + $impots;
                $salaireNet = $salaireBrut + $primes - $totalDeductions;

                Paie::create([
                    'employe_id' => $employe->id,
                    'periode' => $periode,
                    'date_paiement' => now()->subMonths(rand(1, 3)),
                    'salaire_base' => $salaireBase,
                    'heures_travaillees' => $heuresTravaillees,
                    'taux_horaire' => $tauxHoraire,
                    'salaire_brut' => $salaireBrut,
                    'primes' => $primes,
                    'deductions' => $deductions,
                    'cotisations_sociales' => $cotisationsSociales,
                    'impots' => $impots,
                    'salaire_net' => $salaireNet,
                    'statut' => $statuts[array_rand($statuts)],
                    'notes' => 'Paie générée automatiquement pour les tests',
                    'mode_paiement' => $modesPaiement[array_rand($modesPaiement)],
                    'numero_cheque' => $statuts[array_rand($statuts)] === 'paye' ? 'CHK' . rand(1000, 9999) : null,
                    'reference_paiement' => 'REF' . rand(10000, 99999),
                ]);
            }
        }

        $this->command->info('Paies créées avec succès !');
    }
}
