<?php

echo "=== Test du module Candidature ===\n\n";

$baseUrl = 'http://localhost:8000/api/auth';

// 1. Authentification
echo "1. Authentification...\n";
$authData = json_encode([
    'email' => 'admin@maison-confiance.com',
    'password' => 'password123'
]);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/api/login');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $authData);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    $authResult = json_decode($response, true);
    $token = $authResult['access_token'] ?? null;
    if ($token) {
        echo "✅ Authentification réussie\n";
    } else {
        echo "❌ Token non trouvé dans la réponse\n";
        exit;
    }
} else {
    echo "❌ Échec de l'authentification: HTTP $httpCode\n";
    echo "Réponse: $response\n";
    exit;
}

// Headers pour les requêtes authentifiées
$headers = [
    'Authorization: Bearer ' . $token,
    'Accept: application/json',
    'Content-Type: application/json'
];

// 2. Test des candidatures
echo "\n2. Test des endpoints Candidature...\n";

// 2.1 Liste des candidatures
echo "\n2.1. Liste des candidatures...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/candidatures');
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    $candidatures = json_decode($response, true);
    echo "✅ Liste des candidatures récupérée (" . count($candidatures) . " candidatures)\n";
} else {
    echo "❌ Erreur lors de la récupération des candidatures: HTTP $httpCode\n";
    echo "Réponse: $response\n";
}

// 2.2 Candidatures actives
echo "\n2.2. Candidatures actives...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/candidatures/actives');
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    $candidatures = json_decode($response, true);
    echo "✅ Candidatures actives récupérées (" . count($candidatures) . " candidatures)\n";
} else {
    echo "❌ Erreur lors de la récupération des candidatures actives: HTTP $httpCode\n";
    echo "Réponse: $response\n";
}

// 2.3 Candidatures récentes
echo "\n2.3. Candidatures récentes...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/candidatures/recents');
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    $candidatures = json_decode($response, true);
    echo "✅ Candidatures récentes récupérées (" . count($candidatures) . " candidatures)\n";
} else {
    echo "❌ Erreur lors de la récupération des candidatures récentes: HTTP $httpCode\n";
    echo "Réponse: $response\n";
}

// 2.4 Candidatures spontanées
echo "\n2.4. Candidatures spontanées...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/candidatures/spontanees');
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    $candidatures = json_decode($response, true);
    echo "✅ Candidatures spontanées récupérées (" . count($candidatures) . " candidatures)\n";
} else {
    echo "❌ Erreur lors de la récupération des candidatures spontanées: HTTP $httpCode\n";
    echo "Réponse: $response\n";
}

// 2.5 Statistiques des candidatures
echo "\n2.5. Statistiques des candidatures...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/candidatures/statistiques');
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    $stats = json_decode($response, true);
    echo "✅ Statistiques récupérées\n";
    echo "   - Total: " . ($stats['total'] ?? 'N/A') . "\n";
    echo "   - Actives: " . ($stats['actives'] ?? 'N/A') . "\n";
    echo "   - Embauchées: " . ($stats['embauchees'] ?? 'N/A') . "\n";
    echo "   - Refusées: " . ($stats['refusees'] ?? 'N/A') . "\n";
} else {
    echo "❌ Erreur lors de la récupération des statistiques: HTTP $httpCode\n";
    echo "Réponse: $response\n";
}

// 2.6 Recherche de candidatures
echo "\n2.6. Recherche de candidatures...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/candidatures/rechercher?q=développeur');
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    $candidatures = json_decode($response, true);
    echo "✅ Recherche effectuée (" . count($candidatures) . " résultats)\n";
} else {
    echo "❌ Erreur lors de la recherche: HTTP $httpCode\n";
    echo "Réponse: $response\n";
}

// 3. Test des candidats
echo "\n3. Test des endpoints Candidat...\n";

// 3.1 Liste des candidats
echo "\n3.1. Liste des candidats...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/candidats');
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    $candidats = json_decode($response, true);
    echo "✅ Liste des candidats récupérée (" . count($candidats) . " candidats)\n";
} else {
    echo "❌ Erreur lors de la récupération des candidats: HTTP $httpCode\n";
    echo "Réponse: $response\n";
}

// 3.2 Candidats actifs
echo "\n3.2. Candidats actifs...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/candidats/actifs');
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    $candidats = json_decode($response, true);
    echo "✅ Candidats actifs récupérés (" . count($candidats) . " candidats)\n";
} else {
    echo "❌ Erreur lors de la récupération des candidats actifs: HTTP $httpCode\n";
    echo "Réponse: $response\n";
}

// 3.3 Statistiques des candidats
echo "\n3.3. Statistiques des candidats...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/candidats/statistiques');
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    $stats = json_decode($response, true);
    echo "✅ Statistiques des candidats récupérées\n";
} else {
    echo "❌ Erreur lors de la récupération des statistiques: HTTP $httpCode\n";
    echo "Réponse: $response\n";
}

echo "\n=== Test du module Candidature terminé ===\n"; 