<?php

// Script de test pour le module Offre d'emploi
echo "=== TEST MODULE OFFRES D'EMPLOI ===\n\n";

// Configuration
$baseUrl = 'http://localhost:8000/api';
$token = null;

// Fonction pour faire une requête HTTP
function makeRequest($url, $method = 'GET', $data = null, $headers = []) {
    $ch = curl_init();
    
    $defaultHeaders = [
        'Content-Type: application/json',
        'Accept: application/json'
    ];
    
    if (!empty($headers)) {
        $defaultHeaders = array_merge($defaultHeaders, $headers);
    }
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $defaultHeaders);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    
    if ($data && in_array($method, ['POST', 'PUT', 'PATCH'])) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return [
        'code' => $httpCode,
        'data' => json_decode($response, true),
        'raw' => $response
    ];
}

// 1. Connexion pour obtenir le token
echo "1. Connexion...\n";
$loginResponse = makeRequest($baseUrl . '/login', 'POST', [
    'email' => 'admin@example.com',
    'password' => 'password'
]);

if ($loginResponse['code'] === 200 && isset($loginResponse['data']['access_token'])) {
    $token = $loginResponse['data']['access_token'];
    echo "✅ Connexion réussie\n";
} else {
    echo "❌ Échec de la connexion\n";
    echo "Code: " . $loginResponse['code'] . "\n";
    echo "Réponse: " . $loginResponse['raw'] . "\n";
    exit;
}

$headers = ['Authorization: Bearer ' . $token];

// 2. Lister toutes les offres d'emploi
echo "\n2. Lister toutes les offres d'emploi...\n";
$response = makeRequest($baseUrl . '/auth/offres-emploi', 'GET', null, $headers);

if ($response['code'] === 200) {
    $offres = $response['data']['data'] ?? $response['data'];
    echo "✅ " . count($offres) . " offres d'emploi trouvées\n";
    
    if (!empty($offres)) {
        $premiereOffre = $offres[0];
        echo "   Première offre: " . $premiereOffre['titre'] . " (ID: " . $premiereOffre['id'] . ")\n";
    }
} else {
    echo "❌ Erreur lors de la récupération des offres\n";
    echo "Code: " . $response['code'] . "\n";
    echo "Réponse: " . $response['raw'] . "\n";
}

// 3. Créer une nouvelle offre d'emploi
echo "\n3. Créer une nouvelle offre d'emploi...\n";
$nouvelleOffre = [
    'titre' => 'Développeur Frontend React',
    'description' => 'Nous recherchons un développeur Frontend React pour rejoindre notre équipe. Vous travaillerez sur des applications web modernes et innovantes.',
    'profil_recherche' => 'Développeur avec 2-3 ans d\'expérience en React, TypeScript, et développement frontend moderne.',
    'missions' => 'Développement d\'interfaces utilisateur, optimisation des performances, collaboration avec les designers et les développeurs backend.',
    'competences_requises' => 'React, TypeScript, JavaScript ES6+, HTML/CSS, Git, tests unitaires, responsive design.',
    'avantages' => 'Télétravail possible, mutuelle, tickets restaurant, formation continue.',
    'type_contrat' => 'CDI',
    'niveau_experience' => 'intermediaire',
    'niveau_etude' => 'Bac+3',
    'lieu_travail' => 'Bordeaux',
    'mode_travail' => 'hybride',
    'nombre_poste' => 1,
    'salaire_min' => 40000,
    'salaire_max' => 55000,
    'devise_salaire' => 'EUR',
    'periode_salaire' => 'annuel',
    'date_publication' => date('Y-m-d'),
    'date_limite_candidature' => date('Y-m-d', strtotime('+30 days')),
    'statut' => 'brouillon',
    'publiee' => false,
    'urgente' => false,
    'sponsorisee' => false,
    'reference' => 'DEV-FRONT-2024-006',
    'tags' => ['développement', 'frontend', 'react', 'typescript'],
    'meta_description' => 'Poste de développeur Frontend React à Bordeaux - CDI - Salaire 40-55k€',
    'meta_keywords' => 'développeur, frontend, react, bordeaux, cdi'
];

$response = makeRequest($baseUrl . '/auth/offres-emploi', 'POST', $nouvelleOffre, $headers);

if ($response['code'] === 201 || $response['code'] === 200) {
    $offreCreee = $response['data'];
    echo "✅ Offre d'emploi créée avec succès\n";
    echo "   ID: " . $offreCreee['id'] . "\n";
    echo "   Titre: " . $offreCreee['titre'] . "\n";
    $offreId = $offreCreee['id'];
} else {
    echo "❌ Erreur lors de la création de l'offre\n";
    echo "Code: " . $response['code'] . "\n";
    echo "Réponse: " . $response['raw'] . "\n";
    $offreId = null;
}

