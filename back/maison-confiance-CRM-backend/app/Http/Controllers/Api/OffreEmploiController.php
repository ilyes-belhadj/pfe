<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OffreEmploi;
use App\Http\Resources\OffreEmploiResource;
use App\Http\Requests\StoreOffreEmploiRequest;
use App\Http\Requests\UpdateOffreEmploiRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class OffreEmploiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = OffreEmploi::with(['departement', 'recruteur', 'manager']);

        // Filtres
        if ($request->has('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->has('publiee')) {
            $query->where('publiee', $request->boolean('publiee'));
        }

        if ($request->has('urgente')) {
            $query->where('urgente', $request->boolean('urgente'));
        }

        if ($request->has('type_contrat')) {
            $query->typeContrat($request->type_contrat);
        }

        if ($request->has('niveau_experience')) {
            $query->niveauExperience($request->niveau_experience);
        }

        if ($request->has('lieu_travail')) {
            $query->lieuTravail($request->lieu_travail);
        }

        if ($request->has('departement_id')) {
            $query->departement($request->departement_id);
        }

        if ($request->has('recruteur_id')) {
            $query->recruteur($request->recruteur_id);
        }

        if ($request->has('recherche')) {
            $query->recherche($request->recherche);
        }

        // Filtres spéciaux
        if ($request->has('active') && $request->boolean('active')) {
            $query->active();
        }

        if ($request->has('publiee') && $request->boolean('publiee')) {
            $query->publiee();
        }

        if ($request->has('urgente') && $request->boolean('urgente')) {
            $query->urgente();
        }

        if ($request->has('sponsorisee') && $request->boolean('sponsorisee')) {
            $query->sponsorisee();
        }

        if ($request->has('recente') && $request->boolean('recente')) {
            $query->recente();
        }

        if ($request->has('expiree') && $request->boolean('expiree')) {
            $query->expiree();
        }

        if ($request->has('non_expiree') && $request->boolean('non_expiree')) {
            $query->nonExpiree();
        }

        $offres = $query->orderBy('date_publication', 'desc')
                        ->get();

        return OffreEmploiResource::collection($offres);
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
    public function store(StoreOffreEmploiRequest $request)
    {
        $offre = OffreEmploi::create($request->validated());
        return new OffreEmploiResource($offre->load(['departement', 'recruteur', 'manager']));
    }

    /**
     * Display the specified resource.
     */
    public function show(OffreEmploi $offreEmploi)
    {
        // Incrémenter le nombre de vues
        $offreEmploi->incrementVues();
        
        return new OffreEmploiResource($offreEmploi->load(['departement', 'recruteur', 'manager']));
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
    public function update(UpdateOffreEmploiRequest $request, OffreEmploi $offreEmploi)
    {
        $offreEmploi->update($request->validated());
        return new OffreEmploiResource($offreEmploi->load(['departement', 'recruteur', 'manager']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(OffreEmploi $offreEmploi)
    {
        $offreEmploi->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Publier une offre d'emploi
     */
    public function publier(OffreEmploi $offreEmploi)
    {
        $offreEmploi->publier();
        
        return response()->json([
            'message' => 'Offre d\'emploi publiée avec succès',
            'offre' => new OffreEmploiResource($offreEmploi->load(['departement', 'recruteur', 'manager']))
        ]);
    }

    /**
     * Archiver une offre d'emploi
     */
    public function archiver(OffreEmploi $offreEmploi)
    {
        $offreEmploi->archiver();
        
        return response()->json([
            'message' => 'Offre d\'emploi archivée avec succès',
            'offre' => new OffreEmploiResource($offreEmploi->load(['departement', 'recruteur', 'manager']))
        ]);
    }

    /**
     * Terminer une offre d'emploi
     */
    public function terminer(OffreEmploi $offreEmploi)
    {
        $offreEmploi->terminer();
        
        return response()->json([
            'message' => 'Offre d\'emploi terminée avec succès',
            'offre' => new OffreEmploiResource($offreEmploi->load(['departement', 'recruteur', 'manager']))
        ]);
    }

    /**
     * Marquer comme urgente
     */
    public function marquerUrgente(OffreEmploi $offreEmploi)
    {
        $offreEmploi->update(['urgente' => true]);
        
        return response()->json([
            'message' => 'Offre d\'emploi marquée comme urgente',
            'offre' => new OffreEmploiResource($offreEmploi->load(['departement', 'recruteur', 'manager']))
        ]);
    }

    /**
     * Marquer comme sponsorisée
     */
    public function marquerSponsorisee(OffreEmploi $offreEmploi)
    {
        $offreEmploi->update(['sponsorisee' => true]);
        
        return response()->json([
            'message' => 'Offre d\'emploi marquée comme sponsorisée',
            'offre' => new OffreEmploiResource($offreEmploi->load(['departement', 'recruteur', 'manager']))
        ]);
    }

    /**
     * Obtenir les offres actives
     */
    public function actives()
    {
        $offres = OffreEmploi::with(['departement', 'recruteur', 'manager'])
                              ->active()
                              ->orderBy('date_publication', 'desc')
                              ->get();

        return OffreEmploiResource::collection($offres);
    }

    /**
     * Obtenir les offres publiées
     */
    public function publiees()
    {
        $offres = OffreEmploi::with(['departement', 'recruteur', 'manager'])
                              ->publiee()
                              ->orderBy('date_publication', 'desc')
                              ->get();

        return OffreEmploiResource::collection($offres);
    }

    /**
     * Obtenir les offres urgentes
     */
    public function urgentes()
    {
        $offres = OffreEmploi::with(['departement', 'recruteur', 'manager'])
                              ->urgente()
                              ->orderBy('date_publication', 'desc')
                              ->get();

        return OffreEmploiResource::collection($offres);
    }

    /**
     * Obtenir les offres sponsorisées
     */
    public function sponsorisees()
    {
        $offres = OffreEmploi::with(['departement', 'recruteur', 'manager'])
                              ->sponsorisee()
                              ->orderBy('date_publication', 'desc')
                              ->get();

        return OffreEmploiResource::collection($offres);
    }

    /**
     * Obtenir les offres récentes
     */
    public function recentes()
    {
        $offres = OffreEmploi::with(['departement', 'recruteur', 'manager'])
                              ->recente()
                              ->orderBy('date_publication', 'desc')
                              ->get();

        return OffreEmploiResource::collection($offres);
    }

    /**
     * Obtenir les offres expirées
     */
    public function expirees()
    {
        $offres = OffreEmploi::with(['departement', 'recruteur', 'manager'])
                              ->expiree()
                              ->orderBy('date_limite_candidature', 'asc')
                              ->get();

        return OffreEmploiResource::collection($offres);
    }

    /**
     * Obtenir les offres non expirées
     */
    public function nonExpirees()
    {
        $offres = OffreEmploi::with(['departement', 'recruteur', 'manager'])
                              ->nonExpiree()
                              ->orderBy('date_publication', 'desc')
                              ->get();

        return OffreEmploiResource::collection($offres);
    }

    /**
     * Obtenir les statistiques des offres d'emploi
     */
    public function statistiques()
    {
        $stats = [
            'total_offres' => OffreEmploi::count(),
            'offres_actives' => OffreEmploi::active()->count(),
            'offres_publiees' => OffreEmploi::publiee()->count(),
            'offres_urgentes' => OffreEmploi::urgente()->count(),
            'offres_sponsorisees' => OffreEmploi::sponsorisee()->count(),
            'offres_recentes' => OffreEmploi::recente()->count(),
            'offres_expirees' => OffreEmploi::expiree()->count(),
            'offres_non_expirees' => OffreEmploi::nonExpiree()->count(),
            'par_statut' => [
                'brouillon' => OffreEmploi::where('statut', 'brouillon')->count(),
                'active' => OffreEmploi::where('statut', 'active')->count(),
                'en_cours' => OffreEmploi::where('statut', 'en_cours')->count(),
                'terminee' => OffreEmploi::where('statut', 'terminee')->count(),
                'archivee' => OffreEmploi::where('statut', 'archivee')->count(),
            ],
            'par_type_contrat' => [
                'CDI' => OffreEmploi::typeContrat('CDI')->count(),
                'CDD' => OffreEmploi::typeContrat('CDD')->count(),
                'Stage' => OffreEmploi::typeContrat('Stage')->count(),
                'Alternance' => OffreEmploi::typeContrat('Alternance')->count(),
                'Freelance' => OffreEmploi::typeContrat('Freelance')->count(),
            ],
            'par_niveau_experience' => [
                'debutant' => OffreEmploi::niveauExperience('debutant')->count(),
                'intermediaire' => OffreEmploi::niveauExperience('intermediaire')->count(),
                'confirme' => OffreEmploi::niveauExperience('confirme')->count(),
                'expert' => OffreEmploi::niveauExperience('expert')->count(),
            ]
        ];

        return response()->json($stats);
    }

    /**
     * Rechercher des offres d'emploi
     */
    public function rechercher(Request $request)
    {
        $request->validate([
            'term' => 'required|string|min:2'
        ]);

        $offres = OffreEmploi::with(['departement', 'recruteur', 'manager'])
                              ->recherche($request->term)
                              ->orderBy('date_publication', 'desc')
                              ->get();

        return OffreEmploiResource::collection($offres);
    }

    /**
     * Obtenir les candidatures d'une offre d'emploi
     */
    public function candidatures(OffreEmploi $offreEmploi)
    {
        $candidatures = $offreEmploi->candidatures()
                                    ->with(['candidat', 'departement', 'recruteur', 'manager'])
                                    ->orderBy('date_candidature', 'desc')
                                    ->get();

        return response()->json([
            'offre' => [
                'id' => $offreEmploi->id,
                'titre' => $offreEmploi->titre,
                'reference' => $offreEmploi->reference
            ],
            'candidatures' => $candidatures
        ]);
    }

    /**
     * Obtenir les évaluations d'une offre d'emploi
     */
    public function evaluations(OffreEmploi $offreEmploi)
    {
        $evaluations = $offreEmploi->evaluations()
                                   ->with(['evaluateur', 'manager', 'departement'])
                                   ->orderBy('date_evaluation', 'desc')
                                   ->get();

        return response()->json([
            'offre' => [
                'id' => $offreEmploi->id,
                'titre' => $offreEmploi->titre,
                'reference' => $offreEmploi->reference
            ],
            'evaluations' => $evaluations
        ]);
    }
}
