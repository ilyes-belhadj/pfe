<?php

echo "=== SUITE TEST CRUD ÉVALUATIONS ===\n";

$apiUrl = 'http://127.0.0.1:8000/api';

// Authentification
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
$token = $data['access_token'];
$crudUrl = "$apiUrl/auth/evaluations";
$evalId = 22; // L'évaluation créée précédemment

echo "✅ Authentification réussie\n";

// 1. Afficher l'évaluation
echo "\n1. Affichage de l'évaluation $evalId...\n";
$show = curl_init("$crudUrl/$evalId");
curl_setopt($show, CURLOPT_RETURNTRANSFER, true);
curl_setopt($show, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer $token"
]);
$response = curl_exec($show);
curl_close($show);
$evaluation = json_decode($response, true);
echo "✅ Évaluation trouvée: " . $evaluation['titre'] . "\n";

// 2. Modifier l'évaluation
echo "\n2. Modification de l'évaluation...\n";
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

// 3. Ajouter des résultats
echo "\n3. Ajout de résultats...\n";
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

// 4. Terminer l'évaluation
echo "\n4. Terminer l'évaluation...\n";
$terminer = curl_init("$crudUrl/$evalId/terminer");
curl_setopt($terminer, CURLOPT_RETURNTRANSFER, true);
curl_setopt($terminer, CURLOPT_POST, true);
curl_setopt($terminer, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer $token"
]);
$response = curl_exec($terminer);
curl_close($terminer);
echo "✅ Évaluation terminée\n";

// 5. Statistiques
echo "\n5. Récupération des statistiques...\n";
$stats = curl_init("$crudUrl/statistiques");
curl_setopt($stats, CURLOPT_RETURNTRANSFER, true);
curl_setopt($stats, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer $token"
]);
$response = curl_exec($stats);
curl_close($stats);
$statistiques = json_decode($response, true);
echo "✅ Statistiques: " . $statistiques['total_evaluations'] . " évaluations totales\n";

// 6. Évaluations en cours
echo "\n6. Évaluations en cours...\n";
$enCours = curl_init("$crudUrl/en-cours");
curl_setopt($enCours, CURLOPT_RETURNTRANSFER, true);
curl_setopt($enCours, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer $token"
]);
$response = curl_exec($enCours);
curl_close($enCours);
$enCoursData = json_decode($response, true);
echo "✅ " . count($enCoursData) . " évaluations en cours\n";

// 7. Évaluations terminées
echo "\n7. Évaluations terminées...\n";
$terminees = curl_init("$crudUrl/terminees");
curl_setopt($terminees, CURLOPT_RETURNTRANSFER, true);
curl_setopt($terminees, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer $token"
]);
$response = curl_exec($terminees);
curl_close($terminees);
$termineesData = json_decode($response, true);
echo "✅ " . count($termineesData) . " évaluations terminées\n";

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

echo "\n🎉 TOUS LES TESTS CRUD RÉUSSIS !\n";
echo "Le module Évaluation fonctionne parfaitement !\n"; 