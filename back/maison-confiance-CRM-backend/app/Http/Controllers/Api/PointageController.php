<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pointage;
use App\Models\Employe;
use App\Http\Resources\PointageResource;
use App\Http\Requests\StorePointageRequest;
use App\Http\Requests\UpdatePointageRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;

class PointageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Pointage::with(['employe', 'validePar']);

        // Filtres
        if ($request->has('employe_id')) {
            $query->employe($request->employe_id);
        }

        if ($request->has('date')) {
            $query->date($request->date);
        }

        if ($request->has('debut') && $request->has('fin')) {
            $query->periode($request->debut, $request->fin);
        }

        if ($request->has('statut')) {
            $query->statut($request->statut);
        }

        if ($request->has('valide')) {
            $query->valide($request->boolean('valide'));
        }

        // Filtres spéciaux
        if ($request->has('aujourdhui') && $request->boolean('aujourdhui')) {
            $query->aujourdhui();
        }

        if ($request->has('cette_semaine') && $request->boolean('cette_semaine')) {
            $query->cetteSemaine();
        }

        if ($request->has('ce_mois') && $request->boolean('ce_mois')) {
            $query->ceMois();
        }

        $pointages = $query->orderBy('date_pointage', 'desc')
                          ->orderBy('heure_entree', 'desc')
                          ->get();

        return PointageResource::collection($pointages);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePointageRequest $request)
    {
        $data = $request->validated();

        // Calculer automatiquement les heures si pas fournies
        if (!isset($data['heures_travaillees']) && isset($data['heure_entree']) && isset($data['heure_sortie'])) {
            $entree = Carbon::parse($data['date_pointage'] . ' ' . $data['heure_entree']);
            $sortie = Carbon::parse($data['date_pointage'] . ' ' . $data['heure_sortie']);
            $data['heures_travaillees'] = $entree->diffInHours($sortie, true);
        }

        if (!isset($data['heures_pause']) && isset($data['heure_pause_debut']) && isset($data['heure_pause_fin'])) {
            $pause_debut = Carbon::parse($data['date_pointage'] . ' ' . $data['heure_pause_debut']);
            $pause_fin = Carbon::parse($data['date_pointage'] . ' ' . $data['heure_pause_fin']);
            $data['heures_pause'] = $pause_debut->diffInHours($pause_fin, true);
        }

        if (!isset($data['heures_net'])) {
            $heures_travaillees = $data['heures_travaillees'] ?? 0;
            $heures_pause = $data['heures_pause'] ?? 0;
            $data['heures_net'] = $heures_travaillees - $heures_pause;
        }

        // Ajouter les informations de géolocalisation si disponibles
        if ($request->has('latitude') && $request->has('longitude')) {
            $data['latitude'] = $request->latitude;
            $data['longitude'] = $request->longitude;
        }

        // Ajouter les informations de connexion
        $data['ip_address'] = $request->ip();
        $data['user_agent'] = $request->userAgent();

        $pointage = Pointage::create($data);
        return new PointageResource($pointage->load(['employe', 'validePar']));
    }

    /**
     * Display the specified resource.
     */
    public function show(Pointage $pointage)
    {
        return new PointageResource($pointage->load(['employe', 'validePar']));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePointageRequest $request, Pointage $pointage)
    {
        $data = $request->validated();

        // Recalculer les heures si nécessaire
        if (isset($data['heure_entree']) || isset($data['heure_sortie'])) {
            $heure_entree = $data['heure_entree'] ?? $pointage->heure_entree;
            $heure_sortie = $data['heure_sortie'] ?? $pointage->heure_sortie;
            
            if ($heure_entree && $heure_sortie) {
                $entree = Carbon::parse($data['date_pointage'] ?? $pointage->date_pointage . ' ' . $heure_entree);
                $sortie = Carbon::parse($data['date_pointage'] ?? $pointage->date_pointage . ' ' . $heure_sortie);
                $data['heures_travaillees'] = $entree->diffInHours($sortie, true);
            }
        }

        if (isset($data['heure_pause_debut']) || isset($data['heure_pause_fin'])) {
            $pause_debut = $data['heure_pause_debut'] ?? $pointage->heure_pause_debut;
            $pause_fin = $data['heure_pause_fin'] ?? $pointage->heure_pause_fin;
            
            if ($pause_debut && $pause_fin) {
                $pause_debut_time = Carbon::parse($data['date_pointage'] ?? $pointage->date_pointage . ' ' . $pause_debut);
                $pause_fin_time = Carbon::parse($data['date_pointage'] ?? $pointage->date_pointage . ' ' . $pause_fin);
                $data['heures_pause'] = $pause_debut_time->diffInHours($pause_fin_time, true);
            }
        }

        if (isset($data['heures_travaillees']) || isset($data['heures_pause'])) {
            $heures_travaillees = $data['heures_travaillees'] ?? $pointage->heures_travaillees;
            $heures_pause = $data['heures_pause'] ?? $pointage->heures_pause;
            $data['heures_net'] = $heures_travaillees - $heures_pause;
        }

        $pointage->update($data);
        return new PointageResource($pointage->load(['employe', 'validePar']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pointage $pointage)
    {
        $pointage->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Pointage d'entrée
     */
    public function entree(Request $request)
    {
        $request->validate([
            'employe_id' => 'required|exists:employes,id',
            'lieu_pointage' => 'nullable|string',
            'commentaire' => 'nullable|string',
        ]);

        $data = [
            'employe_id' => $request->employe_id,
            'date_pointage' => now()->toDateString(),
            'heure_entree' => now()->format('H:i:s'),
            'statut' => 'present',
            'lieu_pointage' => $request->lieu_pointage ?? 'bureau',
            'commentaire' => $request->commentaire,
            'methode_pointage' => 'application',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ];

        // Ajouter géolocalisation si disponible
        if ($request->has('latitude') && $request->has('longitude')) {
            $data['latitude'] = $request->latitude;
            $data['longitude'] = $request->longitude;
        }

        $pointage = Pointage::create($data);
        return new PointageResource($pointage->load('employe'));
    }

    /**
     * Pointage de sortie
     */
    public function sortie(Request $request)
    {
        $request->validate([
            'employe_id' => 'required|exists:employes,id',
            'commentaire' => 'nullable|string',
        ]);

        $pointage = Pointage::where('employe_id', $request->employe_id)
                           ->where('date_pointage', now()->toDateString())
                           ->whereNull('heure_sortie')
                           ->first();

        if (!$pointage) {
            return response()->json([
                'message' => 'Aucun pointage d\'entrée trouvé pour aujourd\'hui'
            ], 404);
        }

        $pointage->update([
            'heure_sortie' => now()->format('H:i:s'),
            'heures_travaillees' => $pointage->calculerHeuresTravaillees(),
            'heures_net' => $pointage->calculerHeuresTravaillees() - $pointage->heures_pause,
            'commentaire' => $request->commentaire ? $pointage->commentaire . ' | ' . $request->commentaire : $pointage->commentaire,
        ]);

        return new PointageResource($pointage->load('employe'));
    }

    /**
     * Début de pause
     */
    public function debutPause(Request $request)
    {
        $request->validate([
            'employe_id' => 'required|exists:employes,id',
            'commentaire' => 'nullable|string',
        ]);

        $pointage = Pointage::where('employe_id', $request->employe_id)
                           ->where('date_pointage', now()->toDateString())
                           ->whereNotNull('heure_entree')
                           ->whereNull('heure_sortie')
                           ->first();

        if (!$pointage) {
            return response()->json([
                'message' => 'Aucun pointage actif trouvé'
            ], 404);
        }

        $pointage->update([
            'heure_pause_debut' => now()->format('H:i:s'),
            'statut' => 'en_pause',
            'commentaire' => $request->commentaire ? $pointage->commentaire . ' | Pause: ' . $request->commentaire : $pointage->commentaire . ' | Pause',
        ]);

        return new PointageResource($pointage->load('employe'));
    }

    /**
     * Fin de pause
     */
    public function finPause(Request $request)
    {
        $request->validate([
            'employe_id' => 'required|exists:employes,id',
            'commentaire' => 'nullable|string',
        ]);

        $pointage = Pointage::where('employe_id', $request->employe_id)
                           ->where('date_pointage', now()->toDateString())
                           ->whereNotNull('heure_pause_debut')
                           ->whereNull('heure_pause_fin')
                           ->first();

        if (!$pointage) {
            return response()->json([
                'message' => 'Aucune pause active trouvée'
            ], 404);
        }

        $pointage->update([
            'heure_pause_fin' => now()->format('H:i:s'),
            'heures_pause' => $pointage->calculerHeuresPause(),
            'heures_net' => $pointage->heures_travaillees - $pointage->calculerHeuresPause(),
            'statut' => 'present',
            'commentaire' => $request->commentaire ? $pointage->commentaire . ' | Fin pause: ' . $request->commentaire : $pointage->commentaire . ' | Fin pause',
        ]);

        return new PointageResource($pointage->load('employe'));
    }

    /**
     * Valider un pointage
     */
    public function valider(Pointage $pointage)
    {
        $user = \App\Models\User::first();
        $pointage->marquerValide($user);
        return response()->json([
            'message' => 'Pointage validé avec succès',
            'pointage' => new PointageResource($pointage->load(['employe', 'validePar']))
        ]);
    }

    /**
     * Invalider un pointage
     */
    public function invalider(Pointage $pointage)
    {
        $pointage->marquerNonValide();
        return response()->json([
            'message' => 'Pointage invalidé avec succès',
            'pointage' => new PointageResource($pointage->load(['employe', 'validePar']))
        ]);
    }

    /**
     * Obtenir les pointages d'un employé
     */
    public function pointagesEmploye(Employe $employe)
    {
        $pointages = $employe->pointages()
                            ->with('validePar')
                            ->orderBy('date_pointage', 'desc')
                            ->orderBy('heure_entree', 'desc')
                            ->get();

        return PointageResource::collection($pointages);
    }

    /**
     * Obtenir les pointages du jour
     */
    public function aujourdhui()
    {
        try {
            $pointages = Pointage::with(['employe', 'validePar'])
                                ->where('date_pointage', now()->toDateString())
                                ->orderBy('heure_entree', 'asc')
                                ->get();

            return PointageResource::collection($pointages);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la récupération des pointages du jour',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtenir les pointages non validés
     */
    public function nonValides()
    {
        try {
            $pointages = Pointage::with(['employe', 'validePar'])
                                ->where('valide', false)
                                ->orderBy('date_pointage', 'desc')
                                ->orderBy('heure_entree', 'desc')
                                ->get();

            return PointageResource::collection($pointages);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la récupération des pointages non validés',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtenir les statistiques de pointage
     */
    public function statistiques(Request $request)
    {
        try {
            $debut = $request->get('debut', now()->startOfMonth()->toDateString());
            $fin = $request->get('fin', now()->endOfMonth()->toDateString());
            $employe_id = $request->get('employe_id');

            $query = Pointage::whereBetween('date_pointage', [$debut, $fin]);
            
            if ($employe_id) {
                $query->where('employe_id', $employe_id);
            }

            $stats = [
                'total_pointages' => $query->count(),
                'total_heures_travaillees' => $query->sum('heures_travaillees') ?? 0,
                'total_heures_pause' => $query->sum('heures_pause') ?? 0,
                'total_heures_net' => $query->sum('heures_net') ?? 0,
                'pointages_valides' => $query->where('valide', true)->count(),
                'pointages_non_valides' => $query->where('valide', false)->count(),
                'presents' => $query->where('statut', 'present')->count(),
                'absents' => $query->where('statut', 'absent')->count(),
                'retards' => $query->where('statut', 'retard')->count(),
                'departs_anticipes' => $query->where('statut', 'depart_anticipé')->count(),
            ];

            return response()->json($stats);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la récupération des statistiques',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtenir le pointage actuel d'un employé
     */
    public function pointageActuel(Request $request)
    {
        try {
            // Si aucun employe_id n'est fourni, retourner un message d'erreur
            if (!$request->has('employe_id')) {
                return response()->json([
                    'message' => 'employe_id est requis'
                ], 400);
            }

            $request->validate([
                'employe_id' => 'required|exists:employes,id',
            ]);

            $pointage = Pointage::where('employe_id', $request->employe_id)
                               ->where('date_pointage', now()->toDateString())
                               ->whereNotNull('heure_entree')
                               ->whereNull('heure_sortie')
                               ->first();

            if (!$pointage) {
                return response()->json([
                    'message' => 'Aucun pointage actuel trouvé'
                ], 404);
            }

            return new PointageResource($pointage->load('employe'));
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la récupération du pointage actuel',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
