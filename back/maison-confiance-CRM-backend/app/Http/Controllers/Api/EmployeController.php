<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employe; // Importez le modèle
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response; // Pour les codes HTTP
use App\Http\Resources\EmployeResource; // Si vous utilisez les ressources
use App\Http\Requests\StoreEmployeRequest; // Si vous utilisez les Form Requests
use App\Http\Requests\UpdateEmployeRequest; // Si vous utilisez les Form Requests


class EmployeController extends Controller
{
    public function index()
    {
        try {
            $employes = Employe::with('departement')->get();
            return response()->json($employes);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la récupération des employés',
                'error' => $e->getMessage()
            ], 500);
        }
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

        $query = Employe::with('departement');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('prenom', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if (in_array($sort, ['nom', 'prenom', 'email', 'date_embauche', 'salaire', 'created_at']) && in_array($direction, ['asc', 'desc'])) {
            $query->orderBy($sort, $direction);
        } else {
            $query->orderBy('nom', 'asc');
        }

        $employes = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json($employes);
    }

    /**
     * Display all resources for kanban view.
     */
    public function all()
    {
        $employes = Employe::with('departement')->get();
        return response()->json($employes);
    }

    public function store(StoreEmployeRequest $request) // Utiliser Form Request
    {
        $employe = Employe::create($request->validated());
        return new EmployeResource($employe); // Ou response()->json($employe, Response::HTTP_CREATED);
    }

    public function show(Employe $employe) // Route Model Binding
    {
        return new EmployeResource($employe); // Ou response()->json($employe);
    }

    public function update(UpdateEmployeRequest $request, Employe $employe) // Utiliser Form Request et Route Model Binding
    {
        $employe->update($request->validated());
        return new EmployeResource($employe); // Ou response()->json($employe);
    }

    public function destroy(Employe $employe) // Route Model Binding
    {
        $employe->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}