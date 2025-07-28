<?php

// Configuration
$baseUrl = 'http://localhost:8000/api';
$adminEmail = 'admin@maison-confiance.com';
$adminPassword = 'password123';

echo "=== TEST COMPLET DE TOUS LES ENDPOINTS CRM ===\n\n";

// Fonction pour faire des requêtes HTTP
function makeRequest($url, $method = 'GET', $data = null, $headers = []) {
    $ch = curl_init();
    
    $defaultHeaders = [
        'Content-Type: application/json',
        'Accept: application/json'
    ];
    
    $headers = array_merge($defaultHeaders, $headers);
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
    } elseif ($method === 'PUT') {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
    } elseif ($method === 'DELETE') {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return [
        'status' => $httpCode,
        'body' => $response,
        'data' => json_decode($response, true)
    ];
}

// 1. Authentification
echo "1. Authentification...\n";
$loginResponse = makeRequest($baseUrl . '/login', 'POST', [
    'email' => $adminEmail,
    'password' => $adminPassword
]);

if ($loginResponse['status'] === 200) {
    // Gérer différentes structures de réponse possibles
    $responseData = $loginResponse['data'];
    $token = null;
    
    if (isset($responseData['data']['token'])) {
        $token = $responseData['data']['token'];
    } elseif (isset($responseData['token'])) {
        $token = $responseData['token'];
    } elseif (isset($responseData['access_token'])) {
        $token = $responseData['access_token'];
    }
    
    if ($token) {
        echo "✅ Authentification réussie\n\n";
    } else {
        echo "❌ Token non trouvé dans la réponse: " . json_encode($responseData) . "\n";
        exit(1);
    }
} else {
    echo "❌ Échec de l'authentification: " . $loginResponse['body'] . "\n";
    exit(1);
}

$headers = [
    'Authorization: Bearer ' . $token,
    'Accept: application/json'
];

// 2. Test de tous les endpoints
echo "2. Test de tous les endpoints...\n\n";

$results = [];
$successCount = 0;
$errorCount = 0;

// Dashboard
echo "=== DASHBOARD ===\n";
$response = makeRequest($baseUrl . '/auth/dashboard', 'GET', null, $headers);
$status = $response['status'] === 200 ? '✅' : '❌';
echo "$status GET /auth/dashboard => HTTP {$response['status']}\n";
if ($response['status'] === 200) $successCount++; else $errorCount++;

// Users
echo "\n=== USERS ===\n";
$endpoints = [
    ['GET', '/auth/users'],
    ['GET', '/auth/users/all'],
    ['POST', '/auth/users', [
        'nom' => 'Test',
        'prenom' => 'User',
        'email' => 'test@example.com',
        'password' => 'password123',
        'role_id' => 1
    ]],
    ['POST', '/auth/users/onToggleChangetat', ['id' => 1]],
    ['POST', '/auth/users/verifyUserExistantt', ['email' => 'test@example.com']]
];

foreach ($endpoints as $endpoint) {
    $method = $endpoint[0];
    $url = $baseUrl . $endpoint[1];
    $data = $endpoint[2] ?? null;
    
    $response = makeRequest($url, $method, $data, $headers);
    $status = in_array($response['status'], [200, 201, 422]) ? '✅' : '❌';
    echo "$status $method $endpoint[1] => HTTP {$response['status']}\n";
    if (in_array($response['status'], [200, 201])) $successCount++; else $errorCount++;
}

// Roles
echo "\n=== ROLES ===\n";
$endpoints = [
    ['GET', '/auth/roles'],
    ['POST', '/auth/roles', [
        'nom' => 'Test Role',
        'description' => 'Role de test'
    ]]
];

foreach ($endpoints as $endpoint) {
    $method = $endpoint[0];
    $url = $baseUrl . $endpoint[1];
    $data = $endpoint[2] ?? null;
    
    $response = makeRequest($url, $method, $data, $headers);
    $status = in_array($response['status'], [200, 201, 422]) ? '✅' : '❌';
    echo "$status $method $endpoint[1] => HTTP {$response['status']}\n";
    if (in_array($response['status'], [200, 201])) $successCount++; else $errorCount++;
}

// Absences
echo "\n=== ABSENCES ===\n";
$endpoints = [
    ['GET', '/auth/absences'],
    ['POST', '/auth/absences', [
        'employe_id' => 1,
        'date_debut' => '2024-01-15',
        'date_fin' => '2024-01-16',
        'motif' => 'Congé maladie'
    ]]
];

