<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\DepartementController;
use App\Http\Controllers\Api\EmployeController;
use App\Http\Controllers\Api\AbsenceController;
use App\Http\Controllers\Api\FormationController;
use App\Http\Controllers\Api\PaieController;
use App\Http\Controllers\Api\PointageController;
use App\Http\Controllers\Api\CandidatController;
use App\Http\Controllers\Api\CandidatureController;
use App\Http\Controllers\Api\EvaluationController;
use App\Http\Controllers\Api\OffreEmploiController;
use App\Http\Controllers\Api\DashboardController;

// Authentification sans middleware
Route::post('login', [AuthController::class, 'login']);
Route::post('test', [AuthController::class, 'testApi']);
Route::post('register', [AuthController::class, 'register']);

// Routes protégées
Route::group(['middleware' => 'jwt.auth', 'prefix' => 'auth'], function () {

    // Dashboard
    Route::group(['prefix' => 'dashboard'], function () {
        Route::get('', [DashboardController::class, 'index']);
        Route::get('{module}', [DashboardController::class, 'moduleStats']);
    });

    // Auth
    Route::get('me', [AuthController::class, 'me']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);

    // Users
    Route::group(['prefix' => 'users'], function () {
        Route::get('', [UserController::class, 'index']);
        Route::get('all', [UserController::class, 'all'])->name('users.index');
        Route::post('', [UserController::class, 'store']);
        Route::get('{iduser}', [UserController::class, 'show']);
        Route::put('{iduser}', [UserController::class, 'update']);
        Route::delete('{iduser}', [UserController::class, 'destroy']);
        Route::post('onToggleChangetat', [UserController::class, 'onToggleChangetat'])->name('users.onToggleChangetat');
        Route::post('verifyUserExistantt', [UserController::class, 'verifyUserExistantt']);
    });

    // Roles
    Route::group(['prefix' => 'roles'], function () {
        Route::get('', [RoleController::class, 'index']);
        Route::post('', [RoleController::class, 'store']);
        Route::get('{idrole}', [RoleController::class, 'show']);
        Route::put('{idrole}', [RoleController::class, 'update']);
        Route::delete('{idrole}', [RoleController::class, 'destroy']);
    });

    // absences
    Route::group(['prefix' => 'absences'], function () {
        Route::get('', [AbsenceController::class, 'index']);
        Route::post('', [AbsenceController::class, 'store']);
        Route::get('{absence}', [AbsenceController::class, 'show']);
        Route::put('{absence}', [AbsenceController::class, 'update']);
        Route::delete('{absence}', [AbsenceController::class, 'destroy']);
    });

    // Formations
    Route::group(['prefix' => 'formations'], function () {
        Route::get('', [FormationController::class, 'index']);
        Route::post('', [FormationController::class, 'store']);
        Route::get('disponibles', [FormationController::class, 'formationsDisponibles']);
        Route::get('{formation}', [FormationController::class, 'show']);
        Route::put('{formation}', [FormationController::class, 'update']);
        Route::delete('{formation}', [FormationController::class, 'destroy']);
        
        // Inscription/Désinscription
        Route::post('{formation}/inscrire', [FormationController::class, 'inscrireEmploye']);
        Route::post('{formation}/desinscrire', [FormationController::class, 'desinscrireEmploye']);
    });

    // Projects
    Route::group(['prefix' => 'projects'], function () {
        Route::get('all', [ProjectController::class, 'allPaginated']);
        Route::get('all/kanban', [ProjectController::class, 'all']);
        Route::get('', [ProjectController::class, 'index']);
        Route::post('', [ProjectController::class, 'store']);
        Route::get('{idproject}', [ProjectController::class, 'show']);
        Route::put('{idproject}', [ProjectController::class, 'update']);
        Route::delete('{idproject}', [ProjectController::class, 'destroy']);
    });

    // Departements
    Route::group(['prefix' => 'departements'], function () {
        Route::get('all', [DepartementController::class, 'allPaginated']); // optionnel
        Route::get('all/kanban', [DepartementController::class, 'all']);   // optionnel
        Route::get('', [DepartementController::class, 'index']);
        Route::post('', [DepartementController::class, 'store']);
        Route::get('{departement}', [DepartementController::class, 'show']);
        Route::put('{departement}', [DepartementController::class, 'update']);
        Route::delete('{departement}', [DepartementController::class, 'destroy']);
    });

    // Employes (si besoin)
    Route::group(['prefix' => 'employes'], function () {
        Route::get('all', [EmployeController::class, 'allPaginated']);
        Route::get('all/kanban', [EmployeController::class, 'all']);
        Route::get('', [EmployeController::class, 'index']);
        Route::post('', [EmployeController::class, 'store']);
        Route::get('{employes}', [EmployeController::class, 'show']);
        Route::put('{employes}', [EmployeController::class, 'update']);
        Route::delete('{employes}', [EmployeController::class, 'destroy']);
        
        // Paies d'un employé
        Route::get('{employe}/paies', [PaieController::class, 'paiesEmploye']);
    });

    // Paies
    Route::group(['prefix' => 'paies'], function () {
        Route::get('', [PaieController::class, 'index']);
        Route::post('', [PaieController::class, 'store']);
        Route::get('en-attente', [PaieController::class, 'paiesEnAttente']);
        Route::get('payees', [PaieController::class, 'paiesPayees']);
        Route::get('statistiques', [PaieController::class, 'statistiques']);
        Route::get('{paie}', [PaieController::class, 'show']);
        Route::put('{paie}', [PaieController::class, 'update']);
        Route::delete('{paie}', [PaieController::class, 'destroy']);
        Route::post('{paie}/marquer-payee', [PaieController::class, 'marquerPayee']);
        Route::get('{paie}/fiche', [PaieController::class, 'fichePaie']);
    });

    // Pointages
    Route::group(['prefix' => 'pointages'], function () {
        Route::get('', [PointageController::class, 'index']);
        Route::post('', [PointageController::class, 'store']);
        
        // Pointages spéciaux
        Route::post('entree', [PointageController::class, 'entree']);
        Route::post('sortie', [PointageController::class, 'sortie']);
        Route::post('debut-pause', [PointageController::class, 'debutPause']);
        Route::post('fin-pause', [PointageController::class, 'finPause']);
        
        // Consultations spéciales (doivent être avant les routes avec paramètres)
        Route::get('aujourdhui', [PointageController::class, 'aujourdhui']);
        Route::get('non-valides', [PointageController::class, 'nonValides']);
        Route::get('statistiques', [PointageController::class, 'statistiques']);
        Route::get('actuel', [PointageController::class, 'pointageActuel']);
        Route::get('employe/{employe}', [PointageController::class, 'pointagesEmploye']);
        
        // Routes avec paramètres (doivent être après les routes spéciales)
        Route::get('{pointage}', [PointageController::class, 'show']);
        Route::put('{pointage}', [PointageController::class, 'update']);
        Route::delete('{pointage}', [PointageController::class, 'destroy']);
        
        // Validation
        Route::post('{pointage}/valider', [PointageController::class, 'valider']);
        Route::post('{pointage}/invalider', [PointageController::class, 'invalider']);
    });

    // Candidats
    Route::group(['prefix' => 'candidats'], function () {
        Route::get('', [CandidatController::class, 'index']);
        Route::post('', [CandidatController::class, 'store']);
        Route::get('actifs', [CandidatController::class, 'actifs']);
        Route::get('recents', [CandidatController::class, 'recents']);
        Route::get('rechercher', [CandidatController::class, 'rechercher']);
        Route::get('statistiques', [CandidatController::class, 'statistiques']);
        Route::get('{candidat}', [CandidatController::class, 'show']);
        Route::put('{candidat}', [CandidatController::class, 'update']);
        Route::delete('{candidat}', [CandidatController::class, 'destroy']);
        
        // Actions sur les candidats
        Route::post('{candidat}/marquer-actif', [CandidatController::class, 'marquerActif']);
        Route::post('{candidat}/marquer-inactif', [CandidatController::class, 'marquerInactif']);
        Route::post('{candidat}/blacklister', [CandidatController::class, 'blacklister']);
        Route::get('{candidat}/candidatures', [CandidatController::class, 'candidatures']);
        Route::get('{candidat}/candidatures-actives', [CandidatController::class, 'candidaturesActives']);
    });

    // Candidatures
    Route::group(['prefix' => 'candidatures'], function () {
        Route::get('', [CandidatureController::class, 'index']);
        Route::post('', [CandidatureController::class, 'store']);
        Route::get('actives', [CandidatureController::class, 'actives']);
        Route::get('recents', [CandidatureController::class, 'recents']);
        Route::get('spontanees', [CandidatureController::class, 'spontanees']);
        Route::get('rechercher', [CandidatureController::class, 'rechercher']);
        Route::get('statistiques', [CandidatureController::class, 'statistiques']);
        Route::get('{candidature}', [CandidatureController::class, 'show']);
        Route::put('{candidature}', [CandidatureController::class, 'update']);
        Route::delete('{candidature}', [CandidatureController::class, 'destroy']);
        
        // Actions sur les candidatures
        Route::post('{candidature}/changer-statut', [CandidatureController::class, 'changerStatut']);
        Route::post('{candidature}/planifier-entretien', [CandidatureController::class, 'planifierEntretien']);
        Route::post('{candidature}/evaluer', [CandidatureController::class, 'evaluer']);
        
        // Téléchargement de fichiers
        Route::get('{candidature}/telecharger-cv', [CandidatureController::class, 'telechargerCv']);
        Route::get('{candidature}/telecharger-lettre', [CandidatureController::class, 'telechargerLettre']);
    });

    // Évaluations
    Route::group(['prefix' => 'evaluations'], function () {
        Route::get('', [EvaluationController::class, 'index']);
        Route::post('', [EvaluationController::class, 'store']);
        Route::get('en-cours', [EvaluationController::class, 'enCours']);
        Route::get('terminees', [EvaluationController::class, 'terminees']);
        Route::get('en-retard', [EvaluationController::class, 'enRetard']);
        Route::get('recentes', [EvaluationController::class, 'recentes']);
        Route::get('statistiques', [EvaluationController::class, 'statistiques']);
        Route::get('rechercher', [EvaluationController::class, 'rechercher']);
        Route::get('{evaluation}', [EvaluationController::class, 'show']);
        Route::put('{evaluation}', [EvaluationController::class, 'update']);
        Route::delete('{evaluation}', [EvaluationController::class, 'destroy']);
        
        // Actions sur les évaluations
        Route::post('{evaluation}/terminer', [EvaluationController::class, 'terminer']);
        Route::post('{evaluation}/valider', [EvaluationController::class, 'valider']);
        Route::post('{evaluation}/ajouter-resultats', [EvaluationController::class, 'ajouterResultats']);
        Route::post('{evaluation}/ajouter-recommandation', [EvaluationController::class, 'ajouterRecommandation']);
        
        // Évaluations par entité
        Route::get('candidat/{candidat}', [EvaluationController::class, 'evaluationsCandidat']);
        Route::get('employe/{employe}', [EvaluationController::class, 'evaluationsEmploye']);
    });

    // Offres d'emploi
    Route::group(['prefix' => 'offres-emploi'], function () {
        Route::get('', [OffreEmploiController::class, 'index']);
        Route::post('', [OffreEmploiController::class, 'store']);
        Route::get('actives', [OffreEmploiController::class, 'actives']);
        Route::get('publiees', [OffreEmploiController::class, 'publiees']);
        Route::get('urgentes', [OffreEmploiController::class, 'urgentes']);
        Route::get('sponsorisees', [OffreEmploiController::class, 'sponsorisees']);
        Route::get('recentes', [OffreEmploiController::class, 'recentes']);
        Route::get('expirees', [OffreEmploiController::class, 'expirees']);
        Route::get('non-expirees', [OffreEmploiController::class, 'nonExpirees']);
        Route::get('statistiques', [OffreEmploiController::class, 'statistiques']);
        Route::get('rechercher', [OffreEmploiController::class, 'rechercher']);
        Route::get('{offre_emploi}', [OffreEmploiController::class, 'show']);
        Route::put('{offre_emploi}', [OffreEmploiController::class, 'update']);
        Route::delete('{offre_emploi}', [OffreEmploiController::class, 'destroy']);
        
        // Actions sur les offres d'emploi
        Route::post('{offre_emploi}/publier', [OffreEmploiController::class, 'publier']);
        Route::post('{offre_emploi}/archiver', [OffreEmploiController::class, 'archiver']);
        Route::post('{offre_emploi}/terminer', [OffreEmploiController::class, 'terminer']);
        Route::post('{offre_emploi}/marquer-urgente', [OffreEmploiController::class, 'marquerUrgente']);
        Route::post('{offre_emploi}/marquer-sponsorisee', [OffreEmploiController::class, 'marquerSponsorisee']);
        
        // Relations
        Route::get('{offre_emploi}/candidatures', [OffreEmploiController::class, 'candidatures']);
        Route::get('{offre_emploi}/evaluations', [OffreEmploiController::class, 'evaluations']);
    });

});
