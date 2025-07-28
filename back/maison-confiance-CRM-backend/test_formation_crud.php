<?php

// Configuration
$baseUrl = 'http://127.0.0.1:8000/api';
$email = 'test@example.com';
$password = 'password';

echo "=== Test CRUD Formations ===\n\n";

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

// 2. Créer une formation
echo "2. Création d'une formation...\n";
$formationData = [
    'titre' => 'Formation Laravel Avancé',
    'description' => 'Formation complète sur Laravel avec les bonnes pratiques',
    'formateur' => 'Jean Dupont',
    'date_debut' => '2025-08-15',
    'date_fin' => '2025-08-17',
    'duree_heures' => 24,
    'cout' => 1500.00,
    'statut' => 'planifie',
    'lieu' => 'Salle de formation A',
    'nombre_places' => 15
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/auth/formations');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($formationData));
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 201) {
    $formation = json_decode($response, true);
    $formationId = $formation['data']['id'];
    echo "✅ Formation créée avec succès\n";
    echo "ID: $formationId\n";
    echo "Titre: " . $formation['data']['titre'] . "\n\n";
} else {
    echo "❌ Échec de la création de formation\n";
    echo "Code: $httpCode\n";
    echo "Réponse: $response\n";
    exit(1);
}

// 3. Lister toutes les formations
echo "3. Liste de toutes les formations...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/auth/formations');
curl_setopt($ch, CURLOPT_HTTPGET, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    $formations = json_decode($response, true);
    echo "✅ Liste des formations récupérée\n";
    echo "Nombre de formations: " . count($formations['data']) . "\n\n";
} else {
    echo "❌ Échec de la récupération des formations\n";
    echo "Code: $httpCode\n";
    echo "Réponse: $response\n";
}

// 4. Récupérer une formation spécifique
echo "4. Récupération de la formation créée...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/auth/formations/' . $formationId);
curl_setopt($ch, CURLOPT_HTTPGET, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    $formation = json_decode($response, true);
    echo "✅ Formation récupérée avec succès\n";
    echo "Titre: " . $formation['data']['titre'] . "\n";
    echo "Formateur: " . $formation['data']['formateur'] . "\n\n";
} else {
    echo "❌ Échec de la récupération de la formation\n";
    echo "Code: $httpCode\n";
    echo "Réponse: $response\n";
}

// 5. Mettre à jour une formation
echo "5. Mise à jour de la formation...\n";
$updateData = [
    'titre' => 'Formation Laravel Avancé - Mise à jour',
    'description' => 'Formation complète sur Laravel avec les bonnes pratiques - Version mise à jour',
    'cout' => 1800.00,
    'statut' => 'en_cours'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/auth/formations/' . $formationId);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($updateData));
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    $formation = json_decode($response, true);
    echo "✅ Formation mise à jour avec succès\n";
    echo "Nouveau titre: " . $formation['data']['titre'] . "\n";
    echo "Nouveau coût: " . $formation['data']['cout'] . "\n\n";
} else {
    echo "❌ Échec de la mise à jour de la formation\n";
    echo "Code: $httpCode\n";
    echo "Réponse: $response\n";
}

// 6. Lister les employés disponibles
echo "6. Liste des employés disponibles...\n";
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
    echo "✅ Liste des employés récupérée\n";
    echo "Nombre d'employés: " . count($employes['data']) . "\n";
    
    if (count($employes['data']) > 0) {
        $employeId = $employes['data'][0]['id'];
        echo "Employé sélectionné pour l'inscription: " . $employes['data'][0]['nom'] . " (ID: $employeId)\n\n";
        
        // 7. Inscrire un employé à la formation
        echo "7. Inscription d'un employé à la formation...\n";
        $inscriptionData = [
            'employe_id' => $employeId,
            'notes' => 'Inscription automatique pour test'
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $baseUrl . '/auth/formations/' . $formationId . '/inscrire');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($inscriptionData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200) {
            echo "✅ Employé inscrit avec succès\n\n";
            
            // 8. Lister les employés inscrits
            echo "8. Liste des employés inscrits...\n";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $baseUrl . '/auth/formations/' . $formationId);
            curl_setopt($ch, CURLOPT_HTTPGET, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode === 200) {
                $participants = json_decode($response, true);
                echo "✅ Liste des participants récupérée\n";
                echo "Nombre de participants: " . count($participants['data']) . "\n\n";
            } else {
                echo "❌ Échec de la récupération des participants\n";
                echo "Code: $httpCode\n";
                echo "Réponse: $response\n";
            }
            
            // 9. Désinscrire l'employé
            echo "9. Désinscription de l'employé...\n";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $baseUrl . '/auth/formations/' . $formationId . '/desinscrire');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['employe_id' => $employeId]));
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode === 200) {
                echo "✅ Employé désinscrit avec succès\n\n";
            } else {
                echo "❌ Échec de la désinscription\n";
                echo "Code: $httpCode\n";
                echo "Réponse: $response\n";
            }
        } else {
            echo "❌ Échec de l'inscription de l'employé\n";
            echo "Code: $httpCode\n";
            echo "Réponse: $response\n";
        }
    } else {
        echo "Aucun employé disponible pour l'inscription\n\n";
    }
} else {
    echo "❌ Échec de la récupération des employés\n";
    echo "Code: $httpCode\n";
    echo "Réponse: $response\n";
}

// 10. Lister les formations disponibles
echo "10. Liste des formations disponibles...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/auth/formations/disponibles');
curl_setopt($ch, CURLOPT_HTTPGET, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    $formations = json_decode($response, true);
    echo "✅ Liste des formations disponibles récupérée\n";
    echo "Nombre de formations disponibles: " . count($formations['data']) . "\n\n";
} else {
    echo "❌ Échec de la récupération des formations disponibles\n";
    echo "Code: $httpCode\n";
    echo "Réponse: $response\n";
}

// 11. Supprimer la formation
echo "11. Suppression de la formation...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/auth/formations/' . $formationId);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    echo "✅ Formation supprimée avec succès\n\n";
} else {
    echo "❌ Échec de la suppression de la formation\n";
    echo "Code: $httpCode\n";
    echo "Réponse: $response\n";
}

echo "=== Test CRUD Formations terminé ===\n"; 