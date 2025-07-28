<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Evaluation;
use App\Models\Candidat;
use App\Models\Employe;
use App\Http\Resources\EvaluationResource;
use App\Http\Requests\StoreEvaluationRequest;
use App\Http\Requests\UpdateEvaluationRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class EvaluationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Evaluation::with(['evaluable', 'evaluateur', 'manager', 'departement']);

        // Filtres
        if ($request->has('type')) {
            $query->type($request->type);
        }

        if ($request->has('statut')) {
            $query->statut($request->statut);
        }

        if ($request->has('priorite')) {
            $query->priorite($request->priorite);
        }

        if ($request->has('evaluateur_id')) {
            $query->evaluateur($request->evaluateur_id);
        }

        if ($request->has('departement_id')) {
            $query->departement($request->departement_id);
        }

        if ($request->has('recherche')) {
            $query->recherche($request->recherche);
        }

        // Filtres spéciaux
        if ($request->has('en_cours') && $request->boolean('en_cours')) {
            $query->enCours();
        }

        if ($request->has('terminees') && $request->boolean('terminees')) {
            $query->terminees();
        }

        if ($request->has('en_retard') && $request->boolean('en_retard')) {
            $query->enRetard();
        }

        if ($request->has('recentes') && $request->boolean('recentes')) {
            $query->recentes();
        }

        $evaluations = $query->orderBy('date_evaluation', 'desc')
                             ->get();

        return EvaluationResource::collection($evaluations);
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
    public function store(StoreEvaluationRequest $request)
    {
        // Debug: vérifier le contenu de la requête
        $hasCandidat = $request->has('candidat_id') && !is_null($request->candidat_id) && $request->candidat_id !== '';
        $hasEmploye = $request->has('employe_id') && !is_null($request->employe_id) && $request->employe_id !== '';
        
        // Validation préalable des champs evaluable
        if (!$hasCandidat && !$hasEmploye) {
            return response()->json([
                'message' => 'Validation échouée',
                'errors' => [
                    'evaluable' => ['Vous devez spécifier soit un candidat soit un employé.']
                ]
            ], 422);
        }
        
        if ($hasCandidat && $hasEmploye) {
            return response()->json([
                'message' => 'Validation échouée',
                'errors' => [
                    'evaluable' => ['Vous ne pouvez pas spécifier à la fois un candidat et un employé.']
                ]
            ], 422);
        }

        $data = $request->validated();
        
        // Déterminer le type d'évaluable - au moins un doit être fourni
        if ($hasCandidat) {
            $data['evaluable_type'] = Candidat::class;
            $data['evaluable_id'] = $request->candidat_id;
            // S'assurer que employe_id n'est pas dans les données
            unset($data['employe_id']);
        } elseif ($hasEmploye) {
            $data['evaluable_type'] = Employe::class;
            $data['evaluable_id'] = $request->employe_id;
            // S'assurer que candidat_id n'est pas dans les données
            unset($data['candidat_id']);
        }

        // Nettoyer les données pour éviter les champs inutiles
        unset($data['candidat_id'], $data['employe_id']);

        $evaluation = Evaluation::create($data);
        return new EvaluationResource($evaluation->load(['evaluable', 'evaluateur', 'manager', 'departement']));
    }

    /**
     * Display the specified resource.
     */
    public function show(Evaluation $evaluation)
    {
        return new EvaluationResource($evaluation->load(['evaluable', 'evaluateur', 'manager', 'departement']));
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
    public function update(UpdateEvaluationRequest $request, Evaluation $evaluation)
    {
        $evaluation->update($request->validated());
        return new EvaluationResource($evaluation->load(['evaluable', 'evaluateur', 'manager', 'departement']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Evaluation $evaluation)
    {
        $evaluation->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Terminer une évaluation
     */
    public function terminer(Evaluation $evaluation)
    {
        $evaluation->terminer();
        
        return response()->json([
            'message' => 'Évaluation terminée avec succès',
            'evaluation' => new EvaluationResource($evaluation->load(['evaluable', 'evaluateur', 'manager', 'departement']))
        ]);
    }

    /**
     * Valider une évaluation
     */
    public function valider(Request $request, Evaluation $evaluation)
    {
        $request->validate([
            'validateur' => 'required|in:evaluateur,manager,rh,evalue'
        ]);

        $evaluation->valider($request->validateur);
        
        return response()->json([
            'message' => 'Évaluation validée avec succès',
            'evaluation' => new EvaluationResource($evaluation->load(['evaluable', 'evaluateur', 'manager', 'departement']))
        ]);
    }

    /**
     * Ajouter des résultats d'évaluation
     */
    public function ajouterResultats(Request $request, Evaluation $evaluation)
    {
        $request->validate([
            'resultats' => 'required|array',
            'note_globale' => 'nullable|numeric|min:0|max:10',
            'note_competences' => 'nullable|numeric|min:0|max:10',
            'note_performance' => 'nullable|numeric|min:0|max:10',
            'note_comportement' => 'nullable|numeric|min:0|max:10',
            'note_potentiel' => 'nullable|numeric|min:0|max:10',
            'forces' => 'nullable|string',
            'axes_amelioration' => 'nullable|string',
            'objectifs' => 'nullable|string',
            'recommandations' => 'nullable|string',
            'commentaires_evaluateur' => 'nullable|string'
        ]);

        $evaluation->update([
            'resultats' => $request->resultats,
            'note_globale' => $request->note_globale,
            'note_competences' => $request->note_competences,
            'note_performance' => $request->note_performance,
            'note_comportement' => $request->note_comportement,
            'note_potentiel' => $request->note_potentiel,
            'forces' => $request->forces,
            'axes_amelioration' => $request->axes_amelioration,
            'objectifs' => $request->objectifs,
            'recommandations' => $request->recommandations,
            'commentaires_evaluateur' => $request->commentaires_evaluateur,
            'statut' => 'en_cours'
        ]);

        return response()->json([
            'message' => 'Résultats ajoutés avec succès',
            'evaluation' => new EvaluationResource($evaluation->load(['evaluable', 'evaluateur', 'manager', 'departement']))
        ]);
    }

    /**
     * Ajouter une recommandation
     */
    public function ajouterRecommandation(Request $request, Evaluation $evaluation)
    {
        $request->validate([
            'recommandation' => 'required|in:embauche,confirmation,promotion,formation,sanction,licenciement',
            'justification_recommandation' => 'required|string|max:1000'
        ]);

        $evaluation->update([
            'recommandation' => $request->recommandation,
            'justification_recommandation' => $request->justification_recommandation
        ]);

        return response()->json([
            'message' => 'Recommandation ajoutée avec succès',
            'evaluation' => new EvaluationResource($evaluation->load(['evaluable', 'evaluateur', 'manager', 'departement']))
        ]);
    }

    /**
     * Obtenir les évaluations en cours
     */
    public function enCours()
    {
        $evaluations = Evaluation::with(['evaluable', 'evaluateur', 'manager', 'departement'])
                                 ->enCours()
                                 ->orderBy('date_evaluation', 'desc')
                                 ->get();

        return EvaluationResource::collection($evaluations);
    }

    /**
     * Obtenir les évaluations terminées
     */
    public function terminees()
    {
        $evaluations = Evaluation::with(['evaluable', 'evaluateur', 'manager', 'departement'])
                                 ->terminees()
                                 ->orderBy('date_evaluation', 'desc')
                                 ->get();

        return EvaluationResource::collection($evaluations);
    }

    /**
     * Obtenir les évaluations en retard
     */
    public function enRetard()
    {
        $evaluations = Evaluation::with(['evaluable', 'evaluateur', 'manager', 'departement'])
                                 ->enRetard()
                                 ->orderBy('date_limite', 'asc')
                                 ->get();

        return EvaluationResource::collection($evaluations);
    }

    /**
     * Obtenir les évaluations récentes
     */
    public function recentes()
    {
        $evaluations = Evaluation::with(['evaluable', 'evaluateur', 'manager', 'departement'])
                                 ->recentes()
                                 ->orderBy('date_evaluation', 'desc')
                                 ->get();

        return EvaluationResource::collection($evaluations);
    }

    /**
     * Obtenir les statistiques des évaluations
     */
    public function statistiques()
    {
        $stats = [
            'total_evaluations' => Evaluation::count(),
            'evaluations_en_cours' => Evaluation::enCours()->count(),
            'evaluations_terminees' => Evaluation::terminees()->count(),
            'evaluations_en_retard' => Evaluation::enRetard()->count(),
            'evaluations_recents' => Evaluation::recentes()->count(),
            'par_type' => [
                'candidats' => Evaluation::type('candidat')->count(),
                'employes' => Evaluation::type('employe')->count(),
                'periode_essai' => Evaluation::type('periode_essai')->count(),
                'annuelles' => Evaluation::type('annuelle')->count(),
                'performance' => Evaluation::type('performance')->count(),
            ],
            'par_statut' => [
                'brouillon' => Evaluation::statut('brouillon')->count(),
                'en_cours' => Evaluation::statut('en_cours')->count(),
                'terminee' => Evaluation::statut('terminee')->count(),
                'validee' => Evaluation::statut('validee')->count(),
                'rejetee' => Evaluation::statut('rejetee')->count(),
            ],
            'par_priorite' => [
                'basse' => Evaluation::priorite('basse')->count(),
                'normale' => Evaluation::priorite('normale')->count(),
                'haute' => Evaluation::priorite('haute')->count(),
                'urgente' => Evaluation::priorite('urgente')->count(),
            ]
        ];

        return response()->json($stats);
    }

    /**
     * Rechercher des évaluations
     */
    public function rechercher(Request $request)
    {
        $request->validate([
            'term' => 'required|string|min:2'
        ]);

        $evaluations = Evaluation::with(['evaluable', 'evaluateur', 'manager', 'departement'])
                                 ->recherche($request->term)
                                 ->orderBy('date_evaluation', 'desc')
                                 ->get();

        return EvaluationResource::collection($evaluations);
    }

    /**
     * Obtenir les évaluations d'un candidat
     */
    public function evaluationsCandidat(Candidat $candidat)
    {
        $evaluations = $candidat->evaluations()
                                ->with(['evaluateur', 'manager', 'departement'])
                                ->orderBy('date_evaluation', 'desc')
                                ->get();

        return response()->json([
            'candidat' => [
                'id' => $candidat->id,
                'nom_complet' => $candidat->nom_complet,
                'email' => $candidat->email
            ],
            'evaluations' => EvaluationResource::collection($evaluations)
        ]);
    }

    /**
     * Obtenir les évaluations d'un employé
     */
    public function evaluationsEmploye(Employe $employe)
    {
        $evaluations = $employe->evaluations()
                               ->with(['evaluateur', 'manager', 'departement'])
                               ->orderBy('date_evaluation', 'desc')
                               ->get();

        return response()->json([
            'employe' => [
                'id' => $employe->id,
                'nom_complet' => $employe->nom . ' ' . $employe->prenom,
                'email' => $employe->email
            ],
            'evaluations' => EvaluationResource::collection($evaluations)
        ]);
    }
}
