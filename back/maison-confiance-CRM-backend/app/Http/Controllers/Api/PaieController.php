<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Paie;
use App\Models\Employe;
use App\Http\Resources\PaieResource;
use App\Http\Requests\StorePaieRequest;
use App\Http\Requests\UpdatePaieRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class PaieController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Paie::with('employe');

        // Filtres
        if ($request->has('periode')) {
            $query->periode($request->periode);
        }

        if ($request->has('statut')) {
            $query->statut($request->statut);
        }

        if ($request->has('employe_id')) {
            $query->employe($request->employe_id);
        }

        $paies = $query->orderBy('date_paiement', 'desc')->get();
        return PaieResource::collection($paies);
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
    public function store(StorePaieRequest $request)
    {
        $data = $request->validated();

        // Calculer automatiquement le salaire brut si pas fourni
        if (!isset($data['salaire_brut'])) {
            $salaireHoraire = ($data['heures_travaillees'] ?? 0) * ($data['taux_horaire'] ?? 0);
            $data['salaire_brut'] = $data['salaire_base'] + $salaireHoraire;
        }

        // Calculer automatiquement le salaire net si pas fourni
        if (!isset($data['salaire_net'])) {
            $totalDeductions = ($data['deductions'] ?? 0) + ($data['cotisations_sociales'] ?? 0) + ($data['impots'] ?? 0);
            $data['salaire_net'] = $data['salaire_brut'] + ($data['primes'] ?? 0) - $totalDeductions;
        }

        $paie = Paie::create($data);
        return new PaieResource($paie->load('employe'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Paie $paie)
    {
        return new PaieResource($paie->load('employe'));
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
    public function update(UpdatePaieRequest $request, Paie $paie)
    {
        $data = $request->validated();

        // Recalculer les montants si nécessaire
        if (isset($data['heures_travaillees']) || isset($data['taux_horaire']) || isset($data['salaire_base'])) {
            $heures = $data['heures_travaillees'] ?? $paie->heures_travaillees;
            $taux = $data['taux_horaire'] ?? $paie->taux_horaire;
            $base = $data['salaire_base'] ?? $paie->salaire_base;
            
            $salaireHoraire = $heures * $taux;
            $data['salaire_brut'] = $base + $salaireHoraire;
        }

        if (isset($data['salaire_brut']) || isset($data['primes']) || isset($data['deductions']) || 
            isset($data['cotisations_sociales']) || isset($data['impots'])) {
            
            $brut = $data['salaire_brut'] ?? $paie->salaire_brut;
            $primes = $data['primes'] ?? $paie->primes;
            $deductions = $data['deductions'] ?? $paie->deductions;
            $cotisations = $data['cotisations_sociales'] ?? $paie->cotisations_sociales;
            $impots = $data['impots'] ?? $paie->impots;
            
            $totalDeductions = $deductions + $cotisations + $impots;
            $data['salaire_net'] = $brut + $primes - $totalDeductions;
        }

        $paie->update($data);
        return new PaieResource($paie->load('employe'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Paie $paie)
    {
        $paie->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Marquer une paie comme payée
     */
    public function marquerPayee(Paie $paie)
    {
        $paie->marquerCommePayee();
        return response()->json([
            'message' => 'Paie marquée comme payée avec succès',
            'paie' => new PaieResource($paie->load('employe'))
        ]);
    }

    /**
     * Obtenir les paies d'un employé
     */
    public function paiesEmploye(Employe $employe)
    {
        $paies = $employe->paies()->orderBy('date_paiement', 'desc')->get();
        return PaieResource::collection($paies);
    }

    /**
     * Obtenir les paies en attente
     */
    public function paiesEnAttente()
    {
        $paies = Paie::with('employe')
            ->statut('en_attente')
            ->orderBy('date_paiement', 'asc')
            ->get();
        
        return PaieResource::collection($paies);
    }

    /**
     * Obtenir les paies payées
     */
    public function paiesPayees()
    {
        $paies = Paie::with('employe')
            ->statut('paye')
            ->orderBy('date_paiement', 'desc')
            ->get();
        
        return PaieResource::collection($paies);
    }

    /**
     * Obtenir les statistiques de paie
     */
    public function statistiques(Request $request)
    {
        $periode = $request->get('periode', now()->format('Y-m'));
        
        $stats = [
            'total_paies' => Paie::periode($periode)->count(),
            'total_salaire_brut' => Paie::periode($periode)->sum('salaire_brut'),
            'total_salaire_net' => Paie::periode($periode)->sum('salaire_net'),
            'total_primes' => Paie::periode($periode)->sum('primes'),
            'total_deductions' => Paie::periode($periode)->sum('deductions'),
            'total_cotisations' => Paie::periode($periode)->sum('cotisations_sociales'),
            'total_impots' => Paie::periode($periode)->sum('impots'),
            'paies_en_attente' => Paie::periode($periode)->statut('en_attente')->count(),
            'paies_payees' => Paie::periode($periode)->statut('paye')->count(),
        ];

        return response()->json($stats);
    }

    /**
     * Générer une fiche de paie
     */
    public function fichePaie(Paie $paie)
    {
        $paie->load('employe');
        
        $fiche = [
            'employe' => [
                'nom' => $paie->employe->nom . ' ' . $paie->employe->prenom,
                'email' => $paie->employe->email,
                'departement' => $paie->employe->departement->nom ?? 'Non assigné'
            ],
            'periode' => $paie->periode,
            'date_paiement' => $paie->date_paiement->format('d/m/Y'),
            'gains' => [
                'salaire_base' => $paie->salaire_base,
                'heures_travaillees' => $paie->heures_travaillees,
                'taux_horaire' => $paie->taux_horaire,
                'salaire_brut' => $paie->salaire_brut,
                'primes' => $paie->primes,
                'total_gains' => $paie->total_gains
            ],
            'deductions' => [
                'deductions' => $paie->deductions,
                'cotisations_sociales' => $paie->cotisations_sociales,
                'impots' => $paie->impots,
                'total_deductions' => $paie->total_deductions
            ],
            'salaire_net' => $paie->salaire_net,
            'statut' => $paie->statut,
            'mode_paiement' => $paie->mode_paiement
        ];

        return response()->json($fiche);
    }
}
