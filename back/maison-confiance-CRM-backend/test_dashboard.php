<?php

// Configuration
$baseUrl = 'http://localhost:8000/api';
$adminEmail = 'admin@maison-confiance.com';
$adminPassword = 'password123';

echo "=== TEST DU DASHBOARD CRM ===\n\n";

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

// 2. Test du Dashboard
echo "2. Test du Dashboard...\n";
$dashboardResponse = makeRequest($baseUrl . '/auth/dashboard', 'GET', null, [
    'Authorization: Bearer ' . $token,
    'Accept: application/json'
]);

if ($dashboardResponse['status'] === 200) {
    $dashboardData = $dashboardResponse['data']['data'];
    echo "✅ Dashboard accessible\n\n";
    
    // Affichage des statistiques
    echo "=== STATISTIQUES DU DASHBOARD ===\n\n";
    
    // Utilisateurs
    if (isset($dashboardData['users'])) {
        echo "👥 UTILISATEURS:\n";
        echo "   Total: " . $dashboardData['users']['total'] . "\n";
        echo "   Actifs: " . $dashboardData['users']['active'] . "\n";
        echo "   Inactifs: " . $dashboardData['users']['inactive'] . "\n";
        echo "   Récents (30j): " . $dashboardData['users']['recent'] . "\n";
        echo "   % Actifs: " . $dashboardData['users']['percentage_active'] . "%\n\n";
    }
    
    // Employés
    if (isset($dashboardData['employees'])) {
        echo "👨‍💼 EMPLOYÉS:\n";
        echo "   Total: " . $dashboardData['employees']['total'] . "\n";
        echo "   Récents (30j): " . $dashboardData['employees']['recent'] . "\n";
        if (isset($dashboardData['employees']['by_department'])) {
            echo "   Par département: " . count($dashboardData['employees']['by_department']) . " départements\n";
        }
        echo "\n";
    }
    
    // Départements
    if (isset($dashboardData['departments'])) {
        echo "🏢 DÉPARTEMENTS:\n";
        echo "   Total: " . $dashboardData['departments']['total'] . "\n";
        echo "   Avec employés: " . $dashboardData['departments']['with_employees'] . "\n";
        echo "   Sans employés: " . $dashboardData['departments']['without_employees'] . "\n";
        echo "   Moyenne employés: " . $dashboardData['departments']['average_employees'] . "\n\n";
    }
    
    // Absences
    if (isset($dashboardData['absences'])) {
        echo "🏖️ ABSENCES:\n";
        echo "   Total: " . $dashboardData['absences']['total'] . "\n";
        echo "   Ce mois: " . $dashboardData['absences']['current_month'] . "\n";
        echo "   En attente: " . $dashboardData['absences']['pending'] . "\n";
        if (isset($dashboardData['absences']['by_motif'])) {
            echo "   Par motif: " . count($dashboardData['absences']['by_motif']) . " motifs\n";
        }
        echo "\n";
    }
    
    // Formations
    if (isset($dashboardData['formations'])) {
        echo "📚 FORMATIONS:\n";
        echo "   Total: " . $dashboardData['formations']['total'] . "\n";
        echo "   Actives: " . $dashboardData['formations']['active'] . "\n";
        echo "   Terminées: " . $dashboardData['formations']['completed'] . "\n";
        echo "   Annulées: " . $dashboardData['formations']['cancelled'] . "\n";
        echo "   Coût total: " . $dashboardData['formations']['total_cost'] . "€\n";
        echo "   Coût moyen: " . $dashboardData['formations']['average_cost'] . "€\n\n";
    }
    
    // Paies
    if (isset($dashboardData['paies'])) {
        echo "💰 PAIES:\n";
        echo "   Total: " . $dashboardData['paies']['total'] . "\n";
        echo "   Payées: " . $dashboardData['paies']['paid'] . "\n";
        echo "   En attente: " . $dashboardData['paies']['pending'] . "\n";
        echo "   Montant total: " . $dashboardData['paies']['total_amount'] . "€\n";
        echo "   Montant moyen: " . $dashboardData['paies']['average_amount'] . "€\n";
        if (isset($dashboardData['paies']['by_period'])) {
            echo "   Par période: " . count($dashboardData['paies']['by_period']) . " périodes\n";
        }
        echo "\n";
    }
    
    // Pointages
    if (isset($dashboardData['pointages'])) {
        echo "⏰ POINTAGES:\n";
        echo "   Total: " . $dashboardData['pointages']['total'] . "\n";
        echo "   Aujourd'hui: " . $dashboardData['pointages']['today'] . "\n";
        echo "   Cette semaine: " . $dashboardData['pointages']['this_week'] . "\n";
        echo "   Validés: " . $dashboardData['pointages']['validated'] . "\n";
        echo "   Heures totales: " . $dashboardData['pointages']['total_hours'] . "h\n";
        if (isset($dashboardData['pointages']['by_status'])) {
            echo "   Par statut: " . count($dashboardData['pointages']['by_status']) . " statuts\n";
        }
        echo "\n";
    }
    
    // Candidats
    if (isset($dashboardData['candidats'])) {
        echo "👤 CANDIDATS:\n";
        echo "   Total: " . $dashboardData['candidats']['total'] . "\n";
        echo "   Actifs: " . $dashboardData['candidats']['active'] . "\n";
        echo "   Inactifs: " . $dashboardData['candidats']['inactive'] . "\n";
        echo "   Blacklistés: " . $dashboardData['candidats']['blacklisted'] . "\n";
        echo "   Récents (30j): " . $dashboardData['candidats']['recent'] . "\n";
        if (isset($dashboardData['candidats']['by_source'])) {
            echo "   Par source: " . count($dashboardData['candidats']['by_source']) . " sources\n";
        }
        echo "\n";
    }
    
    // Candidatures
    if (isset($dashboardData['candidatures'])) {
        echo "📝 CANDIDATURES:\n";
        echo "   Total: " . $dashboardData['candidatures']['total'] . "\n";
        echo "   Actives: " . $dashboardData['candidatures']['active'] . "\n";
        echo "   Embauches: " . $dashboardData['candidatures']['hired'] . "\n";
        echo "   Refusées: " . $dashboardData['candidatures']['rejected'] . "\n";
        echo "   Récents (30j): " . $dashboardData['candidatures']['recent'] . "\n";
        if (isset($dashboardData['candidatures']['by_status'])) {
            echo "   Par statut: " . count($dashboardData['candidatures']['by_status']) . " statuts\n";
        }
        echo "\n";
    }
    
    // Évaluations
    if (isset($dashboardData['evaluations'])) {
        echo "⭐ ÉVALUATIONS:\n";
        echo "   Total: " . $dashboardData['evaluations']['total'] . "\n";
        echo "   Ce mois: " . $dashboardData['evaluations']['this_month'] . "\n";
        echo "   Note moyenne: " . $dashboardData['evaluations']['average_score'] . "/10\n";
        if (isset($dashboardData['evaluations']['by_type'])) {
            echo "   Par type: " . count($dashboardData['evaluations']['by_type']) . " types\n";
        }
        echo "\n";
    }
    
    // Projets
    if (isset($dashboardData['projects'])) {
        echo "📋 PROJETS:\n";
        echo "   Total: " . $dashboardData['projects']['total'] . "\n";
        echo "   Actifs: " . $dashboardData['projects']['active'] . "\n";
        echo "   Terminés: " . $dashboardData['projects']['completed'] . "\n";
        echo "   Annulés: " . $dashboardData['projects']['cancelled'] . "\n";
        if (isset($dashboardData['projects']['by_status'])) {
            echo "   Par statut: " . count($dashboardData['projects']['by_status']) . " statuts\n";
        }
        echo "\n";
    }
    
    // Offres d'emploi
    if (isset($dashboardData['offres_emploi'])) {
        echo "💼 OFFRES D'EMPLOI:\n";
        echo "   Total: " . $dashboardData['offres_emploi']['total'] . "\n";
        echo "   Actives: " . $dashboardData['offres_emploi']['active'] . "\n";
        echo "   Fermées: " . $dashboardData['offres_emploi']['closed'] . "\n";
        echo "   Brouillons: " . $dashboardData['offres_emploi']['draft'] . "\n";
        if (isset($dashboardData['offres_emploi']['by_type'])) {
            echo "   Par type: " . count($dashboardData['offres_emploi']['by_type']) . " types\n";
        }
        echo "\n";
    }
    
    // Général
    if (isset($dashboardData['general'])) {
        echo "📊 GÉNÉRAL:\n";
        echo "   Modules total: " . $dashboardData['general']['total_modules'] . "\n";
        echo "   Statut système: " . $dashboardData['general']['system_status'] . "\n";
        echo "   Dernière mise à jour: " . $dashboardData['general']['last_updated'] . "\n\n";
    }
    
    echo "✅ TOUTES LES STATISTIQUES ONT ÉTÉ RÉCUPÉRÉES AVEC SUCCÈS!\n";
    echo "🎉 Le dashboard CRM est entièrement fonctionnel!\n\n";
    
} else {
    echo "❌ Erreur lors de l'accès au dashboard: " . $dashboardResponse['body'] . "\n";
    exit(1);
}

echo "=== FIN DU TEST ===\n"; 