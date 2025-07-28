<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Pointage;
use App\Models\Employe;
use Carbon\Carbon;

class PointageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $employes = Employe::all();

        if ($employes->isEmpty()) {
            $this->command->info('Aucun employé trouvé. Création de pointages annulée.');
            return;
        }

        // Créer des pointages pour les 30 derniers jours
        for ($i = 30; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            
            foreach ($employes as $employe) {
                // 80% de chance d'avoir un pointage (simule les absences)
                if (rand(1, 100) <= 80) {
                    $this->creerPointage($employe, $date);
                }
            }
        }

        $this->command->info('Pointages créés avec succès !');
    }

    private function creerPointage(Employe $employe, Carbon $date)
    {
        // Heures de travail typiques (8h par jour)
        $heure_entree = $date->copy()->setTime(rand(7, 9), rand(0, 59), 0);
        $heure_sortie = $date->copy()->setTime(rand(17, 19), rand(0, 59), 0);
        
        // Calculer les heures travaillées
        $heures_travaillees = $heure_entree->diffInHours($heure_sortie, true);
        
        // Pause déjeuner (1h)
        $heure_pause_debut = $date->copy()->setTime(12, 0, 0);
        $heure_pause_fin = $date->copy()->setTime(13, 0, 0);
        $heures_pause = 1.0;
        
        // Heures nettes
        $heures_net = $heures_travaillees - $heures_pause;
        
        // Statut (majorité présents, quelques retards/absences)
        $statuts = ['present', 'present', 'present', 'present', 'present', 'retard', 'depart_anticipé'];
        $statut = $statuts[array_rand($statuts)];
        
        // Lieu de pointage
        $lieux = ['bureau', 'bureau', 'bureau', 'teletravail', 'deplacement'];
        $lieu_pointage = $lieux[array_rand($lieux)];
        
        // Méthode de pointage
        $methodes = ['application', 'badge', 'manuel'];
        $methode_pointage = $methodes[array_rand($methodes)];
        
        // Commentaires occasionnels
        $commentaires = [
            null,
            'Réunion importante',
            'Formation en cours',
            'Déplacement client',
            'Télétravail autorisé',
            'Projet urgent',
        ];
        $commentaire = $commentaires[array_rand($commentaires)];
        
        // Validation (90% validés)
        $valide = rand(1, 100) <= 90;
        
        Pointage::create([
            'employe_id' => $employe->id,
            'date_pointage' => $date->toDateString(),
            'heure_entree' => $heure_entree->format('H:i:s'),
            'heure_sortie' => $heure_sortie->format('H:i:s'),
            'heure_pause_debut' => $heure_pause_debut->format('H:i:s'),
            'heure_pause_fin' => $heure_pause_fin->format('H:i:s'),
            'heures_travaillees' => $heures_travaillees,
            'heures_pause' => $heures_pause,
            'heures_net' => $heures_net,
            'statut' => $statut,
            'commentaire' => $commentaire,
            'lieu_pointage' => $lieu_pointage,
            'methode_pointage' => $methode_pointage,
            'ip_address' => '192.168.1.' . rand(1, 254),
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
            'latitude' => 48.8566 + (rand(-10, 10) / 1000), // Paris + variation
            'longitude' => 2.3522 + (rand(-10, 10) / 1000), // Paris + variation
            'valide' => $valide,
            'valide_par' => $valide ? \App\Models\User::first()?->id : null,
            'valide_le' => $valide ? now() : null,
        ]);
    }
}
