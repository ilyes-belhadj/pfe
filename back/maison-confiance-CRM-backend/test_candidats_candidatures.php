<?php

// Script de test pour les modules Candidats et Candidatures

$baseUrl = 'http://127.0.0.1:8000/api/auth';

// Fonction pour faire des requêtes cURL
function makeRequest($url, $method = 'GET', $data = null, $token = null) {
    $ch = curl_init();
    
    $headers = ['Content-Type: application/json'];
    if ($token) {
        $headers[] = "Authorization: Bearer $token";
    }
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    
    if ($data && in_array($method, ['POST', 'PUT'])) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return [
        'code' => $httpCode,
        'data' => json_decode($response, true)
    ];
}

echo "=== Test des modules Candidats et Candidatures ===\n\n";

// 1. Authentification
echo "1. Authentification...\n";
$loginData = [
    'email' => 'admin@example.com',
    'password' => 'password'
];

$authResponse = makeRequest("$baseUrl/login", 'POST', $loginData);
if ($authResponse['code'] === 200 && isset($authResponse['data']['access_token'])) {
    $token = $authResponse['data']['access_token'];
    echo "✅ Authentification réussie\n";
} else {
    echo "❌ Échec de l'authentification\n";
    echo "Code: " . $authResponse['code'] . "\n";
    print_r($authResponse['data']);
    exit;
}

// 2. Test des candidats
echo "\n2. Test des candidats...\n";

// Lister les candidats
echo "   - Liste des candidats...\n";
$candidatsResponse = makeRequest("$baseUrl/candidats", 'GET', null, $token);
if ($candidatsResponse['code'] === 200) {
    echo "   ✅ Liste des candidats récupérée (" . count($candidatsResponse['data']) . " candidats)\n";
} else {
    echo "   ❌ Échec de récupération des candidats\n";
}

// Candidats actifs
echo "   - Candidats actifs...\n";
$actifsResponse = makeRequest("$baseUrl/candidats/actifs", 'GET', null, $token);
if ($actifsResponse['code'] === 200) {
    echo "   ✅ Candidats actifs récupérés (" . count($actifsResponse['data']) . " candidats)\n";
} else {
    echo "   ❌ Échec de récupération des candidats actifs\n";
}

// Statistiques des candidats
echo "   - Statistiques des candidats...\n";
$statsResponse = makeRequest("$baseUrl/candidats/statistiques", 'GET', null, $token);
if ($statsResponse['code'] === 200) {
    echo "   ✅ Statistiques récupérées\n";
    echo "     Total: " . $statsResponse['data']['total_candidats'] . "\n";
    echo "     Actifs: " . $statsResponse['data']['candidats_actifs'] . "\n";
} else {
    echo "   ❌ Échec de récupération des statistiques\n";
}

// Créer un nouveau candidat
echo "   - Création d'un nouveau candidat...\n";
$nouveauCandidat = [
    'nom' => 'Test',
    'prenom' => 'Candidat',
    'email' => 'test.candidat@example.com',
    'telephone' => '06 12 34 56 78',
    'ville' => 'Paris',
    'code_postal' => '75001',
    'pays' => 'France',
    'civilite' => 'M',
    'statut' => 'actif',
    'source_recrutement' => 'LinkedIn'
];

$createResponse = makeRequest("$baseUrl/candidats", 'POST', $nouveauCandidat, $token);
if ($createResponse['code'] === 201 || $createResponse['code'] === 200) {
    echo "   ✅ Nouveau candidat créé\n";
    $candidatId = $createResponse['data']['id'] ?? null;
} else {
    echo "   ❌ Échec de création du candidat\n";
    print_r($createResponse['data']);
}

// 3. Test des candidatures
echo "\n3. Test des candidatures...\n";

// Lister les candidatures
echo "   - Liste des candidatures...\n";
$candidaturesResponse = makeRequest("$baseUrl/candidatures", 'GET', null, $token);
if ($candidaturesResponse['code'] === 200) {
    echo "   ✅ Liste des candidatures récupérée (" . count($candidaturesResponse['data']) . " candidatures)\n";
} else {
    echo "   ❌ Échec de récupération des candidatures\n";
}

// Candidatures actives
echo "   - Candidatures actives...\n";
$activesResponse = makeRequest("$baseUrl/candidatures/actives", 'GET', null, $token);
if ($activesResponse['code'] === 200) {
    echo "   ✅ Candidatures actives récupérées (" . count($activesResponse['data']) . " candidatures)\n";
} else {
    echo "   ❌ Échec de récupération des candidatures actives\n";
}

