<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\OffreEmploi;
use App\Models\Departement;
use App\Models\User;
use Carbon\Carbon;

class OffreEmploiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer les données existantes
        $departements = Departement::all();
        $users = User::all();

        if ($departements->isEmpty() || $users->isEmpty()) {
            $this->command->info('Impossible de créer les offres d\'emploi : données manquantes (départements, users)');
            return;
        }

        $offres = [
            [
                'titre' => 'Développeur Full Stack Senior',
                'description' => 'Nous recherchons un développeur Full Stack Senior pour rejoindre notre équipe de développement. Vous travaillerez sur des projets innovants et utiliserez les dernières technologies.',
                'profil_recherche' => 'Profil senior avec minimum 5 ans d\'expérience en développement web. Maîtrise de PHP, JavaScript, React, Vue.js, MySQL, et des bonnes pratiques de développement.',
                'missions' => 'Développement d\'applications web, maintenance de systèmes existants, collaboration avec l\'équipe produit, formation des juniors.',
                'competences_requises' => 'PHP 8+, Laravel, JavaScript ES6+, React/Vue.js, MySQL, Git, Docker, API REST, tests unitaires.',
                'avantages' => 'Télétravail possible, mutuelle, tickets restaurant, formation continue, évolution de carrière.',
                'type_contrat' => 'CDI',
                'niveau_experience' => 'confirme',
                'niveau_etude' => 'Bac+5',
                'lieu_travail' => 'Paris',
                'mode_travail' => 'hybride',
                'nombre_poste' => 2,
                'salaire_min' => 55000,
                'salaire_max' => 75000,
                'devise_salaire' => 'EUR',
                'periode_salaire' => 'annuel',
                'date_publication' => now(),
                'date_limite_candidature' => now()->addDays(30),
                'statut' => 'active',
                'publiee' => true,
                'urgente' => true,
                'sponsorisee' => true,
                'departement_id' => $departements->first()->id,
                'recruteur_id' => $users->first()->id,
                'reference' => 'DEV-FS-2024-001',
                'tags' => ['développement', 'full-stack', 'senior', 'php', 'javascript'],
                'meta_description' => 'Poste de développeur Full Stack Senior à Paris - CDI - Salaire 55-75k€',
                'meta_keywords' => 'développeur, full stack, senior, paris, cdi, php, javascript'
            ],
            [
                'titre' => 'Chef de Projet Digital',
                'description' => 'Nous cherchons un Chef de Projet Digital pour piloter nos projets digitaux et accompagner nos clients dans leur transformation numérique.',
                'profil_recherche' => 'Profil confirmé avec expérience en gestion de projets digitaux. Capacité à gérer des équipes et à communiquer avec les clients.',
                'missions' => 'Pilotage de projets digitaux, gestion d\'équipe, relation client, reporting et suivi budgétaire.',
                'competences_requises' => 'Gestion de projet, méthodologies agiles, outils de gestion, communication, anglais courant.',
                'avantages' => 'Télétravail, mutuelle, CE, formation, évolution vers directeur de projet.',
                'type_contrat' => 'CDI',
                'niveau_experience' => 'confirme',
                'niveau_etude' => 'Bac+5',
                'lieu_travail' => 'Lyon',
                'mode_travail' => 'presentiel',
                'nombre_poste' => 1,
                'salaire_min' => 45000,
                'salaire_max' => 60000,
                'devise_salaire' => 'EUR',
                'periode_salaire' => 'annuel',
                'date_publication' => now()->subDays(5),
                'date_limite_candidature' => now()->addDays(25),
                'statut' => 'active',
                'publiee' => true,
                'urgente' => false,
                'sponsorisee' => false,
                'departement_id' => $departements->first()->id,
                'recruteur_id' => $users->first()->id,
                'reference' => 'CPD-2024-002',
                'tags' => ['chef de projet', 'digital', 'lyon', 'gestion'],
                'meta_description' => 'Poste de Chef de Projet Digital à Lyon - CDI - Salaire 45-60k€',
                'meta_keywords' => 'chef de projet, digital, lyon, cdi, gestion'
            ],
            [
                'titre' => 'Stagiaire Marketing Digital',
                'description' => 'Stage de 6 mois en marketing digital pour un étudiant en dernière année. Vous participerez aux campagnes marketing et à l\'élaboration de stratégies digitales.',
                'profil_recherche' => 'Étudiant en dernière année en marketing, communication ou école de commerce. Intérêt pour le digital et les nouvelles technologies.',
                'missions' => 'Participation aux campagnes marketing, création de contenu, analyse de données, support à l\'équipe marketing.',
                'competences_requises' => 'Pack Office, réseaux sociaux, Google Analytics, créativité, anglais.',
                'avantages' => 'Stage rémunéré, convention de stage, possibilité d\'embauche, formation.',
                'type_contrat' => 'Stage',
                'niveau_experience' => 'debutant',
                'niveau_etude' => 'Bac+5',
                'lieu_travail' => 'Marseille',
                'mode_travail' => 'hybride',
                'nombre_poste' => 1,
                'salaire_min' => 800,
                'salaire_max' => 1000,
                'devise_salaire' => 'EUR',
                'periode_salaire' => 'mensuel',
                'date_publication' => now()->subDays(10),
                'date_limite_candidature' => now()->addDays(20),
                'statut' => 'active',
                'publiee' => true,
                'urgente' => false,
                'sponsorisee' => false,
                'departement_id' => $departements->first()->id,
                'recruteur_id' => $users->first()->id,
                'reference' => 'STAGE-MKT-2024-003',
                'tags' => ['stage', 'marketing', 'digital', 'marseille'],
                'meta_description' => 'Stage Marketing Digital à Marseille - 6 mois - 800-1000€/mois',
                'meta_keywords' => 'stage, marketing, digital, marseille, étudiant'
            ],
            [
                'titre' => 'Alternant Développeur Web',
                'description' => 'Alternance en développement web pour un étudiant en formation. Vous alternerez entre formation et travail en entreprise sur des projets concrets.',
                'profil_recherche' => 'Étudiant en formation développement web, motivé et curieux. Appétence pour les nouvelles technologies.',
                'missions' => 'Développement d\'applications web, maintenance, tests, documentation, participation aux réunions d\'équipe.',
                'competences_requises' => 'Bases en HTML/CSS/JavaScript, PHP, bases de données, rigueur, autonomie.',
                'avantages' => 'Formation rémunérée, accompagnement, matériel fourni, évolution possible.',
                'type_contrat' => 'Alternance',
                'niveau_experience' => 'debutant',
                'niveau_etude' => 'Bac+3',
                'lieu_travail' => 'Toulouse',
                'mode_travail' => 'presentiel',
                'nombre_poste' => 1,
                'salaire_min' => 1200,
                'salaire_max' => 1500,
                'devise_salaire' => 'EUR',
                'periode_salaire' => 'mensuel',
                'date_publication' => now()->subDays(15),
                'date_limite_candidature' => now()->addDays(15),
                'statut' => 'active',
                'publiee' => true,
                'urgente' => false,
                'sponsorisee' => false,
                'departement_id' => $departements->first()->id,
                'recruteur_id' => $users->first()->id,
                'reference' => 'ALT-DEV-2024-004',
                'tags' => ['alternance', 'développement', 'web', 'toulouse'],
                'meta_description' => 'Alternance Développeur Web à Toulouse - Formation rémunérée - 1200-1500€/mois',
                'meta_keywords' => 'alternance, développement, web, toulouse, formation'
            ],
            [
                'titre' => 'Freelance UX/UI Designer',
                'description' => 'Nous recherchons un freelance UX/UI Designer pour des missions ponctuelles sur nos projets clients. Vous travaillerez en autonomie sur des missions variées.',
                'profil_recherche' => 'Designer expérimenté avec portfolio solide. Capacité à comprendre les besoins utilisateurs et à créer des interfaces intuitives.',
                'missions' => 'Création de maquettes, tests utilisateurs, amélioration de l\'expérience utilisateur, collaboration avec les développeurs.',
                'competences_requises' => 'Figma, Adobe Creative Suite, méthodologies UX, tests utilisateurs, anglais.',
                'avantages' => 'Télétravail, missions variées, tarifs attractifs, autonomie complète.',
                'type_contrat' => 'Freelance',
                'niveau_experience' => 'expert',
                'niveau_etude' => 'Bac+5',
                'lieu_travail' => 'Remote',
                'mode_travail' => 'teletravail',
                'nombre_poste' => 1,
                'salaire_min' => 400,
                'salaire_max' => 600,
                'devise_salaire' => 'EUR',
                'periode_salaire' => 'journalier',
                'date_publication' => now()->subDays(20),
                'date_limite_candidature' => now()->addDays(40),
                'statut' => 'active',
                'publiee' => true,
                'urgente' => false,
                'sponsorisee' => false,
                'departement_id' => $departements->first()->id,
                'recruteur_id' => $users->first()->id,
                'reference' => 'FREELANCE-UX-2024-005',
                'tags' => ['freelance', 'ux', 'ui', 'design', 'remote'],
                'meta_description' => 'Freelance UX/UI Designer - Remote - 400-600€/jour',
                'meta_keywords' => 'freelance, ux, ui, design, remote, figma'
            ]
        ];

        foreach ($offres as $offreData) {
            OffreEmploi::create($offreData);
        }

        $this->command->info('Offres d\'emploi créées avec succès !');
    }
}