foreach ($endpoints as $endpoint) {
    $method = $endpoint[0];
    $url = $baseUrl . $endpoint[1];
    $data = $endpoint[2] ?? null;
    
    $response = makeRequest($url, $method, $data, $headers);
    $status = in_array($response['status'], [200, 201, 422]) ? '✅' : '❌';
    echo "$status $method $endpoint[1] => HTTP {$response['status']}\n";
    if (in_array($response['status'], [200, 201])) $successCount++; else $errorCount++;
}

// Formations
echo "\n=== FORMATIONS ===\n";
$endpoints = [
    ['GET', '/auth/formations'],
    ['GET', '/auth/formations/disponibles'],
    ['POST', '/auth/formations', [
        'titre' => 'Formation Test',
        'description' => 'Description de test',
        'formateur' => 'Formateur Test',
        'date_debut' => '2024-02-01',
        'date_fin' => '2024-02-02',
        'duree_heures' => 8,
        'cout' => 500.00,
        'statut' => 'planifie',
        'lieu' => 'Salle de formation',
        'nombre_places' => 20,
        'places_occupees' => 0
    ]]
];

foreach ($endpoints as $endpoint) {
    $method = $endpoint[0];
    $url = $baseUrl . $endpoint[1];
    $data = $endpoint[2] ?? null;
    
    $response = makeRequest($url, $method, $data, $headers);
    $status = in_array($response['status'], [200, 201, 422]) ? '✅' : '❌';
    echo "$status $method $endpoint[1] => HTTP {$response['status']}\n";
    if (in_array($response['status'], [200, 201])) $successCount++; else $errorCount++;
}

// Projects
echo "\n=== PROJECTS ===\n";
$endpoints = [
    ['GET', '/auth/projects'],
    ['GET', '/auth/projects/all'],
    ['GET', '/auth/projects/all/kanban'],
    ['POST', '/auth/projects', [
        'nom' => 'Projet Test',
        'description' => 'Description du projet test',
        'date_debut' => '2024-01-01',
        'date_fin' => '2024-12-31',
        'status' => 'en_cours',
        'priorite' => 'moyenne',
        'budget' => 10000.00
    ]]
];

foreach ($endpoints as $endpoint) {
    $method = $endpoint[0];
    $url = $baseUrl . $endpoint[1];
    $data = $endpoint[2] ?? null;
    
    $response = makeRequest($url, $method, $data, $headers);
    $status = in_array($response['status'], [200, 201, 422]) ? '✅' : '❌';
    echo "$status $method $endpoint[1] => HTTP {$response['status']}\n";
    if (in_array($response['status'], [200, 201])) $successCount++; else $errorCount++;
}

// Departements
echo "\n=== DEPARTEMENTS ===\n";
$endpoints = [
    ['GET', '/auth/departements'],
    ['GET', '/auth/departements/all'],
    ['GET', '/auth/departements/all/kanban'],
    ['POST', '/auth/departements', [
        'nom' => 'Département Test',
        'description' => 'Description du département test',
        'responsable' => 'Responsable Test',
        'budget' => 50000.00
    ]]
];

foreach ($endpoints as $endpoint) {
    $method = $endpoint[0];
    $url = $baseUrl . $endpoint[1];
    $data = $endpoint[2] ?? null;
    
    $response = makeRequest($url, $method, $data, $headers);
    $status = in_array($response['status'], [200, 201, 422]) ? '✅' : '❌';
    echo "$status $method $endpoint[1] => HTTP {$response['status']}\n";
    if (in_array($response['status'], [200, 201])) $successCount++; else $errorCount++;
}

// Employes
echo "\n=== EMPLOYES ===\n";
$endpoints = [
    ['GET', '/auth/employes'],
    ['GET', '/auth/employes/all'],
    ['GET', '/auth/employes/all/kanban'],
    ['POST', '/auth/employes', [
        'nom' => 'Employé',
        'prenom' => 'Test',
        'email' => 'employe.test@example.com',
        'date_embauche' => '2024-01-01',
        'salaire' => 3000.00,
        'departement_id' => 1
    ]]
];

