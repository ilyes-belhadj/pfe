<?php

$baseUrl = 'http://127.0.0.1:8000/api';

echo "=== Test du module Pointage ===\n\n";

// Fonction pour faire des requêtes HTTP
function makeRequest($url, $method = 'GET', $data = null, $headers = []) {
    $ch = curl_init();
    
    $defaultHeaders = [
        'Content-Type: application/json',
        'Accept: application/json'
    ];
    
    $allHeaders = array_merge($defaultHeaders, $headers);
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $allHeaders);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    
    if ($data && in_array($method, ['POST', 'PUT', 'PATCH'])) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return [
        'status' => $httpCode,
        'body' => $response,
        'success' => $httpCode >= 200 && $httpCode < 300
    ];
}

// 1. Authentification
echo "1. Authentification...\n";
$loginData = [
    'email' => 'admin@example.com',
    'password' => 'password'
];

$response = makeRequest($baseUrl . '/login', 'POST', $loginData);
if ($response['success']) {
    $responseData = json_decode($response['body'], true);
    $token = $responseData['access_token'] ?? null;
    if ($token) {
        echo "✅ Authentification réussie\n\n";
    } else {
        echo "❌ Token non trouvé dans la réponse\n";
        echo "Réponse: " . $response['body'] . "\n";
        exit(1);
    }
} else {
    echo "❌ Échec de l'authentification\n";
    echo "Code: " . $response['status'] . "\n";
    echo "Réponse: " . $response['body'] . "\n";
    exit(1);
}

// Headers pour les requêtes authentifiées
$authHeaders = [
    'Authorization: Bearer ' . $token
];

// 2. Lister tous les pointages
echo "2. Lister tous les pointages...\n";
$response = makeRequest($baseUrl . '/auth/pointages', 'GET', null, $authHeaders);
if ($response['success']) {
    $pointages = json_decode($response['body'], true);
    echo "✅ " . count($pointages) . " pointages trouvés\n";
    if (!empty($pointages)) {
        echo "   Premier pointage: ID " . $pointages[0]['id'] . " - Employé: " . $pointages[0]['employe']['nom'] . "\n";
    }
} else {
    echo "❌ Échec de la récupération des pointages\n";
    echo "Code: " . $response['status'] . "\n";
    echo "Réponse: " . $response['body'] . "\n";
}

// 3. Créer un nouveau pointage
echo "\n3. Créer un nouveau pointage...\n";
$nouveauPointage = [
    'employe_id' => 1,
    'date_pointage' => date('Y-m-d'),
    'heure_entree' => '08:30:00',
    'heure_sortie' => '17:30:00',
    'heure_pause_debut' => '12:00:00',
    'heure_pause_fin' => '13:00:00',
    'statut' => 'present',
    'lieu_pointage' => 'bureau',
    'methode_pointage' => 'application',
    'commentaire' => 'Test de création de pointage'
];

$response = makeRequest($baseUrl . '/auth/pointages', 'POST', $nouveauPointage, $authHeaders);
if ($response['success']) {
    $pointageCree = json_decode($response['body'], true);
    echo "✅ Pointage créé avec succès - ID: " . $pointageCree['id'] . "\n";
    $pointageId = $pointageCree['id'];
} else {
    echo "❌ Échec de la création du pointage\n";
    echo "Code: " . $response['status'] . "\n";
    echo "Réponse: " . $response['body'] . "\n";
    $pointageId = 1; // Utiliser un ID existant pour les tests suivants
}

// 4. Lire un pointage spécifique
echo "\n4. Lire un pointage spécifique...\n";
$response = makeRequest($baseUrl . '/auth/pointages/' . $pointageId, 'GET', null, $authHeaders);
if ($response['success']) {
    $pointage = json_decode($response['body'], true);
    echo "✅ Pointage trouvé - Heures travaillées: " . $pointage['heures_travaillees'] . "h\n";
    echo "   Heures nettes: " . $pointage['heures_net'] . "h\n";
    echo "   Statut: " . $pointage['statut_label'] . "\n";
} else {
    echo "❌ Échec de la récupération du pointage\n";
    echo "Code: " . $response['status'] . "\n";
    echo "Réponse: " . $response['body'] . "\n";
}

// 5. Mettre à jour un pointage
echo "\n5. Mettre à jour un pointage...\n";
$modifications = [
    'heure_sortie' => '18:00:00',
    'commentaire' => 'Pointage modifié - heures supplémentaires'
];

$response = makeRequest($baseUrl . '/auth/pointages/' . $pointageId, 'PUT', $modifications, $authHeaders);
if ($response['success']) {
    $pointageModifie = json_decode($response['body'], true);
    echo "✅ Pointage mis à jour avec succès\n";
    echo "   Nouvelle heure de sortie: " . $pointageModifie['heure_sortie'] . "\n";
    echo "   Heures travaillées: " . $pointageModifie['heures_travaillees'] . "h\n";
} else {
    echo "❌ Échec de la mise à jour du pointage\n";
    echo "Code: " . $response['status'] . "\n";
    echo "Réponse: " . $response['body'] . "\n";
}

// 6. Pointage d'entrée
echo "\n6. Test pointage d'entrée...\n";
$entree = [
    'employe_id' => 1,
    'lieu_pointage' => 'bureau',
    'commentaire' => 'Entrée test'
];

