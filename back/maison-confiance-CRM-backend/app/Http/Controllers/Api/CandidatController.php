<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Candidat;
use App\Http\Resources\CandidatResource;
use App\Http\Requests\StoreCandidatRequest;
use App\Http\Requests\UpdateCandidatRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class CandidatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Candidat::with(['candidatures']);

        // Filtres
        if ($request->has('statut')) {
            $query->statut($request->statut);
        }

        if ($request->has('source')) {
            $query->source($request->source);
        }

        if ($request->has('recherche')) {
            $query->recherche($request->recherche);
        }

        // Filtres spéciaux
        if ($request->has('actifs') && $request->boolean('actifs')) {
            $query->actifs();
        }

        if ($request->has('recents') && $request->boolean('recents')) {
            $query->recents();
        }

        $candidats = $query->orderBy('nom', 'asc')
                           ->orderBy('prenom', 'asc')
                           ->get();

        return CandidatResource::collection($candidats);
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
    public function store(StoreCandidatRequest $request)
    {
        $candidat = Candidat::create($request->validated());
        return new CandidatResource($candidat->load('candidatures'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Candidat $candidat)
    {
        return new CandidatResource($candidat->load(['candidatures.departement', 'candidatures.recruteur']));
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
    public function update(UpdateCandidatRequest $request, Candidat $candidat)
    {
        $candidat->update($request->validated());
        return new CandidatResource($candidat->load('candidatures'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Candidat $candidat)
    {
        $candidat->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Obtenir les candidats actifs
     */
    public function actifs()
    {
        $candidats = Candidat::with('candidatures')
                             ->actifs()
                             ->orderBy('nom', 'asc')
                             ->orderBy('prenom', 'asc')
                             ->get();

        return CandidatResource::collection($candidats);
    }

    /**
     * Obtenir les candidats récents
     */
    public function recents()
    {
        $candidats = Candidat::with('candidatures')
                             ->recents()
                             ->orderBy('created_at', 'desc')
                             ->get();

        return CandidatResource::collection($candidats);
    }

    /**
     * Rechercher des candidats
     */
    public function rechercher(Request $request)
    {
        $request->validate([
            'term' => 'required|string|min:2'
        ]);

        $candidats = Candidat::with('candidatures')
                             ->recherche($request->term)
                             ->orderBy('nom', 'asc')
                             ->orderBy('prenom', 'asc')
                             ->get();

        return CandidatResource::collection($candidats);
    }

    /**
     * Obtenir les statistiques des candidats
     */
    public function statistiques()
    {
        $stats = [
            'total_candidats' => Candidat::count(),
            'candidats_actifs' => Candidat::actifs()->count(),
            'candidats_recents' => Candidat::recents()->count(),
            'candidats_blacklist' => Candidat::statut('blacklist')->count(),
            'par_source' => [
                'linkedin' => Candidat::source('LinkedIn')->count(),
                'indeed' => Candidat::source('Indeed')->count(),
                'spontanee' => Candidat::source('Spontanée')->count(),
                'autre' => Candidat::whereNotIn('source_recrutement', ['LinkedIn', 'Indeed', 'Spontanée'])->count(),
            ],
            'par_civilite' => [
                'hommes' => Candidat::where('civilite', 'M')->count(),
                'femmes' => Candidat::whereIn('civilite', ['Mme', 'Mlle'])->count(),
            ]
        ];

        return response()->json($stats);
    }

    /**
     * Marquer un candidat comme actif
     */
    public function marquerActif(Candidat $candidat)
    {
        $candidat->update(['statut' => 'actif']);
        return response()->json([
            'message' => 'Candidat marqué comme actif',
            'candidat' => new CandidatResource($candidat->load('candidatures'))
        ]);
    }

    /**
     * Marquer un candidat comme inactif
     */
    public function marquerInactif(Candidat $candidat)
    {
        $candidat->update(['statut' => 'inactif']);
        return response()->json([
            'message' => 'Candidat marqué comme inactif',
            'candidat' => new CandidatResource($candidat->load('candidatures'))
        ]);
    }

    /**
     * Blacklister un candidat
     */
    public function blacklister(Candidat $candidat)
    {
        $candidat->update(['statut' => 'blacklist']);
        return response()->json([
            'message' => 'Candidat blacklisté',
            'candidat' => new CandidatResource($candidat->load('candidatures'))
        ]);
    }

    /**
     * Obtenir les candidatures d'un candidat
     */
    public function candidatures(Candidat $candidat)
    {
        $candidatures = $candidat->candidatures()
                                 ->with(['departement', 'recruteur'])
                                 ->orderBy('date_candidature', 'desc')
                                 ->get();

        return response()->json([
            'candidat' => new CandidatResource($candidat),
            'candidatures' => $candidatures
        ]);
    }

    /**
     * Obtenir les candidatures actives d'un candidat
     */
    public function candidaturesActives(Candidat $candidat)
    {
        $candidatures = $candidat->candidaturesActives()
                                 ->with(['departement', 'recruteur'])
                                 ->orderBy('date_candidature', 'desc')
                                 ->get();

        return response()->json([
            'candidat' => new CandidatResource($candidat),
            'candidatures' => $candidatures
        ]);
    }
}
