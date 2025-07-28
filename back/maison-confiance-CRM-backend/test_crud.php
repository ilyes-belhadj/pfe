<?php

// Script de test pour l'API CRM
$baseUrl = 'http://localhost:8000/api';

echo "=== TEST CRUD API CRM ===\n\n";

// 1. Test d'authentification
echo "1. Test d'authentification...\n";
$loginData = [
    'email' => 'test@example.com',
    'password' => 'password'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/login');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($loginData));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    $data = json_decode($response, true);
    $token = $data['access_token'];
    echo "✅ Connexion réussie\n";
    echo "Token: " . substr($token, 0, 20) . "...\n\n";
} else {
    echo "❌ Échec de la connexion (HTTP $httpCode)\n";
    echo "Réponse: $response\n\n";
    exit;
}

// 2. Test GET employés
echo "2. Test GET employés...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/auth/employes');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $token,
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    $data = json_decode($response, true);
    echo "✅ Liste des employés récupérée\n";
    echo "Nombre d'employés: " . count($data['data'] ?? $data) . "\n\n";
} else {
    echo "❌ Échec de récupération des employés (HTTP $httpCode)\n";
    echo "Réponse: $response\n\n";
}

// 3. Test GET départements
echo "3. Test GET départements...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/auth/departements');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $token,
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    $data = json_decode($response, true);
    echo "✅ Liste des départements récupérée\n";
    echo "Nombre de départements: " . count($data['data'] ?? $data) . "\n\n";
} else {
    echo "❌ Échec de récupération des départements (HTTP $httpCode)\n";
    echo "Réponse: $response\n\n";
}

// 4. Test GET absences
echo "4. Test GET absences...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/auth/absences');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $token,
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    $data = json_decode($response, true);
    echo "✅ Liste des absences récupérée\n";
    echo "Nombre d'absences: " . count($data) . "\n\n";
} else {
    echo "❌ Échec de récupération des absences (HTTP $httpCode)\n";
    echo "Réponse: $response\n\n";
}

// 5. Test POST nouvel employé
echo "5. Test POST nouvel employé...\n";
$newEmploye = [
    'nom' => 'Test',
    'prenom' => 'API',
    'email' => 'test.api@example.com',
    'date_embauche' => '2024-01-01',
    'salaire' => 3000.00,
    'departement_id' => 1
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/auth/employes');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($newEmploye));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $token,
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 201 || $httpCode === 200) {
    $data = json_decode($response, true);
    echo "✅ Employé créé avec succès\n";
    echo "ID: " . ($data['data']['id'] ?? $data['id']) . "\n\n";
} else {
    echo "❌ Échec de création de l'employé (HTTP $httpCode)\n";
    echo "Réponse: $response\n\n";
}

echo "=== FIN DES TESTS ===\n"; 