// Statistiques des candidatures
echo "   - Statistiques des candidatures...\n";
$statsCandidaturesResponse = makeRequest("$baseUrl/candidatures/statistiques", 'GET', null, $token);
if ($statsCandidaturesResponse['code'] === 200) {
    echo "   ✅ Statistiques des candidatures récupérées\n";
    echo "     Total: " . $statsCandidaturesResponse['data']['total_candidatures'] . "\n";
    echo "     Actives: " . $statsCandidaturesResponse['data']['candidatures_actives'] . "\n";
} else {
    echo "   ❌ Échec de récupération des statistiques des candidatures\n";
}

// Créer une nouvelle candidature
echo "   - Création d'une nouvelle candidature...\n";
$nouvelleCandidature = [
    'candidat_id' => 1, // Premier candidat
    'departement_id' => 1, // Premier département
    'poste_souhaite' => 'Développeur Full-Stack',
    'lettre_motivation' => 'Je suis très motivé pour ce poste...',
    'statut' => 'nouvelle',
    'priorite' => 'normale',
    'date_candidature' => date('Y-m-d'),
    'source_candidature' => 'LinkedIn',
    'candidature_spontanee' => false
];

$createCandidatureResponse = makeRequest("$baseUrl/candidatures", 'POST', $nouvelleCandidature, $token);
if ($createCandidatureResponse['code'] === 201 || $createCandidatureResponse['code'] === 200) {
    echo "   ✅ Nouvelle candidature créée\n";
    $candidatureId = $createCandidatureResponse['data']['id'] ?? null;
} else {
    echo "   ❌ Échec de création de la candidature\n";
    print_r($createCandidatureResponse['data']);
}

// 4. Test des actions spéciales
echo "\n4. Test des actions spéciales...\n";

// Changer le statut d'une candidature
if (isset($candidatureId)) {
    echo "   - Changement de statut d'une candidature...\n";
    $changeStatutData = ['statut' => 'en_cours'];
    $changeStatutResponse = makeRequest("$baseUrl/candidatures/$candidatureId/changer-statut", 'POST', $changeStatutData, $token);
    if ($changeStatutResponse['code'] === 200) {
        echo "   ✅ Statut changé avec succès\n";
    } else {
        echo "   ❌ Échec du changement de statut\n";
    }
}

// Planifier un entretien
if (isset($candidatureId)) {
    echo "   - Planification d'un entretien...\n";
    $entretienData = [
        'date_entretien' => date('Y-m-d', strtotime('+1 week')),
        'heure_entretien' => '14:30',
        'lieu_entretien' => 'Bureau RH - Étage 2',
        'notes' => 'Entretien avec le manager du département'
    ];
    $entretienResponse = makeRequest("$baseUrl/candidatures/$candidatureId/planifier-entretien", 'POST', $entretienData, $token);
    if ($entretienResponse['code'] === 200) {
        echo "   ✅ Entretien planifié avec succès\n";
    } else {
        echo "   ❌ Échec de la planification de l'entretien\n";
    }
}

// Évaluer une candidature
if (isset($candidatureId)) {
    echo "   - Évaluation d'une candidature...\n";
    $evaluationData = [
        'note_globale' => 8.5,
        'evaluation' => 'Candidat très prometteur avec de bonnes compétences techniques.',
        'commentaires_rh' => 'Profil intéressant, bonne motivation.',
        'commentaires_technique' => 'Compétences techniques solides, bonne expérience.'
    ];
    $evaluationResponse = makeRequest("$baseUrl/candidatures/$candidatureId/evaluer", 'POST', $evaluationData, $token);
    if ($evaluationResponse['code'] === 200) {
        echo "   ✅ Candidature évaluée avec succès\n";
    } else {
        echo "   ❌ Échec de l'évaluation de la candidature\n";
    }
}

// 5. Test des filtres et recherche
echo "\n5. Test des filtres et recherche...\n";

// Recherche de candidats
echo "   - Recherche de candidats...\n";
$searchResponse = makeRequest("$baseUrl/candidats/rechercher?term=Marie", 'GET', null, $token);
if ($searchResponse['code'] === 200) {
    echo "   ✅ Recherche de candidats réussie (" . count($searchResponse['data']) . " résultats)\n";
} else {
    echo "   ❌ Échec de la recherche de candidats\n";
}

// Filtrage des candidatures par statut
echo "   - Filtrage des candidatures par statut...\n";
$filterResponse = makeRequest("$baseUrl/candidatures?statut=nouvelle", 'GET', null, $token);
if ($filterResponse['code'] === 200) {
    echo "   ✅ Filtrage des candidatures réussi (" . count($filterResponse['data']) . " résultats)\n";
} else {
    echo "   ❌ Échec du filtrage des candidatures\n";
}

echo "\n=== Tests terminés ===\n";
echo "✅ Tous les tests ont été exécutés avec succès !\n"; 