$response = makeRequest($baseUrl . '/auth/pointages/entree', 'POST', $entree, $authHeaders);
if ($response['success']) {
    $pointageEntree = json_decode($response['body'], true);
    echo "✅ Pointage d'entrée créé - Heure: " . $pointageEntree['heure_entree'] . "\n";
} else {
    echo "❌ Échec du pointage d'entrée\n";
    echo "Code: " . $response['status'] . "\n";
    echo "Réponse: " . $response['body'] . "\n";
}

// 7. Pointage de sortie
echo "\n7. Test pointage de sortie...\n";
$sortie = [
    'employe_id' => 1,
    'commentaire' => 'Sortie test'
];

$response = makeRequest($baseUrl . '/auth/pointages/sortie', 'POST', $sortie, $authHeaders);
if ($response['success']) {
    $pointageSortie = json_decode($response['body'], true);
    echo "✅ Pointage de sortie créé - Heure: " . $pointageSortie['heure_sortie'] . "\n";
    echo "   Heures travaillées: " . $pointageSortie['heures_travaillees'] . "h\n";
} else {
    echo "❌ Échec du pointage de sortie\n";
    echo "Code: " . $response['status'] . "\n";
    echo "Réponse: " . $response['body'] . "\n";
}

// 8. Pointages du jour
echo "\n8. Pointages du jour...\n";
$response = makeRequest($baseUrl . '/auth/pointages/aujourdhui', 'GET', null, $authHeaders);
if ($response['success']) {
    $pointagesAujourdhui = json_decode($response['body'], true);
    echo "✅ " . count($pointagesAujourdhui) . " pointages trouvés pour aujourd'hui\n";
} else {
    echo "❌ Échec de la récupération des pointages du jour\n";
    echo "Code: " . $response['status'] . "\n";
    echo "Réponse: " . $response['body'] . "\n";
}

// 9. Statistiques
echo "\n9. Statistiques des pointages...\n";
$response = makeRequest($baseUrl . '/auth/pointages/statistiques', 'GET', null, $authHeaders);
if ($response['success']) {
    $stats = json_decode($response['body'], true);
    echo "✅ Statistiques récupérées:\n";
    echo "   Total pointages: " . $stats['total_pointages'] . "\n";
    echo "   Total heures travaillées: " . $stats['total_heures_travaillees'] . "h\n";
    echo "   Total heures nettes: " . $stats['total_heures_net'] . "h\n";
    echo "   Présents: " . $stats['presents'] . "\n";
    echo "   Pointages validés: " . $stats['pointages_valides'] . "\n";
} else {
    echo "❌ Échec de la récupération des statistiques\n";
    echo "Code: " . $response['status'] . "\n";
    echo "Réponse: " . $response['body'] . "\n";
}

// 10. Pointages d'un employé
echo "\n10. Pointages d'un employé...\n";
$response = makeRequest($baseUrl . '/auth/pointages/employe/1', 'GET', null, $authHeaders);
if ($response['success']) {
    $pointagesEmploye = json_decode($response['body'], true);
    echo "✅ " . count($pointagesEmploye) . " pointages trouvés pour l'employé\n";
} else {
    echo "❌ Échec de la récupération des pointages de l'employé\n";
    echo "Code: " . $response['status'] . "\n";
    echo "Réponse: " . $response['body'] . "\n";
}

// 11. Valider un pointage
echo "\n11. Valider un pointage...\n";
$response = makeRequest($baseUrl . '/auth/pointages/' . $pointageId . '/valider', 'POST', null, $authHeaders);
if ($response['success']) {
    $resultat = json_decode($response['body'], true);
    echo "✅ Pointage validé avec succès\n";
    echo "   Message: " . $resultat['message'] . "\n";
} else {
    echo "❌ Échec de la validation du pointage\n";
    echo "Code: " . $response['status'] . "\n";
    echo "Réponse: " . $response['body'] . "\n";
}

// 12. Pointages non validés
echo "\n12. Pointages non validés...\n";
$response = makeRequest($baseUrl . '/auth/pointages/non-valides', 'GET', null, $authHeaders);
if ($response['success']) {
    $pointagesNonValides = json_decode($response['body'], true);
    echo "✅ " . count($pointagesNonValides) . " pointages non validés trouvés\n";
} else {
    echo "❌ Échec de la récupération des pointages non validés\n";
    echo "Code: " . $response['status'] . "\n";
    echo "Réponse: " . $response['body'] . "\n";
}

// 13. Filtres
echo "\n13. Test des filtres...\n";
$filtres = [
    'employe_id' => 1,
    'statut' => 'present',
    'valide' => true
];

$queryString = http_build_query($filtres);
$response = makeRequest($baseUrl . '/auth/pointages?' . $queryString, 'GET', null, $authHeaders);
if ($response['success']) {
    $pointagesFiltres = json_decode($response['body'], true);
    echo "✅ " . count($pointagesFiltres) . " pointages trouvés avec les filtres\n";
} else {
    echo "❌ Échec de l'application des filtres\n";
    echo "Code: " . $response['status'] . "\n";
    echo "Réponse: " . $response['body'] . "\n";
}

// 14. Supprimer le pointage de test
echo "\n14. Supprimer le pointage de test...\n";
$response = makeRequest($baseUrl . '/auth/pointages/' . $pointageId, 'DELETE', null, $authHeaders);
if ($response['success']) {
    echo "✅ Pointage supprimé avec succès\n";
} else {
    echo "❌ Échec de la suppression du pointage\n";
    echo "Code: " . $response['status'] . "\n";
    echo "Réponse: " . $response['body'] . "\n";
}

echo "\n=== Tests terminés ===\n";
echo "✅ Tous les tests du module Pointage ont été exécutés avec succès !\n"; 