foreach ($endpoints as $endpoint) {
    $method = $endpoint[0];
    $url = $baseUrl . $endpoint[1];
    $data = $endpoint[2] ?? null;
    
    $response = makeRequest($url, $method, $data, $headers);
    $status = in_array($response['status'], [200, 201, 422]) ? '✅' : '❌';
    echo "$status $method $endpoint[1] => HTTP {$response['status']}\n";
    if (in_array($response['status'], [200, 201])) $successCount++; else $errorCount++;
}

// Paies
echo "\n=== PAIES ===\n";
$endpoints = [
    ['GET', '/auth/paies'],
    ['GET', '/auth/paies/en-attente'],
    ['GET', '/auth/paies/payees'],
    ['GET', '/auth/paies/statistiques'],
    ['POST', '/auth/paies', [
        'employe_id' => 1,
        'periode' => '2024-01',
        'date_paiement' => '2024-01-31',
        'salaire_base' => 3000.00,
        'heures_travaillees' => 160,
        'taux_horaire' => 18.75,
        'salaire_brut' => 3000.00,
        'primes' => 200.00,
        'deductions' => 0.00,
        'cotisations_sociales' => 450.00,
        'impots' => 300.00,
        'salaire_net' => 2450.00,
        'statut' => 'en_attente',
        'mode_paiement' => 'virement'
    ]]
];

foreach ($endpoints as $endpoint) {
    $method = $endpoint[0];
    $url = $baseUrl . $endpoint[1];
    $data = $endpoint[2] ?? null;
    
    $response = makeRequest($url, $method, $data, $headers);
    $status = in_array($response['status'], [200, 201, 422]) ? '✅' : '❌';
    echo "$status $method $endpoint[1] => HTTP {$response['status']}\n";
    if (in_array($response['status'], [200, 201])) $successCount++; else $errorCount++;
}

// Pointages
echo "\n=== POINTAGES ===\n";
$endpoints = [
    ['GET', '/auth/pointages'],
    ['GET', '/auth/pointages/aujourdhui'],
    ['GET', '/auth/pointages/non-valides'],
    ['GET', '/auth/pointages/statistiques'],
    ['GET', '/auth/pointages/actuel'],
    ['POST', '/auth/pointages', [
        'employe_id' => 1,
        'date_pointage' => '2024-01-15',
        'heure_entree' => '09:00:00',
        'heure_sortie' => '17:00:00',
        'heures_travaillees' => 8.00,
        'heures_pause' => 1.00,
        'heures_net' => 7.00,
        'statut' => 'present',
        'lieu_pointage' => 'bureau'
    ]],
    ['POST', '/auth/pointages/entree', [
        'employe_id' => 1,
        'lieu_pointage' => 'bureau'
    ]],
    ['POST', '/auth/pointages/sortie', [
        'employe_id' => 1
    ]]
];

foreach ($endpoints as $endpoint) {
    $method = $endpoint[0];
    $url = $baseUrl . $endpoint[1];
    $data = $endpoint[2] ?? null;
    
    $response = makeRequest($url, $method, $data, $headers);
    $status = in_array($response['status'], [200, 201, 422]) ? '✅' : '❌';
    echo "$status $method $endpoint[1] => HTTP {$response['status']}\n";
    if (in_array($response['status'], [200, 201])) $successCount++; else $errorCount++;
}

// Candidats
echo "\n=== CANDIDATS ===\n";
$endpoints = [
    ['GET', '/auth/candidats'],
    ['GET', '/auth/candidats/actifs'],
    ['GET', '/auth/candidats/recents'],
    ['GET', '/auth/candidats/rechercher'],
    ['GET', '/auth/candidats/statistiques'],
    ['POST', '/auth/candidats', [
        'nom' => 'Candidat',
        'prenom' => 'Test',
        'email' => 'candidat.test@example.com',
        'telephone' => '0123456789',
        'adresse' => '123 Rue Test',
        'ville' => 'Paris',
        'code_postal' => '75001',
        'pays' => 'France',
        'date_naissance' => '1990-01-01',
        'nationalite' => 'Française',
        'civilite' => 'M',
        'statut' => 'actif',
        'source_recrutement' => 'LinkedIn'
    ]]
];

foreach ($endpoints as $endpoint) {
    $method = $endpoint[0];
    $url = $baseUrl . $endpoint[1];
    $data = $endpoint[2] ?? null;
    
    $response = makeRequest($url, $method, $data, $headers);
    $status = in_array($response['status'], [200, 201, 422]) ? '✅' : '❌';
    echo "$status $method $endpoint[1] => HTTP {$response['status']}\n";
    if (in_array($response['status'], [200, 201])) $successCount++; else $errorCount++;
}

