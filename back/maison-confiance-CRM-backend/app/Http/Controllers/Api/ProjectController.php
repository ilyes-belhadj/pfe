<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Log;

class ProjectController extends Controller
{
    // GET /api/projects
    public function index()
    {
        $projects = Project::all();

        return response()->json([
            'success' => true,
            'data' => $projects
        ]);
    }

    // POST /api/projects
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'client' => 'required|string',
            'email' => 'required|email',
            'phone' => 'required|string',
            'address' => 'required|string',
            'budget' => 'required|numeric',
            'status' => 'required|string',
            'progress' => 'required|integer',
        ]);

        $validated['id'] = (string) Str::uuid();

        $project = Project::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Project created successfully.',
            'data' => $project
        ], 201);
    }

    // GET /api/projects/{id}
    public function show($id)
    {
        $project = Project::find($id);

        if (!$project) {
            return response()->json([
                'success' => false,
                'message' => 'Project not found.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $project
        ]);
    }

    // PUT/PATCH /api/projects/{id}
    public function update(Request $request, $id)
    {
        $project = Project::find($id);

        if (!$project) {
            return response()->json([
                'success' => false,
                'message' => 'Project not found.'
            ], 404);
        }

        $validated = $request->validate([
            'title' => 'sometimes|required|string',
            'client' => 'sometimes|required|string',
            'email' => 'sometimes|required|email',
            'phone' => 'sometimes|required|string',
            'address' => 'sometimes|required|string',
            'budget' => 'sometimes|required|numeric',
            'status' => 'sometimes|required|string',
            'progress' => 'sometimes|required|integer',
            'redirect_to_plan_request' => 'sometimes|boolean',
            // You can add validation for other fields if you want strict validation.
        ]);

        $project->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Project updated successfully.',
            'data' => $project
        ]);
    }

    // DELETE /api/projects/{id}
    public function destroy($id)
    {
        $project = Project::find($id);

        if (!$project) {
            return response()->json([
                'success' => false,
                'message' => 'Project not found.'
            ], 404);
        }

        $project->delete();

        return response()->json([
            'success' => true,
            'message' => 'Project deleted successfully.'
        ]);
    }

    public function allPaginated(Request $request)
    {
        $user = JWTAuth::parseToken()->toUser();
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
        $page = $request->query('page', 1);
        $perPage = $request->query('per_page', 5);
        $search = Str::lower($request->query('search', ''));
        $sort = $request->query('sort', 'created_at');
        $direction = $request->query('direction', 'desc');

        $query = Project::query();


        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereRaw('lower(title) like ?', ["%{$search}%"])
                    ->orWhereRaw('lower(email) like ?', ["%{$search}%"]);
            });
        }

        if (in_array($sort, ['title', 'email']) && in_array($direction, ['asc', 'desc'])) {
            $query->orderBy($sort, $direction);
        } else {
            $query->orderBy('created_at', 'asc');
        }

        $projects = $query->paginate($perPage, ['*'], 'page', $page);
        return response()->json($projects, 200);
    }

    public function all(Request $request)
    {
        $user = JWTAuth::parseToken()->toUser();
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $projects = Project::all();

        return response()->json($projects, 200);
    }
}
