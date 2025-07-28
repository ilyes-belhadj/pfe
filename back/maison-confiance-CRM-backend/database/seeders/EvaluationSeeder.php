<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Evaluation;
use App\Models\Candidat;
use App\Models\Employe;
use App\Models\User;
use App\Models\Departement;
use Carbon\Carbon;

class EvaluationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer les données existantes
        $candidats = Candidat::all();
        $employes = Employe::all();
        $users = User::all();
        $departements = Departement::all();

        if ($candidats->isEmpty() || $users->isEmpty() || $departements->isEmpty()) {
            $this->command->info('Impossible de créer les évaluations : données manquantes (candidats, users, départements)');
            return;
        }

        // Critères d'évaluation standard
        $criteresStandard = [
            'competences_techniques' => [
                'nom' => 'Compétences techniques',
                'poids' => 30,
                'description' => 'Maîtrise des outils et technologies requises'
            ],
            'experience' => [
                'nom' => 'Expérience',
                'poids' => 25,
                'description' => 'Pertinence de l\'expérience professionnelle'
            ],
            'motivation' => [
                'nom' => 'Motivation',
                'poids' => 20,
                'description' => 'Intérêt et motivation pour le poste'
            ],
            'communication' => [
                'nom' => 'Communication',
                'poids' => 15,
                'description' => 'Capacités de communication orale et écrite'
            ],
            'integration' => [
                'nom' => 'Intégration',
                'poids' => 10,
                'description' => 'Capacité à s\'intégrer dans l\'équipe'
            ]
        ];

        // Évaluations pour les candidats
        foreach ($candidats->take(10) as $candidat) {
            $evaluateur = $users->random();
            $departement = $departements->random();
            
            // Évaluation initiale
            $evaluation = Evaluation::create([
                'titre' => 'Évaluation initiale - ' . $candidat->nom . ' ' . $candidat->prenom,
                'description' => 'Évaluation initiale du candidat pour le poste de ' . $departement->nom,
                'type' => 'candidat',
                'statut' => $this->getRandomStatut(),
                'priorite' => $this->getRandomPriorite(),
                'evaluable_type' => Candidat::class,
                'evaluable_id' => $candidat->id,
                'evaluateur_id' => $evaluateur->id,
                'manager_id' => $users->random()->id,
                'departement_id' => $departement->id,
                'date_evaluation' => Carbon::now()->subDays(rand(1, 30)),
                'date_limite' => Carbon::now()->addDays(rand(1, 14)),
                'criteres_evaluation' => $criteresStandard,
                'resultats' => $this->generateResultats($criteresStandard),
                'note_globale' => rand(60, 95) / 10,
                'note_competences' => rand(60, 95) / 10,
                'note_performance' => rand(60, 95) / 10,
                'note_comportement' => rand(60, 95) / 10,
                'note_potentiel' => rand(60, 95) / 10,
                'forces' => $this->getRandomForces(),
                'axes_amelioration' => $this->getRandomAxesAmelioration(),
                'objectifs' => $this->getRandomObjectifs(),
                'recommandations' => $this->getRandomRecommandations(),
                'commentaires_evaluateur' => $this->getRandomCommentairesEvaluateur(),
                'recommandation' => $this->getRandomRecommandation(),
                'justification_recommandation' => $this->getRandomJustification(),
                'version_grille' => 'v1.0',
                'reference' => 'EVAL-' . str_pad($candidat->id, 4, '0', STR_PAD_LEFT) . '-' . date('Y'),
            ]);

            // Évaluation de suivi (si candidat actif)
            if ($candidat->statut === 'actif') {
                Evaluation::create([
                    'titre' => 'Évaluation de suivi - ' . $candidat->nom . ' ' . $candidat->prenom,
                    'description' => 'Évaluation de suivi après entretien',
                    'type' => 'candidat',
                    'statut' => 'en_cours',
                    'priorite' => 'normale',
                    'evaluable_type' => Candidat::class,
                    'evaluable_id' => $candidat->id,
                    'evaluateur_id' => $evaluateur->id,
                    'manager_id' => $users->random()->id,
                    'departement_id' => $departement->id,
                    'date_evaluation' => Carbon::now()->subDays(rand(1, 7)),
                    'date_limite' => Carbon::now()->addDays(rand(3, 10)),
                    'criteres_evaluation' => $criteresStandard,
                    'version_grille' => 'v1.0',
                    'reference' => 'EVAL-SUIVI-' . str_pad($candidat->id, 4, '0', STR_PAD_LEFT) . '-' . date('Y'),
                ]);
            }
        }

        // Évaluations pour les employés
        foreach ($employes->take(8) as $employe) {
            $evaluateur = $users->random();
            $departement = $employe->departement ?? $departements->random();
            
            // Évaluation de période d'essai
            if (Carbon::parse($employe->date_embauche)->diffInDays(now()) < 90) {
                Evaluation::create([
                    'titre' => 'Évaluation période d\'essai - ' . $employe->nom . ' ' . $employe->prenom,
                    'description' => 'Évaluation de la période d\'essai',
                    'type' => 'periode_essai',
                    'statut' => $this->getRandomStatut(),
                    'priorite' => 'haute',
                    'evaluable_type' => Employe::class,
                    'evaluable_id' => $employe->id,
                    'evaluateur_id' => $evaluateur->id,
                    'manager_id' => $users->random()->id,
                    'departement_id' => $departement->id,
                    'date_evaluation' => Carbon::now()->subDays(rand(1, 30)),
                    'date_limite' => Carbon::now()->addDays(rand(1, 14)),
                    'criteres_evaluation' => $criteresStandard,
                    'resultats' => $this->generateResultats($criteresStandard),
                    'note_globale' => rand(70, 95) / 10,
                    'note_competences' => rand(70, 95) / 10,
                    'note_performance' => rand(70, 95) / 10,
                    'note_comportement' => rand(70, 95) / 10,
                    'note_potentiel' => rand(70, 95) / 10,
                    'forces' => $this->getRandomForces(),
                    'axes_amelioration' => $this->getRandomAxesAmelioration(),
                    'objectifs' => $this->getRandomObjectifs(),
                    'recommandations' => $this->getRandomRecommandations(),
                    'commentaires_evaluateur' => $this->getRandomCommentairesEvaluateur(),
                    'recommandation' => 'confirmation',
                    'justification_recommandation' => 'Performance satisfaisante pendant la période d\'essai',
                    'version_grille' => 'v1.0',
                    'reference' => 'EVAL-PERIODE-' . str_pad($employe->id, 4, '0', STR_PAD_LEFT) . '-' . date('Y'),
                ]);
            }

            // Évaluation annuelle
            Evaluation::create([
                'titre' => 'Évaluation annuelle - ' . $employe->nom . ' ' . $employe->prenom,
                'description' => 'Évaluation annuelle de performance',
                'type' => 'annuelle',
                'statut' => $this->getRandomStatut(),
                'priorite' => 'normale',
                'evaluable_type' => Employe::class,
                'evaluable_id' => $employe->id,
                'evaluateur_id' => $evaluateur->id,
                'manager_id' => $users->random()->id,
                'departement_id' => $departement->id,
                'date_evaluation' => Carbon::now()->subDays(rand(1, 60)),
                'date_limite' => Carbon::now()->addDays(rand(7, 30)),
                'criteres_evaluation' => $criteresStandard,
                'resultats' => $this->generateResultats($criteresStandard),
                'note_globale' => rand(65, 95) / 10,
                'note_competences' => rand(65, 95) / 10,
                'note_performance' => rand(65, 95) / 10,
                'note_comportement' => rand(65, 95) / 10,
                'note_potentiel' => rand(65, 95) / 10,
                'forces' => $this->getRandomForces(),
                'axes_amelioration' => $this->getRandomAxesAmelioration(),
                'objectifs' => $this->getRandomObjectifs(),
                'recommandations' => $this->getRandomRecommandations(),
                'commentaires_evaluateur' => $this->getRandomCommentairesEvaluateur(),
                'recommandation' => $this->getRandomRecommandation(),
                'justification_recommandation' => $this->getRandomJustification(),
                'version_grille' => 'v1.0',
                'reference' => 'EVAL-ANNUELLE-' . str_pad($employe->id, 4, '0', STR_PAD_LEFT) . '-' . date('Y'),
            ]);
        }

        $this->command->info('Évaluations créées avec succès !');
    }

    private function getRandomStatut()
    {
        $statuts = ['brouillon', 'en_cours', 'terminee', 'validee'];
        return $statuts[array_rand($statuts)];
    }

    private function getRandomPriorite()
    {
        $priorites = ['basse', 'normale', 'haute', 'urgente'];
        return $priorites[array_rand($priorites)];
    }

    private function generateResultats($criteres)
    {
        $resultats = [];
        foreach ($criteres as $key => $critere) {
            $resultats[$key] = [
                'note' => rand(60, 95) / 10,
                'commentaire' => $this->getRandomCommentaireCritere($key),
                'poids' => $critere['poids']
            ];
        }
        return $resultats;
    }

    private function getRandomForces()
    {
        $forces = [
            'Excellente maîtrise technique',
            'Bonne capacité d\'adaptation',
            'Esprit d\'équipe développé',
            'Autonomie dans le travail',
            'Communication claire et efficace',
            'Résolution de problèmes efficace',
            'Créativité et innovation',
            'Leadership naturel',
            'Organisation et planification',
            'Curiosité et envie d\'apprendre'
        ];
        return $forces[array_rand($forces)];
    }

    private function getRandomAxesAmelioration()
    {
        $axes = [
            'Développer la prise de parole en public',
            'Améliorer la gestion du temps',
            'Renforcer les compétences en leadership',
            'Développer l\'expertise technique',
            'Améliorer la communication écrite',
            'Développer la pensée stratégique',
            'Renforcer la gestion de projet',
            'Améliorer la résolution de conflits',
            'Développer les compétences commerciales',
            'Renforcer la créativité'
        ];
        return $axes[array_rand($axes)];
    }

    private function getRandomObjectifs()
    {
        $objectifs = [
            'Atteindre un niveau expert dans les technologies principales',
            'Prendre en charge un projet d\'envergure',
            'Former 2 nouveaux collaborateurs',
            'Améliorer les processus de l\'équipe',
            'Développer de nouvelles compétences techniques',
            'Contribuer à l\'innovation de l\'entreprise',
            'Renforcer la collaboration inter-départements',
            'Optimiser les performances de l\'équipe',
            'Développer des solutions innovantes',
            'Mentorer les juniors de l\'équipe'
        ];
        return $objectifs[array_rand($objectifs)];
    }

    private function getRandomRecommandations()
    {
        $recommandations = [
            'Continuer le développement professionnel',
            'Participer à des formations spécialisées',
            'Prendre plus de responsabilités',
            'Développer l\'expertise technique',
            'Améliorer la communication',
            'Renforcer le leadership',
            'Diversifier les compétences',
            'Optimiser les performances',
            'Développer la créativité',
            'Renforcer l\'autonomie'
        ];
        return $recommandations[array_rand($recommandations)];
    }

    private function getRandomCommentairesEvaluateur()
    {
        $commentaires = [
            'Excellent profil, très prometteur pour l\'entreprise',
            'Bonne performance, quelques axes d\'amélioration identifiés',
            'Collaborateur motivé avec un bon potentiel',
            'Résultats satisfaisants, continuez dans cette direction',
            'Profil intéressant, développement à poursuivre',
            'Bonne intégration, progression positive',
            'Compétences techniques solides, à développer',
            'Performance correcte, amélioration possible',
            'Collaborateur fiable et consciencieux',
            'Potentiel intéressant, formation recommandée'
        ];
        return $commentaires[array_rand($commentaires)];
    }

    private function getRandomRecommandation()
    {
        $recommandations = ['embauche', 'confirmation', 'promotion', 'formation'];
        return $recommandations[array_rand($recommandations)];
    }

    private function getRandomJustification()
    {
        $justifications = [
            'Profil correspondant parfaitement aux besoins',
            'Performance satisfaisante et engagement positif',
            'Potentiel élevé et motivation importante',
            'Compétences techniques et comportementales adaptées',
            'Intégration réussie et résultats probants',
            'Développement professionnel prometteur',
            'Contribution positive à l\'équipe',
            'Capacités d\'évolution intéressantes',
            'Adéquation avec les valeurs de l\'entreprise',
            'Potentiel de croissance important'
        ];
        return $justifications[array_rand($justifications)];
    }

    private function getRandomCommentaireCritere($critere)
    {
        $commentaires = [
            'competences_techniques' => 'Maîtrise solide des technologies requises',
            'experience' => 'Expérience pertinente et variée',
            'motivation' => 'Motivation élevée et engagement visible',
            'communication' => 'Communication claire et efficace',
            'integration' => 'Intégration réussie dans l\'équipe'
        ];
        
        return $commentaires[$critere] ?? 'Évaluation positive';
    }
}