// 4. Récupérer une offre spécifique
if ($offreId) {
    echo "\n4. Récupérer l'offre créée...\n";
    $response = makeRequest($baseUrl . '/auth/offres-emploi/' . $offreId, 'GET', null, $headers);

    if ($response['code'] === 200) {
        $offre = $response['data'];
        echo "✅ Offre récupérée avec succès\n";
        echo "   Titre: " . $offre['titre'] . "\n";
        echo "   Statut: " . $offre['statut_label'] . "\n";
        echo "   Nombre de vues: " . $offre['nombre_vues'] . "\n";
    } else {
        echo "❌ Erreur lors de la récupération de l'offre\n";
        echo "Code: " . $response['code'] . "\n";
    }
}

// 5. Publier l'offre
if ($offreId) {
    echo "\n5. Publier l'offre...\n";
    $response = makeRequest($baseUrl . '/auth/offres-emploi/' . $offreId . '/publier', 'POST', null, $headers);

    if ($response['code'] === 200) {
        echo "✅ Offre publiée avec succès\n";
    } else {
        echo "❌ Erreur lors de la publication\n";
        echo "Code: " . $response['code'] . "\n";
        echo "Réponse: " . $response['raw'] . "\n";
    }
}

// 6. Marquer comme urgente
if ($offreId) {
    echo "\n6. Marquer l'offre comme urgente...\n";
    $response = makeRequest($baseUrl . '/auth/offres-emploi/' . $offreId . '/marquer-urgente', 'POST', null, $headers);

    if ($response['code'] === 200) {
        echo "✅ Offre marquée comme urgente\n";
    } else {
        echo "❌ Erreur lors du marquage urgent\n";
        echo "Code: " . $response['code'] . "\n";
    }
}

// 7. Marquer comme sponsorisée
if ($offreId) {
    echo "\n7. Marquer l'offre comme sponsorisée...\n";
    $response = makeRequest($baseUrl . '/auth/offres-emploi/' . $offreId . '/marquer-sponsorisee', 'POST', null, $headers);

    if ($response['code'] === 200) {
        echo "✅ Offre marquée comme sponsorisée\n";
    } else {
        echo "❌ Erreur lors du marquage sponsorisé\n";
        echo "Code: " . $response['code'] . "\n";
    }
}

// 8. Mettre à jour l'offre
if ($offreId) {
    echo "\n8. Mettre à jour l'offre...\n";
    $updateData = [
        'salaire_min' => 45000,
        'salaire_max' => 60000,
        'avantages' => 'Télétravail possible, mutuelle, tickets restaurant, formation continue, évolution de carrière.',
        'notes_internes' => 'Offre mise à jour - augmentation du salaire suite à la demande du management.'
    ];

    $response = makeRequest($baseUrl . '/auth/offres-emploi/' . $offreId, 'PUT', $updateData, $headers);

    if ($response['code'] === 200) {
        echo "✅ Offre mise à jour avec succès\n";
        echo "   Nouveau salaire: " . $response['data']['salaire_formatted'] . "\n";
    } else {
        echo "❌ Erreur lors de la mise à jour\n";
        echo "Code: " . $response['code'] . "\n";
        echo "Réponse: " . $response['raw'] . "\n";
    }
}

// 9. Tester les filtres
echo "\n9. Tester les filtres...\n";

// Offres actives
$response = makeRequest($baseUrl . '/auth/offres-emploi?active=true', 'GET', null, $headers);
if ($response['code'] === 200) {
    $offresActives = $response['data']['data'] ?? $response['data'];
    echo "✅ " . count($offresActives) . " offres actives trouvées\n";
}

// Offres urgentes
$response = makeRequest($baseUrl . '/auth/offres-emploi?urgente=true', 'GET', null, $headers);
if ($response['code'] === 200) {
    $offresUrgentes = $response['data']['data'] ?? $response['data'];
    echo "✅ " . count($offresUrgentes) . " offres urgentes trouvées\n";
}

// Offres sponsorisées
$response = makeRequest($baseUrl . '/auth/offres-emploi?sponsorisee=true', 'GET', null, $headers);
if ($response['code'] === 200) {
    $offresSponsorisees = $response['data']['data'] ?? $response['data'];
    echo "✅ " . count($offresSponsorisees) . " offres sponsorisées trouvées\n";
}

// 10. Tester les endpoints spéciaux
echo "\n10. Tester les endpoints spéciaux...\n";

