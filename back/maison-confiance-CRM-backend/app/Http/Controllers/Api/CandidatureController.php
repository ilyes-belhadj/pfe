<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Candidature;
use App\Models\Candidat;
use App\Http\Resources\CandidatureResource;
use App\Http\Requests\StoreCandidatureRequest;
use App\Http\Requests\UpdateCandidatureRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Storage;

class CandidatureController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Candidature::with(['candidat', 'departement', 'recruteur', 'manager']);

        // Filtres
        if ($request->has('statut')) {
            $query->statut($request->statut);
        }

        if ($request->has('priorite')) {
            $query->priorite($request->priorite);
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
        if ($request->has('actives') && $request->boolean('actives')) {
            $query->actives();
        }

        if ($request->has('recents') && $request->boolean('recents')) {
            $query->recents();
        }

        if ($request->has('spontanees') && $request->boolean('spontanees')) {
            $query->spontanees();
        }

        $candidatures = $query->orderBy('date_candidature', 'desc')
                              ->get();

        return CandidatureResource::collection($candidatures);
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
    public function store(StoreCandidatureRequest $request)
    {
        $data = $request->validated();
        
        // Gérer l'upload du CV
        if ($request->hasFile('cv')) {
            $cvFile = $request->file('cv');
            $cvPath = $cvFile->store('cvs', 'public');
            
            $data['cv_path'] = $cvPath;
            $data['cv_filename'] = $cvFile->getClientOriginalName();
            $data['cv_mime_type'] = $cvFile->getMimeType();
            $data['cv_size'] = $cvFile->getSize();
        }

        // Gérer l'upload de la lettre de motivation
        if ($request->hasFile('lettre_motivation')) {
            $lettreFile = $request->file('lettre_motivation');
            $lettrePath = $lettreFile->store('lettres', 'public');
            
            $data['lettre_motivation_path'] = $lettrePath;
            $data['lettre_motivation_filename'] = $lettreFile->getClientOriginalName();
            $data['lettre_motivation_mime_type'] = $lettreFile->getMimeType();
            $data['lettre_motivation_size'] = $lettreFile->getSize();
        }

        $candidature = Candidature::create($data);
        return new CandidatureResource($candidature->load(['candidat', 'departement', 'recruteur']));
    }

    /**
     * Display the specified resource.
     */
    public function show(Candidature $candidature)
    {
        return new CandidatureResource($candidature->load(['candidat', 'departement', 'recruteur', 'manager']));
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
    public function update(UpdateCandidatureRequest $request, Candidature $candidature)
    {
        $data = $request->validated();
        
        // Gérer l'upload du CV
        if ($request->hasFile('cv')) {
            // Supprimer l'ancien CV s'il existe
            if ($candidature->cv_path) {
                Storage::disk('public')->delete($candidature->cv_path);
            }
            
            $cvFile = $request->file('cv');
            $cvPath = $cvFile->store('cvs', 'public');
            
            $data['cv_path'] = $cvPath;
            $data['cv_filename'] = $cvFile->getClientOriginalName();
            $data['cv_mime_type'] = $cvFile->getMimeType();
            $data['cv_size'] = $cvFile->getSize();
        }

        // Gérer l'upload de la lettre de motivation
        if ($request->hasFile('lettre_motivation')) {
            // Supprimer l'ancienne lettre s'il existe
            if ($candidature->lettre_motivation_path) {
                Storage::disk('public')->delete($candidature->lettre_motivation_path);
            }
            
            $lettreFile = $request->file('lettre_motivation');
            $lettrePath = $lettreFile->store('lettres', 'public');
            
            $data['lettre_motivation_path'] = $lettrePath;
            $data['lettre_motivation_filename'] = $lettreFile->getClientOriginalName();
            $data['lettre_motivation_mime_type'] = $lettreFile->getMimeType();
            $data['lettre_motivation_size'] = $lettreFile->getSize();
        }

        $candidature->update($data);
        return new CandidatureResource($candidature->load(['candidat', 'departement', 'recruteur']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Candidature $candidature)
    {
        // Supprimer les fichiers associés
        if ($candidature->cv_path) {
            Storage::disk('public')->delete($candidature->cv_path);
        }
        
        if ($candidature->lettre_motivation_path) {
            Storage::disk('public')->delete($candidature->lettre_motivation_path);
        }

        $candidature->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Télécharger le CV
     */
    public function telechargerCv(Candidature $candidature)
    {
        if (!$candidature->cv_path || !Storage::disk('public')->exists($candidature->cv_path)) {
            return response()->json(['message' => 'CV non trouvé'], 404);
        }

        $path = Storage::disk('public')->path($candidature->cv_path);
        $filename = $candidature->cv_filename ?? 'cv.pdf';
        
        return response()->download($path, $filename);
    }

    /**
     * Télécharger la lettre de motivation
     */
    public function telechargerLettre(Candidature $candidature)
    {
        if (!$candidature->lettre_motivation_path || !Storage::disk('public')->exists($candidature->lettre_motivation_path)) {
            return response()->json(['message' => 'Lettre de motivation non trouvée'], 404);
        }

        $path = Storage::disk('public')->path($candidature->lettre_motivation_path);
        $filename = $candidature->lettre_motivation_filename ?? 'lettre_motivation.pdf';
        
        return response()->download($path, $filename);
    }

    /**
     * Changer le statut d'une candidature
     */
    public function changerStatut(Request $request, Candidature $candidature)
    {
        $request->validate([
            'statut' => 'required|in:nouvelle,en_cours,entretien_telephone,entretien_rh,entretien_technique,entretien_final,test_technique,reference_check,offre_envoyee,offre_acceptee,embauche,refusee,annulee'
        ]);

        $candidature->changerStatut($request->statut);
        
        return response()->json([
            'message' => 'Statut mis à jour avec succès',
            'candidature' => new CandidatureResource($candidature->load(['candidat', 'departement', 'recruteur']))
        ]);
    }

    /**
     * Planifier un entretien
     */
    public function planifierEntretien(Request $request, Candidature $candidature)
    {
        $request->validate([
            'date_entretien' => 'required|date|after:today',
            'heure_entretien' => 'required|date_format:H:i',
            'lieu_entretien' => 'nullable|string|max:255',
            'notes' => 'nullable|string'
        ]);

        $candidature->update([
            'date_entretien' => $request->date_entretien,
            'heure_entretien' => $request->heure_entretien,
            'lieu_entretien' => $request->lieu_entretien,
            'notes_entretien' => $request->notes,
            'date_derniere_action' => now()
        ]);

        return response()->json([
            'message' => 'Entretien planifié avec succès',
            'candidature' => new CandidatureResource($candidature->load(['candidat', 'departement', 'recruteur']))
        ]);
    }

    /**
     * Ajouter une évaluation
     */
    public function evaluer(Request $request, Candidature $candidature)
    {
        $request->validate([
            'note_globale' => 'required|numeric|min:0|max:10',
            'evaluation' => 'nullable|string',
            'commentaires_rh' => 'nullable|string',
            'commentaires_technique' => 'nullable|string',
            'commentaires_manager' => 'nullable|string'
        ]);

        $candidature->update([
            'note_globale' => $request->note_globale,
            'evaluation' => $request->evaluation,
            'commentaires_rh' => $request->commentaires_rh,
            'commentaires_technique' => $request->commentaires_technique,
            'commentaires_manager' => $request->commentaires_manager,
            'date_derniere_action' => now()
        ]);

        return response()->json([
            'message' => 'Évaluation ajoutée avec succès',
            'candidature' => new CandidatureResource($candidature->load(['candidat', 'departement', 'recruteur']))
        ]);
    }

    /**
     * Obtenir les candidatures actives
     */
    public function actives()
    {
        $candidatures = Candidature::with(['candidat', 'departement', 'recruteur'])
                                   ->actives()
                                   ->orderBy('date_candidature', 'desc')
                                   ->get();

        return CandidatureResource::collection($candidatures);
    }

    /**
     * Obtenir les candidatures récentes
     */
    public function recents()
    {
        try {
            $candidatures = Candidature::with(['candidat', 'departement', 'recruteur'])
                ->where('created_at', '>=', now()->subDays(30))
                ->orderBy('created_at', 'desc')
                ->get();

            return CandidatureResource::collection($candidatures);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la récupération des candidatures récentes',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtenir les candidatures spontanées
     */
    public function spontanees()
    {
        try {
            $candidatures = Candidature::with(['candidat', 'departement', 'recruteur'])
                ->orderBy('date_candidature', 'desc')
                ->get();

            return CandidatureResource::collection($candidatures);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la récupération des candidatures spontanées',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtenir les statistiques des candidatures
     */
    public function statistiques()
    {
        try {
            $stats = [
                'total' => Candidature::count(),
                'actives' => Candidature::whereNotIn('statut', ['embauche', 'refusee', 'annulee'])->count(),
                'embauchees' => Candidature::where('statut', 'embauche')->count(),
                'refusees' => Candidature::where('statut', 'refusee')->count(),
                'annulees' => Candidature::where('statut', 'annulee')->count(),
                'en_attente' => Candidature::where('statut', 'en_attente')->count(),
                'en_cours' => Candidature::where('statut', 'en_cours')->count(),
                'recents' => Candidature::where('created_at', '>=', now()->subDays(30))->count(),
                'par_statut' => Candidature::select('statut', \DB::raw('count(*) as count'))
                    ->groupBy('statut')
                    ->get(),
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
     * Rechercher des candidatures
     */
    public function rechercher(Request $request)
    {
        $request->validate([
            'term' => 'required|string|min:2'
        ]);

        $candidatures = Candidature::with(['candidat', 'departement', 'recruteur'])
                                   ->recherche($request->term)
                                   ->orderBy('date_candidature', 'desc')
                                   ->get();

        return CandidatureResource::collection($candidatures);
    }
}
