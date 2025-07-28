<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Candidature;
use App\Models\Candidat;
use App\Models\Departement;
use App\Models\User;
use Carbon\Carbon;

class CandidatureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer les candidats et départements existants
        $candidats = Candidat::all();
        $departements = Departement::all();
        $users = User::all();

        if ($candidats->isEmpty()) {
            $this->command->error('Aucun candidat trouvé. Veuillez d\'abord exécuter CandidatSeeder.');
            return;
        }

        if ($departements->isEmpty()) {
            $this->command->error('Aucun département trouvé. Veuillez d\'abord exécuter DepartementSeeder.');
            return;
        }

        if ($users->isEmpty()) {
            $this->command->error('Aucun utilisateur trouvé. Veuillez d\'abord exécuter DatabaseSeeder.');
            return;
        }

        $postes = [
            'Développeur Full-Stack',
            'Développeur Backend',
            'Développeur Frontend',
            'DevOps Engineer',
            'Data Scientist',
            'UX/UI Designer',
            'Product Manager',
            'Développeur Mobile',
            'Lead Developer',
            'Architecte Logiciel',
            'Scrum Master',
            'Testeur QA',
            'Développeur Python',
            'Développeur Java',
            'Développeur React',
        ];

        $statuts = [
            'nouvelle' => 0.3,
            'en_cours' => 0.2,
            'entretien_telephone' => 0.1,
            'entretien_rh' => 0.1,
            'entretien_technique' => 0.1,
            'test_technique' => 0.05,
            'offre_envoyee' => 0.05,
            'embauche' => 0.05,
            'refusee' => 0.05,
        ];

        $priorites = ['basse', 'normale', 'haute', 'urgente'];
        $sources = ['LinkedIn', 'Indeed', 'Spontanée', 'Apec', 'Pôle Emploi'];

        $candidatures = [];

        foreach ($candidats as $candidat) {
            // Créer 1 à 3 candidatures par candidat
            $nombreCandidatures = rand(1, 3);
            
            for ($i = 0; $i < $nombreCandidatures; $i++) {
                $statut = $this->getRandomStatut($statuts);
                $dateCandidature = Carbon::now()->subDays(rand(0, 90));
                $dateDerniereAction = $statut !== 'nouvelle' ? $dateCandidature->copy()->addDays(rand(1, 30)) : null;
                
                $candidature = [
                    'candidat_id' => $candidat->id,
                    'departement_id' => $departements->random()->id,
                    'poste_souhaite' => $postes[array_rand($postes)],
                    'lettre_motivation' => $this->generateLettreMotivation($candidat),
                    'statut' => $statut,
                    'priorite' => $priorites[array_rand($priorites)],
                    'date_candidature' => $dateCandidature,
                    'date_derniere_action' => $dateDerniereAction,
                    'source_candidature' => $sources[array_rand($sources)],
                    'candidature_spontanee' => rand(0, 1),
                    'recruteur_id' => $users->random()->id,
                    'manager_id' => rand(0, 1) ? $users->random()->id : null,
                ];

                // Ajouter des données conditionnelles selon le statut
                if (in_array($statut, ['entretien_telephone', 'entretien_rh', 'entretien_technique'])) {
                    $candidature['date_entretien'] = Carbon::now()->addDays(rand(1, 14));
                    $candidature['heure_entretien'] = Carbon::createFromTime(rand(9, 17), rand(0, 3) * 15);
                    $candidature['lieu_entretien'] = 'Bureau RH - Étage 2';
                    $candidature['notes_entretien'] = 'Entretien prévu avec le manager du département.';
                }

                if (in_array($statut, ['test_technique', 'offre_envoyee', 'embauche'])) {
                    $candidature['evaluation'] = 'Candidat très prometteur avec de bonnes compétences techniques.';
                    $candidature['note_globale'] = rand(7, 10);
                    $candidature['commentaires_rh'] = 'Profil intéressant, bonne motivation.';
                    $candidature['commentaires_technique'] = 'Compétences techniques solides, bonne expérience.';
                }

                if ($statut === 'embauche') {
                    $candidature['salaire_propose'] = $candidat->pretention_salaire ?? rand(35000, 80000);
                    $candidature['date_debut_souhaite'] = Carbon::now()->addMonths(rand(1, 3));
                }

                if ($statut === 'refusee') {
                    $candidature['motif_refus'] = 'Profil ne correspondant pas aux attentes du poste.';
                }

                $candidatures[] = $candidature;
            }
        }

        foreach ($candidatures as $candidature) {
            Candidature::create($candidature);
        }

        $this->command->info('Candidatures créées avec succès !');
    }

    private function getRandomStatut($statuts)
    {
        $rand = mt_rand() / mt_getrandmax();
        $cumulative = 0;
        
        foreach ($statuts as $statut => $probability) {
            $cumulative += $probability;
            if ($rand <= $cumulative) {
                return $statut;
            }
        }
        
        return 'nouvelle';
    }

    private function generateLettreMotivation($candidat)
    {
        $lettres = [
            "Madame, Monsieur,\n\nJe me permets de vous présenter ma candidature pour le poste de développeur au sein de votre entreprise. Avec mon expérience en développement web et ma passion pour les nouvelles technologies, je suis convaincu(e) de pouvoir apporter une valeur ajoutée à votre équipe.\n\nJe reste à votre disposition pour un entretien.\n\nCordialement,\n" . $candidat->prenom . ' ' . $candidat->nom,
            
            "Bonjour,\n\nSuite à votre annonce, je souhaite vous présenter ma candidature. Mon parcours professionnel et mes compétences techniques correspondent parfaitement au profil recherché.\n\nJe serais ravi(e) d'échanger avec vous lors d'un entretien.\n\nBien cordialement,\n" . $candidat->prenom . ' ' . $candidat->nom,
            
            "Madame, Monsieur,\n\nPassionné(e) par le développement informatique, je vous présente ma candidature. Mon expérience et ma motivation font de moi un(e) candidat(e) sérieux(se) pour ce poste.\n\nJe reste disponible pour un entretien.\n\nCordialement,\n" . $candidat->prenom . ' ' . $candidat->nom,
        ];

        return $lettres[array_rand($lettres)];
    }
}
