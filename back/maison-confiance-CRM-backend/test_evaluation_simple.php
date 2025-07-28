<?php

$apiUrl = 'http://127.0.0.1:8000/api';

// Test avec différents utilisateurs existants
$users = [
    ['email' => 'test@example.com', 'password' => 'password'],
    ['email' => 'admin@example.com', 'password' => 'password'],
    ['email' => 'user@example.com', 'password' => 'password']
];

$token = null;

foreach ($users as $user) {
    echo "Tentative de connexion avec {$user['email']}...\n";
    
    $login = curl_init("$apiUrl/login");
    curl_setopt($login, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($login, CURLOPT_POST, true);
    curl_setopt($login, CURLOPT_POSTFIELDS, [
        'email' => $user['email'],
        'password' => $user['password'],
    ]);
    $response = curl_exec($login);
    curl_close($login);
    
    $data = json_decode($response, true);
    if (isset($data['access_token'])) {
        $token = $data['access_token'];
        echo "[OK] Authentification réussie avec {$user['email']}\n";
        break;
    } else {
        echo "[ERREUR] Échec avec {$user['email']}: " . $response . "\n";
    }
}

if (!$token) {
    die("Aucun utilisateur valide trouvé. Vérifiez les utilisateurs dans la base de données.\n");
}

$crudUrl = "$apiUrl/auth/evaluations";

// 1. Lister les évaluations
echo "\n=== TEST 1: Lister les évaluations ===\n";
$list = curl_init($crudUrl);
curl_setopt($list, CURLOPT_RETURNTRANSFER, true);
curl_setopt($list, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer $token"
]);
$response = curl_exec($list);
curl_close($list);
echo "Réponse: $response\n";

// 2. Créer une évaluation
echo "\n=== TEST 2: Créer une évaluation ===\n";
$create = curl_init($crudUrl);
curl_setopt($create, CURLOPT_RETURNTRANSFER, true);
curl_setopt($create, CURLOPT_POST, true);
curl_setopt($create, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer $token"
]);
curl_setopt($create, CURLOPT_POSTFIELDS, [
    'titre' => 'Test CRUD Evaluation',
    'type' => 'candidat',
    'evaluateur_id' => 1,
    'candidat_id' => 1,
    'date_evaluation' => date('Y-m-d'),
    'description' => 'Évaluation de test pour le CRUD'
]);
$response = curl_exec($create);
curl_close($create);
$data = json_decode($response, true);
echo "Réponse création: $response\n";

if (isset($data['id'])) {
    $evalId = $data['id'];
    echo "[OK] Évaluation créée avec l'ID: $evalId\n";
    
    // 3. Afficher l'évaluation
    echo "\n=== TEST 3: Afficher l'évaluation ===\n";
    $show = curl_init("$crudUrl/$evalId");
    curl_setopt($show, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($show, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer $token"
    ]);
    $response = curl_exec($show);
    curl_close($show);
    echo "Réponse affichage: $response\n";
    
    // 4. Modifier l'évaluation
    echo "\n=== TEST 4: Modifier l'évaluation ===\n";
    $update = curl_init("$crudUrl/$evalId");
    curl_setopt($update, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($update, CURLOPT_CUSTOMREQUEST, 'PUT');
    curl_setopt($update, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer $token"
    ]);
    curl_setopt($update, CURLOPT_POSTFIELDS, [
        'titre' => 'Test CRUD Evaluation - MODIFIÉ',
        'statut' => 'en_cours',
        'description' => 'Évaluation modifiée'
    ]);
    $response = curl_exec($update);
    curl_close($update);
    echo "Réponse modification: $response\n";
    
    // 5. Supprimer l'évaluation
    echo "\n=== TEST 5: Supprimer l'évaluation ===\n";
    $delete = curl_init("$crudUrl/$evalId");
    curl_setopt($delete, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($delete, CURLOPT_CUSTOMREQUEST, 'DELETE');
    curl_setopt($delete, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer $token"
    ]);
    $response = curl_exec($delete);
    curl_close($delete);
    echo "Réponse suppression: $response\n";
    
} else {
    echo "[ERREUR] Impossible de créer l'évaluation\n";
}

echo "\n=== TEST TERMINÉ ===\n"; 