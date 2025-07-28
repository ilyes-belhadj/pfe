<?php

namespace App\Http\Controllers\Api;

use App\Models\Absence;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AbsenceController extends Controller
{
    public function index()
    {
        return response()->json(Absence::with('employe')->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'employe_id' => 'required|exists:employes,id',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after_or_equal:date_debut',
            'motif' => 'required|string|max:255'
        ]);

        $absence = Absence::create($data);
        return response()->json($absence, 201);
    }

    public function show(Absence $absence)
    {
        return response()->json($absence->load('employe'));
    }

    public function update(Request $request, Absence $absence)
    {
        $data = $request->validate([
            'employe_id' => 'sometimes|exists:employes,id',
            'date_debut' => 'sometimes|date',
            'date_fin' => 'sometimes|date|after_or_equal:date_debut',
            'motif' => 'sometimes|string|max:255'
        ]);

        $absence->update($data);
        return response()->json($absence);
    }

    public function destroy(Absence $absence)
    {
        $absence->delete();
        return response()->noContent();
    }
}
