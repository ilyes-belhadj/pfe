<?php

// Configuration
$baseUrl = 'http://127.0.0.1:8000/api';
$email = 'test@example.com';
$password = 'password';

echo "=== Test CRUD Module Paie ===\n\n";

// 1. Authentification
echo "1. Authentification...\n";
$loginData = [
    'email' => $email,
    'password' => $password
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
    $loginResult = json_decode($response, true);
    $token = $loginResult['access_token'];
    echo "✅ Authentification réussie\n";
    echo "Token: " . substr($token, 0, 20) . "...\n\n";
} else {
    echo "❌ Échec de l'authentification\n";
    echo "Code: $httpCode\n";
    echo "Réponse: $response\n";
    exit(1);
}

// Headers pour les requêtes authentifiées
$headers = [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $token
];

// 2. Récupérer les employés pour créer une paie
echo "2. Récupération des employés...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/auth/employes');
curl_setopt($ch, CURLOPT_HTTPGET, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    $employes = json_decode($response, true);
    echo "✅ Employés récupérés\n";
    echo "Nombre d'employés: " . count($employes['data']) . "\n";
    
    if (count($employes['data']) > 0) {
        $employeId = $employes['data'][0]['id'];
        echo "Employé sélectionné: " . $employes['data'][0]['nom'] . " " . $employes['data'][0]['prenom'] . "\n\n";
        
        // 3. Créer une paie
        echo "3. Création d'une paie...\n";
        $paieData = [
            'employe_id' => $employeId,
            'periode' => '2025-09',
            'date_paiement' => '2025-09-30',
            'salaire_base' => 3500.00,
            'heures_travaillees' => 160,
            'taux_horaire' => 20.00,
            'primes' => 300.00,
            'deductions' => 150.00,
            'cotisations_sociales' => 525.00,
            'impots' => 350.00,
            'statut' => 'en_attente',
            'notes' => 'Paie de test pour septembre 2025',
            'mode_paiement' => 'virement'
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $baseUrl . '/auth/paies');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($paieData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 201) {
            $paie = json_decode($response, true);
            $paieId = $paie['data']['id'];
            echo "✅ Paie créée avec succès\n";
            echo "ID: $paieId\n";
            echo "Salaire net: " . $paie['data']['salaire_net'] . " €\n\n";
        } else {
            echo "❌ Échec de la création de paie\n";
            echo "Code: $httpCode\n";
            echo "Réponse: $response\n";
            exit(1);
        }

        // 4. Lister toutes les paies
        echo "4. Liste de toutes les paies...\n";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $baseUrl . '/auth/paies');
        curl_setopt($ch, CURLOPT_HTTPGET, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200) {
            $paies = json_decode($response, true);
            echo "✅ Liste des paies récupérée\n";
            echo "Nombre de paies: " . count($paies['data']) . "\n\n";
        } else {
            echo "❌ Échec de la récupération des paies\n";
            echo "Code: $httpCode\n";
            echo "Réponse: $response\n";
        }

        // 5. Récupérer une paie spécifique
        echo "5. Récupération de la paie créée...\n";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $baseUrl . '/auth/paies/' . $paieId);
        curl_setopt($ch, CURLOPT_HTTPGET, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200) {
            $paie = json_decode($response, true);
            echo "✅ Paie récupérée avec succès\n";
            echo "Employé: " . $paie['data']['employe']['nom_complet'] . "\n";
            echo "Période: " . $paie['data']['periode'] . "\n";
            echo "Statut: " . $paie['data']['statut_label'] . "\n\n";
        } else {
            echo "❌ Échec de la récupération de la paie\n";
            echo "Code: $httpCode\n";
            echo "Réponse: $response\n";
        }

        // 6. Mettre à jour une paie
        echo "6. Mise à jour de la paie...\n";
        $updateData = [
            'primes' => 400.00,
            'notes' => 'Paie mise à jour avec primes supplémentaires'
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $baseUrl . '/auth/paies/' . $paieId);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($updateData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200) {
            $paie = json_decode($response, true);
            echo "✅ Paie mise à jour avec succès\n";
            echo "Nouvelles primes: " . $paie['data']['primes'] . " €\n";
            echo "Nouveau salaire net: " . $paie['data']['salaire_net'] . " €\n\n";
        } else {
            echo "❌ Échec de la mise à jour de la paie\n";
            echo "Code: $httpCode\n";
            echo "Réponse: $response\n";
        }

        // 7. Marquer la paie comme payée
        echo "7. Marquage de la paie comme payée...\n";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $baseUrl . '/auth/paies/' . $paieId . '/marquer-payee');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200) {
            echo "✅ Paie marquée comme payée avec succès\n\n";
        } else {
            echo "❌ Échec du marquage de la paie\n";
            echo "Code: $httpCode\n";
            echo "Réponse: $response\n";
        }

        // 8. Générer la fiche de paie
        echo "8. Génération de la fiche de paie...\n";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $baseUrl . '/auth/paies/' . $paieId . '/fiche');
        curl_setopt($ch, CURLOPT_HTTPGET, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200) {
            $fiche = json_decode($response, true);
            echo "✅ Fiche de paie générée avec succès\n";
            echo "Employé: " . $fiche['employe']['nom'] . "\n";
            echo "Période: " . $fiche['periode'] . "\n";
            echo "Salaire net: " . $fiche['salaire_net'] . " €\n\n";
        } else {
            echo "❌ Échec de la génération de la fiche de paie\n";
            echo "Code: $httpCode\n";
            echo "Réponse: $response\n";
        }

        // 9. Obtenir les paies en attente
        echo "9. Liste des paies en attente...\n";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $baseUrl . '/auth/paies/en-attente');
        curl_setopt($ch, CURLOPT_HTTPGET, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200) {
            $paiesEnAttente = json_decode($response, true);
            echo "✅ Paies en attente récupérées\n";
            echo "Nombre de paies en attente: " . count($paiesEnAttente['data']) . "\n\n";
        } else {
            echo "❌ Échec de la récupération des paies en attente\n";
            echo "Code: $httpCode\n";
            echo "Réponse: $response\n";
        }

        // 10. Obtenir les paies payées
        echo "10. Liste des paies payées...\n";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $baseUrl . '/auth/paies/payees');
        curl_setopt($ch, CURLOPT_HTTPGET, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200) {
            $paiesPayees = json_decode($response, true);
            echo "✅ Paies payées récupérées\n";
            echo "Nombre de paies payées: " . count($paiesPayees['data']) . "\n\n";
        } else {
            echo "❌ Échec de la récupération des paies payées\n";
            echo "Code: $httpCode\n";
            echo "Réponse: $response\n";
        }

        // 11. Obtenir les statistiques
        echo "11. Statistiques des paies...\n";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $baseUrl . '/auth/paies/statistiques?periode=2025-09');
        curl_setopt($ch, CURLOPT_HTTPGET, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200) {
            $stats = json_decode($response, true);
            echo "✅ Statistiques récupérées\n";
            echo "Total paies: " . $stats['total_paies'] . "\n";
            echo "Total salaire brut: " . $stats['total_salaire_brut'] . " €\n";
            echo "Total salaire net: " . $stats['total_salaire_net'] . " €\n";
            echo "Paies en attente: " . $stats['paies_en_attente'] . "\n";
            echo "Paies payées: " . $stats['paies_payees'] . "\n\n";
        } else {
            echo "❌ Échec de la récupération des statistiques\n";
            echo "Code: $httpCode\n";
            echo "Réponse: $response\n";
        }

        // 12. Obtenir les paies d'un employé
        echo "12. Paies d'un employé...\n";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $baseUrl . '/auth/employes/' . $employeId . '/paies');
        curl_setopt($ch, CURLOPT_HTTPGET, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200) {
            $paiesEmploye = json_decode($response, true);
            echo "✅ Paies de l'employé récupérées\n";
            echo "Nombre de paies: " . count($paiesEmploye['data']) . "\n\n";
        } else {
            echo "❌ Échec de la récupération des paies de l'employé\n";
            echo "Code: $httpCode\n";
            echo "Réponse: $response\n";
        }

        // 13. Supprimer la paie créée
        echo "13. Suppression de la paie...\n";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $baseUrl . '/auth/paies/' . $paieId);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 204) {
            echo "✅ Paie supprimée avec succès\n\n";
        } else {
            echo "❌ Échec de la suppression de la paie\n";
            echo "Code: $httpCode\n";
            echo "Réponse: $response\n";
        }
    } else {
        echo "Aucun employé disponible pour créer une paie\n\n";
    }
} else {
    echo "❌ Échec de la récupération des employés\n";
    echo "Code: $httpCode\n";
    echo "Réponse: $response\n";
}

echo "=== Test CRUD Module Paie terminé ===\n"; 