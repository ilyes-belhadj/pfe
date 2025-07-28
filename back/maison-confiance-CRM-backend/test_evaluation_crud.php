<?php

$apiUrl = 'http://127.0.0.1:8000/api';
$email = 'admin@example.com';
$password = 'password';

// 1. Authentification
echo "=== Test d'authentification ===\n";
$login = curl_init("$apiUrl/login");
curl_setopt($login, CURLOPT_RETURNTRANSFER, true);
curl_setopt($login, CURLOPT_POST, true);
curl_setopt($login, CURLOPT_POSTFIELDS, json_encode([
    'email' => $email,
    'password' => $password,
]));
curl_setopt($login, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);
$response = curl_exec($login);
$httpCode = curl_getinfo($login, CURLINFO_HTTP_CODE);
curl_close($login);

$data = json_decode($response, true);
if ($httpCode !== 200 || !isset($data['access_token'])) {
    die("❌ Erreur d'authentification (Code: $httpCode) : " . $response . "\n");
}
$token = $data['access_token'];
echo "✅ Authentification réussie\n\n";

$crudUrl = "$apiUrl/auth/evaluations";

// 2. Lister les évaluations
echo "=== Test de listage des évaluations ===\n";
$list = curl_init($crudUrl);
curl_setopt($list, CURLOPT_RETURNTRANSFER, true);
curl_setopt($list, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer $token",
    'Accept: application/json'
]);
$response = curl_exec($list);
$httpCode = curl_getinfo($list, CURLINFO_HTTP_CODE);
curl_close($list);

if ($httpCode === 200) {
    $evaluations = json_decode($response, true);
    echo "✅ Liste récupérée: " . count($evaluations) . " évaluations\n\n";
} else {
    echo "❌ Erreur listage (Code: $httpCode): $response\n\n";
}

// 3. Test de création SANS candidat_id ni employe_id (doit échouer)
echo "=== Test création sans evaluable (doit échouer) ===\n";
$create = curl_init($crudUrl);
curl_setopt($create, CURLOPT_RETURNTRANSFER, true);
curl_setopt($create, CURLOPT_POST, true);
curl_setopt($create, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer $token",
    'Content-Type: application/json',
    'Accept: application/json'
]);
curl_setopt($create, CURLOPT_POSTFIELDS, json_encode([
    'titre' => 'Test sans evaluable',
    'type' => 'candidat',
    'evaluateur_id' => 1,
    'date_evaluation' => date('Y-m-d'),
]));
$response = curl_exec($create);
$httpCode = curl_getinfo($create, CURLINFO_HTTP_CODE);
curl_close($create);

if ($httpCode === 422) {
    echo "✅ Validation échoue comme attendu (Code: $httpCode)\n";
    echo "Réponse: $response\n\n";
} else {
    echo "❌ La validation aurait dû échouer (Code: $httpCode): $response\n\n";
}

// 4. Test de création AVEC candidat_id
echo "=== Test création avec candidat_id ===\n";
$create = curl_init($crudUrl);
curl_setopt($create, CURLOPT_RETURNTRANSFER, true);
curl_setopt($create, CURLOPT_POST, true);
curl_setopt($create, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer $token",
    'Content-Type: application/json',
    'Accept: application/json'
]);
curl_setopt($create, CURLOPT_POSTFIELDS, json_encode([
    'titre' => 'Test CRUD avec candidat',
    'type' => 'candidat',
    'evaluateur_id' => 1,
    'candidat_id' => 1,
    'date_evaluation' => date('Y-m-d'),
]));
$response = curl_exec($create);
$httpCode = curl_getinfo($create, CURLINFO_HTTP_CODE);
curl_close($create);

$data = json_decode($response, true);
if ($httpCode === 201 && isset($data['data']['id'])) {
    $evalId = $data['data']['id'];
    echo "✅ Création réussie avec candidat: ID $evalId\n\n";
} elseif ($httpCode === 422) {
    echo "❌ Erreur de validation: $response\n\n";
    $evalId = null;
} else {
    echo "❌ Erreur création (Code: $httpCode): $response\n\n";
    $evalId = null;
}

// 5. Test de création AVEC employe_id
echo "=== Test création avec employe_id ===\n";
$create2 = curl_init($crudUrl);
curl_setopt($create2, CURLOPT_RETURNTRANSFER, true);
curl_setopt($create2, CURLOPT_POST, true);
curl_setopt($create2, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer $token",
    'Content-Type: application/json',
    'Accept: application/json'
]);
curl_setopt($create2, CURLOPT_POSTFIELDS, json_encode([
    'titre' => 'Test CRUD avec employé',
    'type' => 'employe',
    'evaluateur_id' => 1,
    'employe_id' => 1,
    'date_evaluation' => date('Y-m-d'),
]));
$response = curl_exec($create2);
$httpCode = curl_getinfo($create2, CURLINFO_HTTP_CODE);
curl_close($create2);

$data2 = json_decode($response, true);
if ($httpCode === 201 && isset($data2['data']['id'])) {
    $evalId2 = $data2['data']['id'];
    echo "✅ Création réussie avec employé: ID $evalId2\n\n";
} elseif ($httpCode === 422) {
    echo "❌ Erreur de validation: $response\n\n";
    $evalId2 = null;
} else {
    echo "❌ Erreur création (Code: $httpCode): $response\n\n";
    $evalId2 = null;
}

// 6. Tests de modification et suppression si au moins une création a réussi
if ($evalId) {
    echo "=== Test modification ===\n";
    $update = curl_init("$crudUrl/$evalId");
    curl_setopt($update, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($update, CURLOPT_CUSTOMREQUEST, 'PUT');
    curl_setopt($update, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer $token",
        'Content-Type: application/json',
        'Accept: application/json'
    ]);
    curl_setopt($update, CURLOPT_POSTFIELDS, json_encode([
        'titre' => 'Test CRUD modifié',
        'statut' => 'en_cours'
    ]));
    $response = curl_exec($update);
    $httpCode = curl_getinfo($update, CURLINFO_HTTP_CODE);
    curl_close($update);
    
    if ($httpCode === 200) {
        echo "✅ Modification réussie\n\n";
    } else {
        echo "❌ Erreur modification (Code: $httpCode): $response\n\n";
    }

    echo "=== Test suppression ===\n";
    $delete = curl_init("$crudUrl/$evalId");
    curl_setopt($delete, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($delete, CURLOPT_CUSTOMREQUEST, 'DELETE');
    curl_setopt($delete, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer $token",
        'Accept: application/json'
    ]);
    $response = curl_exec($delete);
    $httpCode = curl_getinfo($delete, CURLINFO_HTTP_CODE);
    curl_close($delete);
    
    if ($httpCode === 204) {
        echo "✅ Suppression réussie\n\n";
    } else {
        echo "❌ Erreur suppression (Code: $httpCode): $response\n\n";
    }
}

// Nettoyer la deuxième évaluation si elle existe
if ($evalId2) {
    $delete2 = curl_init("$crudUrl/$evalId2");
    curl_setopt($delete2, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($delete2, CURLOPT_CUSTOMREQUEST, 'DELETE');
    curl_setopt($delete2, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer $token",
        'Accept: application/json'
    ]);
    curl_exec($delete2);
    curl_close($delete2);
}

echo "=== Tests terminés ===\n"; 