<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\Http;

// Configuration
$baseUrl = 'http://localhost:8000/api';
$token = null;

echo "=== Test du module Candidature ===\n\n";

// 1. Authentification
echo "1. Authentification...\n";
try {
    $response = Http::post($baseUrl . '/auth/login', [
        'email' => 'admin@maison-confiance.com',
        'password' => 'password123'
    ]);
    
    if ($response->successful()) {
        $token = $response->json('access_token');
        echo "✅ Authentification réussie\n";
    } else {
        echo "❌ Échec de l'authentification: " . $response->status() . "\n";
        echo "Réponse: " . $response->body() . "\n";
        exit;
    }
} catch (Exception $e) {
    echo "❌ Erreur d'authentification: " . $e->getMessage() . "\n";
    exit;
}

// Headers pour les requêtes authentifiées
$headers = [
    'Authorization' => 'Bearer ' . $token,
    'Accept' => 'application/json',
    'Content-Type' => 'application/json'
];

// 2. Test des candidatures
echo "\n2. Test des endpoints Candidature...\n";

// 2.1 Liste des candidatures
echo "\n2.1. Liste des candidatures...\n";
try {
    $response = Http::withHeaders($headers)->get($baseUrl . '/candidatures');
    if ($response->successful()) {
        $candidatures = $response->json();
        echo "✅ Liste des candidatures récupérée (" . count($candidatures) . " candidatures)\n";
    } else {
        echo "❌ Erreur lors de la récupération des candidatures: " . $response->status() . "\n";
        echo "Réponse: " . $response->body() . "\n";
    }
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}

// 2.2 Candidatures actives
echo "\n2.2. Candidatures actives...\n";
try {
    $response = Http::withHeaders($headers)->get($baseUrl . '/candidatures/actives');
    if ($response->successful()) {
        $candidatures = $response->json();
        echo "✅ Candidatures actives récupérées (" . count($candidatures) . " candidatures)\n";
    } else {
        echo "❌ Erreur lors de la récupération des candidatures actives: " . $response->status() . "\n";
        echo "Réponse: " . $response->body() . "\n";
    }
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}

// 2.3 Candidatures récentes
echo "\n2.3. Candidatures récentes...\n";
try {
    $response = Http::withHeaders($headers)->get($baseUrl . '/candidatures/recents');
    if ($response->successful()) {
        $candidatures = $response->json();
        echo "✅ Candidatures récentes récupérées (" . count($candidatures) . " candidatures)\n";
    } else {
        echo "❌ Erreur lors de la récupération des candidatures récentes: " . $response->status() . "\n";
        echo "Réponse: " . $response->body() . "\n";
    }
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}

// 2.4 Candidatures spontanées
echo "\n2.4. Candidatures spontanées...\n";
try {
    $response = Http::withHeaders($headers)->get($baseUrl . '/candidatures/spontanees');
    if ($response->successful()) {
        $candidatures = $response->json();
        echo "✅ Candidatures spontanées récupérées (" . count($candidatures) . " candidatures)\n";
    } else {
        echo "❌ Erreur lors de la récupération des candidatures spontanées: " . $response->status() . "\n";
        echo "Réponse: " . $response->body() . "\n";
    }
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}

// 2.5 Statistiques des candidatures
echo "\n2.5. Statistiques des candidatures...\n";
try {
    $response = Http::withHeaders($headers)->get($baseUrl . '/candidatures/statistiques');
    if ($response->successful()) {
        $stats = $response->json();
        echo "✅ Statistiques récupérées\n";
        echo "   - Total: " . ($stats['total'] ?? 'N/A') . "\n";
        echo "   - Actives: " . ($stats['actives'] ?? 'N/A') . "\n";
        echo "   - Embauchées: " . ($stats['embauchees'] ?? 'N/A') . "\n";
        echo "   - Refusées: " . ($stats['refusees'] ?? 'N/A') . "\n";
    } else {
        echo "❌ Erreur lors de la récupération des statistiques: " . $response->status() . "\n";
        echo "Réponse: " . $response->body() . "\n";
    }
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}

// 2.6 Recherche de candidatures
echo "\n2.6. Recherche de candidatures...\n";
try {
    $response = Http::withHeaders($headers)->get($baseUrl . '/candidatures/rechercher', [
        'q' => 'développeur'
    ]);
    if ($response->successful()) {
        $candidatures = $response->json();
        echo "✅ Recherche effectuée (" . count($candidatures) . " résultats)\n";
    } else {
        echo "❌ Erreur lors de la recherche: " . $response->status() . "\n";
        echo "Réponse: " . $response->body() . "\n";
    }
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}

// 3. Test de création d'une candidature
echo "\n3. Test de création d'une candidature...\n";
try {
    $candidatureData = [
        'candidat_id' => 1, // Assurez-vous qu'un candidat existe
        'departement_id' => 1, // Assurez-vous qu'un département existe
        'poste_souhaite' => 'Développeur Full Stack',
        'lettre_motivation' => 'Je suis très motivé pour rejoindre votre équipe...',
        'statut' => 'nouvelle',
        'priorite' => 'normale',
        'date_candidature' => now()->format('Y-m-d'),
        'source_candidature' => 'LinkedIn',
        'candidature_spontanee' => false
    ];
    
    $response = Http::withHeaders($headers)->post($baseUrl . '/candidatures', $candidatureData);
    if ($response->successful()) {
        $candidature = $response->json();
        echo "✅ Candidature créée avec succès (ID: " . $candidature['id'] . ")\n";
        $candidatureId = $candidature['id'];
    } else {
        echo "❌ Erreur lors de la création de la candidature: " . $response->status() . "\n";
        echo "Réponse: " . $response->body() . "\n";
        $candidatureId = 1; // Utiliser un ID par défaut pour les tests suivants
    }
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
    $candidatureId = 1;
}

