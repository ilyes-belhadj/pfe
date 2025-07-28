<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class UserController extends Controller

{


    public function index()
    {
        return response()->json(User::with('role')->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6',
            'role_id' => 'nullable|exists:roles,id'
        ]);

        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);

        return response()->json($user->load('role'), 201);
    }

    public function show(User $user)
    {
        return response()->json($user->load('role'));
    }

    public function update(Request $request, int $user_id)
    {
        $user = User::findOrFail($user_id);
        if (!$user) {
            return response()->json(['error' => "user not found"], 404);
        }
        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            //'email' => ['sometimes', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:6',
            'role_id' => 'nullable|exists:roles,id'
        ]);

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return response()->json($user->load('role'));
    }

    public function destroy(User $user)
    {
        $user->delete();
        return response()->noContent();
    }

    public function all(Request $request)
    {
        $user = JWTAuth::parseToken()->toUser();
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
        $page = $request->query('page', 1);
        $perPage = $request->query('per_page', 5);
        $search = Str::lower($request->query('search', ''));
        $roles = isset($request->roles) ? explode(',', $request->roles) : null;
        $sort = $request->query('sort', 'created_at');
        $direction = $request->query('direction', 'desc');

        $query = User::query()->with(['role']);

        if ($roles) {
            $query->whereHas('role', function ($q) use ($roles) {
                $q->where(function ($query) use ($roles) {
                    foreach ($roles as $role) {
                        $query->orWhere('name', 'like', "%$role%");
                    }
                });
            });
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereRaw('lower(name) like ?', ["%{$search}%"])
                    ->orWhereRaw('lower(email) like ?', ["%{$search}%"]);
            });
        }

        if (in_array($sort, ['name', 'email']) && in_array($direction, ['asc', 'desc'])) {
            $query->orderBy($sort, $direction);
        } else {
            $query->orderBy('name', 'asc');
        }

        $users = $query->paginate($perPage, ['*'], 'page', $page);

        $defaultRoles = [
            'Commercial',
            'Directeur',
            'Metreur',
            'Administratif',
        ];

        $users->getCollection()->transform(function ($user) use ($roles, $defaultRoles) {
            $roleObj = $user->role;
            if ($roleObj) {
                $roleName = $roleObj->name;
                if ($roles) {
                    foreach ($roles as $requestedRole) {
                        if (stripos($roleName, $requestedRole) !== false) {
                            $roleObj->name = $requestedRole;
                            break;
                        }
                    }
                } else {
                    foreach ($defaultRoles as $defaultRole) {
                        if (stripos($roleName, $defaultRole) !== false) {
                            $roleObj->name = $defaultRole;
                            break;
                        }
                    }
                }
            }
            return $user;
        });


        return response()->json($users, 200);
    }

    public function verifyUserExistantt(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $email = $request->email;
        $user = User::where('email', $email)->first();

        if ($user) {
            return response()->json(['result' => 'Cette adresse e-mail est déjà utilisée !'], 200);
        } else {
            return response()->json(['result' => 'Adresse e-mail valide !'], 200);
        }
    }

        public function onToggleChangetat(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:users,id'
        ]);

        $user = User::findOrFail($request->input('id'));
        
        // Toggle the active status
        $user->active = !$user->active;
        $user->save();
        
        return response()->json([
            'success' => true, 
            'message' => 'User status updated successfully',
            'user' => $user->load('role')
        ], 200);
    }
}