// Candidatures
echo "\n=== CANDIDATURES ===\n";
$endpoints = [
    ['GET', '/auth/candidatures'],
    ['GET', '/auth/candidatures/actives'],
    ['GET', '/auth/candidatures/recents'],
    ['GET', '/auth/candidatures/spontanees'],
    ['GET', '/auth/candidatures/rechercher'],
    ['GET', '/auth/candidatures/statistiques'],
    ['POST', '/auth/candidatures', [
        'candidat_id' => 1,
        'offre_emploi_id' => 1,
        'date_candidature' => '2024-01-15',
        'statut' => 'en_attente',
        'cv_path' => '/cvs/cv_test.pdf',
        'lettre_motivation' => 'Lettre de motivation de test',
        'source' => 'site_web'
    ]]
];

foreach ($endpoints as $endpoint) {
    $method = $endpoint[0];
    $url = $baseUrl . $endpoint[1];
    $data = $endpoint[2] ?? null;
    
    $response = makeRequest($url, $method, $data, $headers);
    $status = in_array($response['status'], [200, 201, 422]) ? '✅' : '❌';
    echo "$status $method $endpoint[1] => HTTP {$response['status']}\n";
    if (in_array($response['status'], [200, 201])) $successCount++; else $errorCount++;
}

// Evaluations
echo "\n=== EVALUATIONS ===\n";
$endpoints = [
    ['GET', '/auth/evaluations'],
    ['POST', '/auth/evaluations', [
        'evaluable_type' => 'App\\Models\\Candidat',
        'evaluable_id' => 1,
        'type_evaluation' => 'entretien',
        'date_evaluation' => '2024-01-15',
        'evaluateur' => 'Recruteur Test',
        'note' => 8,
        'commentaires' => 'Évaluation de test',
        'statut' => 'terminee'
    ]]
];

foreach ($endpoints as $endpoint) {
    $method = $endpoint[0];
    $url = $baseUrl . $endpoint[1];
    $data = $endpoint[2] ?? null;
    
    $response = makeRequest($url, $method, $data, $headers);
    $status = in_array($response['status'], [200, 201, 422]) ? '✅' : '❌';
    echo "$status $method $endpoint[1] => HTTP {$response['status']}\n";
    if (in_array($response['status'], [200, 201])) $successCount++; else $errorCount++;
}

// Offres d'emploi
echo "\n=== OFFRES D'EMPLOI ===\n";
$endpoints = [
    ['GET', '/auth/offres-emploi'],
    ['POST', '/auth/offres-emploi', [
        'titre' => 'Développeur Web',
        'description' => 'Description du poste',
        'entreprise' => 'Entreprise Test',
        'lieu' => 'Paris',
        'type_contrat' => 'CDI',
        'salaire_min' => 40000,
        'salaire_max' => 60000,
        'statut' => 'active',
        'date_publication' => '2024-01-15',
        'date_expiration' => '2024-02-15',
        'competences_requises' => 'PHP, Laravel, JavaScript',
        'experience_requise' => '3-5 ans',
        'niveau_etude' => 'Bac+3'
    ]]
];

foreach ($endpoints as $endpoint) {
    $method = $endpoint[0];
    $url = $baseUrl . $endpoint[1];
    $data = $endpoint[2] ?? null;
    
    $response = makeRequest($url, $method, $data, $headers);
    $status = in_array($response['status'], [200, 201, 422]) ? '✅' : '❌';
    echo "$status $method $endpoint[1] => HTTP {$response['status']}\n";
    if (in_array($response['status'], [200, 201])) $successCount++; else $errorCount++;
}

// Résumé final
echo "\n=== RÉSUMÉ DU TEST ===\n";
echo "✅ Endpoints réussis: $successCount\n";
echo "❌ Endpoints en erreur: $errorCount\n";
echo "📊 Total testés: " . ($successCount + $errorCount) . "\n";
echo "🎯 Taux de réussite: " . round(($successCount / ($successCount + $errorCount)) * 100, 2) . "%\n\n";

if ($errorCount === 0) {
    echo "🎉 TOUS LES ENDPOINTS FONCTIONNENT PARFAITEMENT!\n";
} else {
    echo "⚠️  Certains endpoints ont des erreurs. Vérifiez les logs pour plus de détails.\n";
}

echo "\n=== FIN DU TEST ===\n"; 