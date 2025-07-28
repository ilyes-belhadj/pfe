<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Formation;
use App\Http\Resources\FormationResource;
use App\Http\Requests\StoreFormationRequest;
use App\Http\Requests\UpdateFormationRequest;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FormationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $formations = Formation::with('employes')->get();
        return FormationResource::collection($formations);
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
    public function store(StoreFormationRequest $request)
    {
        $formation = Formation::create($request->validated());
        return new FormationResource($formation->load('employes'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Formation $formation)
    {
        return new FormationResource($formation->load('employes'));
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
    public function update(UpdateFormationRequest $request, Formation $formation)
    {
        $formation->update($request->validated());
        return new FormationResource($formation->load('employes'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Formation $formation)
    {
        $formation->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Inscrire un employé à une formation
     */
    public function inscrireEmploye(Request $request, Formation $formation)
    {
        $request->validate([
            'employe_id' => 'required|exists:employes,id',
            'notes' => 'nullable|string'
        ]);

        // Vérifier si la formation est disponible
        if (!$formation->isAvailable()) {
            return response()->json([
                'message' => 'Cette formation n\'est pas disponible'
            ], 422);
        }

        // Vérifier si l'employé n'est pas déjà inscrit
        if ($formation->employes()->where('employe_id', $request->employe_id)->exists()) {
            return response()->json([
                'message' => 'Cet employé est déjà inscrit à cette formation'
            ], 422);
        }

        // Inscrire l'employé
        $formation->employes()->attach($request->employe_id, [
            'date_inscription' => now(),
            'notes' => $request->notes
        ]);

        // Incrémenter le nombre de places occupées
        $formation->increment('places_occupees');

        return response()->json([
            'message' => 'Employé inscrit avec succès'
        ]);
    }

    /**
     * Désinscrire un employé d'une formation
     */
    public function desinscrireEmploye(Request $request, Formation $formation)
    {
        $request->validate([
            'employe_id' => 'required|exists:employes,id'
        ]);

        // Vérifier si l'employé est inscrit
        if (!$formation->employes()->where('employe_id', $request->employe_id)->exists()) {
            return response()->json([
                'message' => 'Cet employé n\'est pas inscrit à cette formation'
            ], 422);
        }

        // Désinscrire l'employé
        $formation->employes()->detach($request->employe_id);

        // Décrémenter le nombre de places occupées
        $formation->decrement('places_occupees');

        return response()->json([
            'message' => 'Employé désinscrit avec succès'
        ]);
    }

    /**
     * Obtenir les formations disponibles
     */
    public function formationsDisponibles()
    {
        $formations = Formation::where('statut', 'planifie')
            ->whereRaw('places_occupees < nombre_places')
            ->with('employes')
            ->get();

        return FormationResource::collection($formations);
    }
}