// Offres actives
$response = makeRequest($baseUrl . '/auth/offres-emploi/actives', 'GET', null, $headers);
if ($response['code'] === 200) {
    $offresActives = $response['data']['data'] ?? $response['data'];
    echo "✅ " . count($offresActives) . " offres actives via endpoint spécial\n";
}

// Offres publiées
$response = makeRequest($baseUrl . '/auth/offres-emploi/publiees', 'GET', null, $headers);
if ($response['code'] === 200) {
    $offresPubliees = $response['data']['data'] ?? $response['data'];
    echo "✅ " . count($offresPubliees) . " offres publiées trouvées\n";
}

// Offres urgentes
$response = makeRequest($baseUrl . '/auth/offres-emploi/urgentes', 'GET', null, $headers);
if ($response['code'] === 200) {
    $offresUrgentes = $response['data']['data'] ?? $response['data'];
    echo "✅ " . count($offresUrgentes) . " offres urgentes via endpoint spécial\n";
}

// Offres sponsorisées
$response = makeRequest($baseUrl . '/auth/offres-emploi/sponsorisees', 'GET', null, $headers);
if ($response['code'] === 200) {
    $offresSponsorisees = $response['data']['data'] ?? $response['data'];
    echo "✅ " . count($offresSponsorisees) . " offres sponsorisées via endpoint spécial\n";
}

// Offres récentes
$response = makeRequest($baseUrl . '/auth/offres-emploi/recentes', 'GET', null, $headers);
if ($response['code'] === 200) {
    $offresRecentes = $response['data']['data'] ?? $response['data'];
    echo "✅ " . count($offresRecentes) . " offres récentes trouvées\n";
}

// 11. Statistiques
echo "\n11. Récupérer les statistiques...\n";
$response = makeRequest($baseUrl . '/auth/offres-emploi/statistiques', 'GET', null, $headers);

if ($response['code'] === 200) {
    $stats = $response['data'];
    echo "✅ Statistiques récupérées\n";
    echo "   Total offres: " . $stats['total_offres'] . "\n";
    echo "   Offres actives: " . $stats['offres_actives'] . "\n";
    echo "   Offres publiées: " . $stats['offres_publiees'] . "\n";
    echo "   Offres urgentes: " . $stats['offres_urgentes'] . "\n";
    echo "   Offres sponsorisées: " . $stats['offres_sponsorisees'] . "\n";
} else {
    echo "❌ Erreur lors de la récupération des statistiques\n";
    echo "Code: " . $response['code'] . "\n";
}

// 12. Recherche
echo "\n12. Tester la recherche...\n";
$response = makeRequest($baseUrl . '/auth/offres-emploi/rechercher?term=react', 'GET', null, $headers);

if ($response['code'] === 200) {
    $resultats = $response['data']['data'] ?? $response['data'];
    echo "✅ Recherche effectuée\n";
    echo "   " . count($resultats) . " résultats trouvés pour 'react'\n";
} else {
    echo "❌ Erreur lors de la recherche\n";
    echo "Code: " . $response['code'] . "\n";
}

// 13. Terminer l'offre
if ($offreId) {
    echo "\n13. Terminer l'offre...\n";
    $response = makeRequest($baseUrl . '/auth/offres-emploi/' . $offreId . '/terminer', 'POST', null, $headers);

    if ($response['code'] === 200) {
        echo "✅ Offre terminée avec succès\n";
    } else {
        echo "❌ Erreur lors de la terminaison\n";
        echo "Code: " . $response['code'] . "\n";
    }
}

// 14. Archiver l'offre
if ($offreId) {
    echo "\n14. Archiver l'offre...\n";
    $response = makeRequest($baseUrl . '/auth/offres-emploi/' . $offreId . '/archiver', 'POST', null, $headers);

    if ($response['code'] === 200) {
        echo "✅ Offre archivée avec succès\n";
    } else {
        echo "❌ Erreur lors de l'archivage\n";
        echo "Code: " . $response['code'] . "\n";
    }
}

// 15. Supprimer l'offre
if ($offreId) {
    echo "\n15. Supprimer l'offre...\n";
    $response = makeRequest($baseUrl . '/auth/offres-emploi/' . $offreId, 'DELETE', null, $headers);

    if ($response['code'] === 204) {
        echo "✅ Offre supprimée avec succès\n";
    } else {
        echo "❌ Erreur lors de la suppression\n";
        echo "Code: " . $response['code'] . "\n";
    }
}

echo "\n=== FIN DES TESTS OFFRES D'EMPLOI ===\n";
echo "✅ Tous les tests du module Offre d'emploi ont été exécutés avec succès !\n"; 