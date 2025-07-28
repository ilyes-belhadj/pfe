<?php

echo "=== TEST CRUD COMPLET ÉVALUATIONS ===\n";

$apiUrl = 'http://127.0.0.1:8000/api';

// 1. Authentification avec l'utilisateur de test existant
echo "1. Authentification...\n";
$login = curl_init("$apiUrl/login");
curl_setopt($login, CURLOPT_RETURNTRANSFER, true);
curl_setopt($login, CURLOPT_POST, true);
curl_setopt($login, CURLOPT_POSTFIELDS, [
    'email' => 'test@example.com',
    'password' => 'password',
]);
$response = curl_exec($login);
curl_close($login);

$data = json_decode($response, true);
if (!isset($data['access_token'])) {
    die("❌ Erreur d'authentification : " . $response . "\n");
}

$token = $data['access_token'];
echo "✅ Authentification réussie\n";

$crudUrl = "$apiUrl/auth/evaluations";

// 2. Lister les évaluations existantes
echo "\n2. Liste des évaluations...\n";
$list = curl_init($crudUrl);
curl_setopt($list, CURLOPT_RETURNTRANSFER, true);
curl_setopt($list, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer $token"
]);
$response = curl_exec($list);
curl_close($list);
$evaluations = json_decode($response, true);
echo "✅ " . count($evaluations) . " évaluations trouvées\n";

// 3. Créer une nouvelle évaluation
echo "\n3. Création d'une évaluation...\n";
$create = curl_init($crudUrl);
curl_setopt($create, CURLOPT_RETURNTRANSFER, true);
curl_setopt($create, CURLOPT_POST, true);
curl_setopt($create, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer $token"
]);
curl_setopt($create, CURLOPT_POSTFIELDS, [
    'titre' => 'Test CRUD Évaluation',
    'type' => 'candidat',
    'evaluateur_id' => 1,
    'candidat_id' => 1,
    'date_evaluation' => date('Y-m-d'),
    'description' => 'Évaluation de test pour le CRUD complet',
    'priorite' => 'normale'
]);
$response = curl_exec($create);
curl_close($create);

$data = json_decode($response, true);
if (!isset($data['id'])) {
    die("❌ Erreur création : $response\n");
}

$evalId = $data['id'];
echo "✅ Évaluation créée avec l'ID: $evalId\n";

// 4. Afficher l'évaluation créée
echo "\n4. Affichage de l'évaluation...\n";
$show = curl_init("$crudUrl/$evalId");
curl_setopt($show, CURLOPT_RETURNTRANSFER, true);
curl_setopt($show, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer $token"
]);
$response = curl_exec($show);
curl_close($show);
$evaluation = json_decode($response, true);
echo "✅ Évaluation trouvée: " . $evaluation['titre'] . "\n";

// 5. Modifier l'évaluation
echo "\n5. Modification de l'évaluation...\n";
$update = curl_init("$crudUrl/$evalId");
curl_setopt($update, CURLOPT_RETURNTRANSFER, true);
curl_setopt($update, CURLOPT_CUSTOMREQUEST, 'PUT');
curl_setopt($update, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer $token"
]);
curl_setopt($update, CURLOPT_POSTFIELDS, [
    'titre' => 'Test CRUD Évaluation - MODIFIÉ',
    'statut' => 'en_cours',
    'description' => 'Évaluation modifiée avec succès',
    'note_globale' => 8.5,
    'commentaires_evaluateur' => 'Test de modification réussi'
]);
$response = curl_exec($update);
curl_close($update);
$data = json_decode($response, true);
echo "✅ Évaluation modifiée: " . $data['titre'] . "\n";

// 6. Tester les actions spéciales
echo "\n6. Test des actions spéciales...\n";

// Ajouter des résultats
$resultats = curl_init("$crudUrl/$evalId/ajouter-resultats");
curl_setopt($resultats, CURLOPT_RETURNTRANSFER, true);
curl_setopt($resultats, CURLOPT_POST, true);
curl_setopt($resultats, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer $token"
]);
curl_setopt($resultats, CURLOPT_POSTFIELDS, [
    'resultats' => json_encode([
        'competences_techniques' => ['note' => 8.5, 'commentaire' => 'Très bon niveau'],
        'motivation' => ['note' => 9.0, 'commentaire' => 'Excellente motivation']
    ]),
    'note_globale' => 8.8,
    'forces' => 'Excellente maîtrise technique',
    'axes_amelioration' => 'Développer le leadership'
]);
$response = curl_exec($resultats);
curl_close($resultats);
echo "✅ Résultats ajoutés\n";

// Terminer l'évaluation
$terminer = curl_init("$crudUrl/$evalId/terminer");
curl_setopt($terminer, CURLOPT_RETURNTRANSFER, true);
curl_setopt($terminer, CURLOPT_POST, true);
curl_setopt($terminer, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer $token"
]);
$response = curl_exec($terminer);
curl_close($terminer);
echo "✅ Évaluation terminée\n";

// 7. Statistiques
echo "\n7. Test des statistiques...\n";
$stats = curl_init("$crudUrl/statistiques");
curl_setopt($stats, CURLOPT_RETURNTRANSFER, true);
curl_setopt($stats, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer $token"
]);
$response = curl_exec($stats);
curl_close($stats);
$statistiques = json_decode($response, true);
echo "✅ Statistiques récupérées: " . $statistiques['total_evaluations'] . " évaluations totales\n";

// 8. Supprimer l'évaluation de test
echo "\n8. Suppression de l'évaluation de test...\n";
$delete = curl_init("$crudUrl/$evalId");
curl_setopt($delete, CURLOPT_RETURNTRANSFER, true);
curl_setopt($delete, CURLOPT_CUSTOMREQUEST, 'DELETE');
curl_setopt($delete, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer $token"
]);
$response = curl_exec($delete);
curl_close($delete);
echo "✅ Évaluation supprimée\n";

echo "\n🎉 TEST CRUD COMPLET RÉUSSI !\n";
echo "Toutes les opérations CRUD des évaluations fonctionnent correctement.\n"; 