// 4. Test des actions sur une candidature
if (isset($candidatureId)) {
    echo "\n4. Test des actions sur la candidature...\n";
    
    // 4.1 Détails de la candidature
    echo "\n4.1. Détails de la candidature...\n";
    try {
        $response = Http::withHeaders($headers)->get($baseUrl . '/candidatures/' . $candidatureId);
        if ($response->successful()) {
            $candidature = $response->json();
            echo "✅ Détails de la candidature récupérés\n";
        } else {
            echo "❌ Erreur lors de la récupération des détails: " . $response->status() . "\n";
            echo "Réponse: " . $response->body() . "\n";
        }
    } catch (Exception $e) {
        echo "❌ Erreur: " . $e->getMessage() . "\n";
    }
    
    // 4.2 Changer le statut
    echo "\n4.2. Changement de statut...\n";
    try {
        $response = Http::withHeaders($headers)->post($baseUrl . '/candidatures/' . $candidatureId . '/changer-statut', [
            'statut' => 'en_cours'
        ]);
        if ($response->successful()) {
            echo "✅ Statut changé avec succès\n";
        } else {
            echo "❌ Erreur lors du changement de statut: " . $response->status() . "\n";
            echo "Réponse: " . $response->body() . "\n";
        }
    } catch (Exception $e) {
        echo "❌ Erreur: " . $e->getMessage() . "\n";
    }
    
    // 4.3 Planifier un entretien
    echo "\n4.3. Planification d'un entretien...\n";
    try {
        $response = Http::withHeaders($headers)->post($baseUrl . '/candidatures/' . $candidatureId . '/planifier-entretien', [
            'date_entretien' => now()->addDays(7)->format('Y-m-d'),
            'heure_entretien' => '14:00',
            'lieu_entretien' => 'Salle de réunion A',
            'notes' => 'Entretien technique avec l\'équipe développement'
        ]);
        if ($response->successful()) {
            echo "✅ Entretien planifié avec succès\n";
        } else {
            echo "❌ Erreur lors de la planification: " . $response->status() . "\n";
            echo "Réponse: " . $response->body() . "\n";
        }
    } catch (Exception $e) {
        echo "❌ Erreur: " . $e->getMessage() . "\n";
    }
    
    // 4.4 Évaluer la candidature
    echo "\n4.4. Évaluation de la candidature...\n";
    try {
        $response = Http::withHeaders($headers)->post($baseUrl . '/candidatures/' . $candidatureId . '/evaluer', [
            'note_globale' => 8.5,
            'evaluation' => 'Excellent profil, très motivé',
            'commentaires_rh' => 'Bon contact, professionnel',
            'commentaires_technique' => 'Compétences techniques solides',
            'commentaires_manager' => 'Semble bien s\'intégrer dans l\'équipe'
        ]);
        if ($response->successful()) {
            echo "✅ Candidature évaluée avec succès\n";
        } else {
            echo "❌ Erreur lors de l'évaluation: " . $response->status() . "\n";
            echo "Réponse: " . $response->body() . "\n";
        }
    } catch (Exception $e) {
        echo "❌ Erreur: " . $e->getMessage() . "\n";
    }
}

// 5. Test des candidats
echo "\n5. Test des endpoints Candidat...\n";

// 5.1 Liste des candidats
echo "\n5.1. Liste des candidats...\n";
try {
    $response = Http::withHeaders($headers)->get($baseUrl . '/candidats');
    if ($response->successful()) {
        $candidats = $response->json();
        echo "✅ Liste des candidats récupérée (" . count($candidats) . " candidats)\n";
    } else {
        echo "❌ Erreur lors de la récupération des candidats: " . $response->status() . "\n";
        echo "Réponse: " . $response->body() . "\n";
    }
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}

// 5.2 Candidats actifs
echo "\n5.2. Candidats actifs...\n";
try {
    $response = Http::withHeaders($headers)->get($baseUrl . '/candidats/actifs');
    if ($response->successful()) {
        $candidats = $response->json();
        echo "✅ Candidats actifs récupérés (" . count($candidats) . " candidats)\n";
    } else {
        echo "❌ Erreur lors de la récupération des candidats actifs: " . $response->status() . "\n";
        echo "Réponse: " . $response->body() . "\n";
    }
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}

// 5.3 Statistiques des candidats
echo "\n5.3. Statistiques des candidats...\n";
try {
    $response = Http::withHeaders($headers)->get($baseUrl . '/candidats/statistiques');
    if ($response->successful()) {
        $stats = $response->json();
        echo "✅ Statistiques des candidats récupérées\n";
    } else {
        echo "❌ Erreur lors de la récupération des statistiques: " . $response->status() . "\n";
        echo "Réponse: " . $response->body() . "\n";
    }
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}

echo "\n=== Test du module Candidature terminé ===\n"; 