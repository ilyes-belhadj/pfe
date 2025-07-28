<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Departement; // Importez le modèle Departement
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Resources\DepartementResource; // À créer à l'étape 5
use App\Http\Requests\StoreDepartementRequest;
 // À créer à l'étape 4
use App\Http\Requests\UpdateDepartementRequest; // À créer à l'étape 4
use Illuminate\Support\Facades\Log;

class DepartementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Log::alert('DepartementController: Index method called', [
            'ip' => request()->ip(),
            'url' => request()->fullUrl(),
            'method' => request()->method(),
        ]);
        $departements = Departement::all();
        // Optionnel: charger les employés liés si nécessaire, par ex: Departement::with('employes')->get();
        return DepartementResource::collection($departements);
    }

    /**
     * Display a paginated listing of the resource.
     */
    public function allPaginated(Request $request)
    {
        $page = $request->query('page', 1);
        $perPage = $request->query('per_page', 10);
        $search = $request->query('search', '');
        $sort = $request->query('sort', 'nom');
        $direction = $request->query('direction', 'asc');

        $query = Departement::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if (in_array($sort, ['nom', 'description', 'created_at']) && in_array($direction, ['asc', 'desc'])) {
            $query->orderBy($sort, $direction);
        } else {
            $query->orderBy('nom', 'asc');
        }

        $departements = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json($departements);
    }

    /**
     * Display all resources for kanban view.
     */
    public function all()
    {
        $departements = Departement::with('employes')->get();
        return response()->json($departements);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDepartementRequest $request)
    {
        $departement = Departement::create($request->validated());
        return new DepartementResource($departement);
    }

    /**
     * Display the specified resource.
     */
    public function show(Departement $departement) // Route Model Binding
    {
        // Optionnel: charger les employés liés si nécessaire, par ex: $departement->load('employes');
        return new DepartementResource($departement);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDepartementRequest $request, Departement $departement) // Route Model Binding
    {
        $departement->update($request->validated());
        return new DepartementResource($departement);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Departement $departement) // Route Model Binding
    {
        // Avant de supprimer un département, vous pourriez vouloir gérer
        // les employés qui y sont rattachés (par ex: les assigner à null ou à un département par défaut).
        // La contrainte `onDelete('set null')` dans la migration d'Employe gère cela pour departement_id.
        $departement->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}