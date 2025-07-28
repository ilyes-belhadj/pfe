<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Employe;
use App\Models\Departement;
use App\Models\Absence;
use App\Models\Formation;
use App\Models\Paie;
use App\Models\Pointage;
use App\Models\Candidat;
use App\Models\Candidature;
use App\Models\Evaluation;
use App\Models\Project;
use App\Models\OffreEmploi;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            $data = [
                'users' => $this->getUsersStats(),
                'employees' => $this->getEmployeesStats(),
                'departments' => $this->getDepartmentsStats(),
                'absences' => $this->getAbsencesStats(),
                'formations' => $this->getFormationsStats(),
                'paies' => $this->getPaiesStats(),
                'pointages' => $this->getPointagesStats(),
                'candidats' => $this->getCandidatsStats(),
                'candidatures' => $this->getCandidaturesStats(),
                'evaluations' => $this->getEvaluationsStats(),
                'projects' => $this->getProjectsStats(),
                'offres_emploi' => $this->getOffresEmploiStats(),
                'general' => $this->getGeneralStats(),
            ];

            return response()->json([
                'success' => true,
                'message' => 'Statistiques du tableau de bord récupérées avec succès',
                'data' => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des statistiques: ' . $e->getMessage()
            ], 500);
        }
    }

    private function getUsersStats()
    {
        try {
            $total = User::count();
            $active = User::where('active', true)->count();
            $inactive = User::where('active', false)->count();
            $recent = User::where('created_at', '>=', now()->subDays(30))->count();

            return [
                'total' => $total,
                'active' => $active,
                'inactive' => $inactive,
                'recent' => $recent,
                'percentage_active' => $total > 0 ? round(($active / $total) * 100, 2) : 0
            ];
        } catch (\Exception $e) {
            return [
                'total' => 0,
                'active' => 0,
                'inactive' => 0,
                'recent' => 0,
                'percentage_active' => 0
            ];
        }
    }

    private function getEmployeesStats()
    {
        try {
            $total = Employe::count();
            $byDepartment = Employe::select('departement_id', DB::raw('count(*) as count'))
                ->groupBy('departement_id')
                ->get();

            return [
                'total' => $total,
                'by_department' => $byDepartment,
                'recent' => Employe::where('created_at', '>=', now()->subDays(30))->count()
            ];
        } catch (\Exception $e) {
            return [
                'total' => 0,
                'by_department' => [],
                'recent' => 0
            ];
        }
    }

    private function getDepartmentsStats()
    {
        try {
            $total = Departement::count();
            $withEmployees = Departement::has('employes')->count();
            $withoutEmployees = $total - $withEmployees;

            return [
                'total' => $total,
                'with_employees' => $withEmployees,
                'without_employees' => $withoutEmployees,
                'average_employees' => $total > 0 ? round(Employe::count() / $total, 2) : 0
            ];
        } catch (\Exception $e) {
            return [
                'total' => 0,
                'with_employees' => 0,
                'without_employees' => 0,
                'average_employees' => 0
            ];
        }
    }

    private function getAbsencesStats()
    {
        try {
            $total = Absence::count();
            $currentMonth = Absence::whereMonth('date_debut', now()->month)
                ->whereYear('date_debut', now()->year)
                ->count();
            $pending = Absence::where('date_fin', '>=', now())->count();

            return [
                'total' => $total,
                'current_month' => $currentMonth,
                'pending' => $pending,
                'by_motif' => Absence::select('motif', DB::raw('count(*) as count'))
                    ->groupBy('motif')
                    ->get()
            ];
        } catch (\Exception $e) {
            return [
                'total' => 0,
                'current_month' => 0,
                'pending' => 0,
                'by_motif' => []
            ];
        }
    }

    private function getFormationsStats()
    {
        try {
            $total = Formation::count();
            $active = Formation::where('statut', 'planifie')->count();
            $completed = Formation::where('statut', 'termine')->count();
            $cancelled = Formation::where('statut', 'annule')->count();

            return [
                'total' => $total,
                'active' => $active,
                'completed' => $completed,
                'cancelled' => $cancelled,
                'total_cost' => Formation::sum('cout') ?? 0,
                'average_cost' => $total > 0 ? round((Formation::sum('cout') ?? 0) / $total, 2) : 0
            ];
        } catch (\Exception $e) {
            return [
                'total' => 0,
                'active' => 0,
                'completed' => 0,
                'cancelled' => 0,
                'total_cost' => 0,
                'average_cost' => 0
            ];
        }
    }

    private function getPaiesStats()
    {
        try {
            $total = Paie::count();
            $paid = Paie::where('statut', 'paye')->count();
            $pending = Paie::where('statut', 'en_attente')->count();
            $totalAmount = Paie::sum('salaire_net') ?? 0;

            return [
                'total' => $total,
                'paid' => $paid,
                'pending' => $pending,
                'total_amount' => $totalAmount,
                'average_amount' => $total > 0 ? round($totalAmount / $total, 2) : 0,
                'by_period' => Paie::select('periode', DB::raw('count(*) as count'))
                    ->groupBy('periode')
                    ->get()
            ];
        } catch (\Exception $e) {
            return [
                'total' => 0,
                'paid' => 0,
                'pending' => 0,
                'total_amount' => 0,
                'average_amount' => 0,
                'by_period' => []
            ];
        }
    }

    private function getPointagesStats()
    {
        try {
            $total = Pointage::count();
            $today = Pointage::where('date_pointage', now()->toDateString())->count();
            $thisWeek = Pointage::whereBetween('date_pointage', [
                now()->startOfWeek()->toDateString(),
                now()->endOfWeek()->toDateString()
            ])->count();
            $validated = Pointage::where('valide', true)->count();

            return [
                'total' => $total,
                'today' => $today,
                'this_week' => $thisWeek,
                'validated' => $validated,
                'by_status' => Pointage::select('statut', DB::raw('count(*) as count'))
                    ->groupBy('statut')
                    ->get(),
                'total_hours' => Pointage::sum('heures_net') ?? 0
            ];
        } catch (\Exception $e) {
            return [
                'total' => 0,
                'today' => 0,
                'this_week' => 0,
                'validated' => 0,
                'by_status' => [],
                'total_hours' => 0
            ];
        }
    }

    private function getCandidatsStats()
    {
        try {
            $total = Candidat::count();
            $active = Candidat::where('statut', 'actif')->count();
            $inactive = Candidat::where('statut', 'inactif')->count();
            $blacklisted = Candidat::where('statut', 'blacklist')->count();
            $recent = Candidat::where('created_at', '>=', now()->subDays(30))->count();

            return [
                'total' => $total,
                'active' => $active,
                'inactive' => $inactive,
                'blacklisted' => $blacklisted,
                'recent' => $recent,
                'by_source' => Candidat::select('source_recrutement', DB::raw('count(*) as count'))
                    ->groupBy('source_recrutement')
                    ->get()
            ];
        } catch (\Exception $e) {
            return [
                'total' => 0,
                'active' => 0,
                'inactive' => 0,
                'blacklisted' => 0,
                'recent' => 0,
                'by_source' => []
            ];
        }
    }

    private function getCandidaturesStats()
    {
        try {
            $total = Candidature::count();
            $active = Candidature::whereNotIn('statut', ['embauche', 'refusee', 'annulee'])->count();
            $hired = Candidature::where('statut', 'embauche')->count();
            $rejected = Candidature::where('statut', 'refusee')->count();

            return [
                'total' => $total,
                'active' => $active,
                'hired' => $hired,
                'rejected' => $rejected,
                'by_status' => Candidature::select('statut', DB::raw('count(*) as count'))
                    ->groupBy('statut')
                    ->get(),
                'recent' => Candidature::where('created_at', '>=', now()->subDays(30))->count()
            ];
        } catch (\Exception $e) {
            return [
                'total' => 0,
                'active' => 0,
                'hired' => 0,
                'rejected' => 0,
                'by_status' => [],
                'recent' => 0
            ];
        }
    }

    private function getEvaluationsStats()
    {
        try {
            $total = Evaluation::count();
            $thisMonth = Evaluation::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count();

            return [
                'total' => $total,
                'this_month' => $thisMonth,
                'average_score' => $total > 0 ? round(Evaluation::avg('note') ?? 0, 2) : 0,
                'by_type' => Evaluation::select('type_evaluation', DB::raw('count(*) as count'))
                    ->groupBy('type_evaluation')
                    ->get()
            ];
        } catch (\Exception $e) {
            return [
                'total' => 0,
                'this_month' => 0,
                'average_score' => 0,
                'by_type' => []
            ];
        }
    }

    private function getProjectsStats()
    {
        try {
            $total = Project::count();
            $active = Project::where('status', 'en_cours')->count();
            $completed = Project::where('status', 'termine')->count();
            $cancelled = Project::where('status', 'annule')->count();

            return [
                'total' => $total,
                'active' => $active,
                'completed' => $completed,
                'cancelled' => $cancelled,
                'by_status' => Project::select('status', DB::raw('count(*) as count'))
                    ->groupBy('status')
                    ->get()
            ];
        } catch (\Exception $e) {
            return [
                'total' => 0,
                'active' => 0,
                'completed' => 0,
                'cancelled' => 0,
                'by_status' => []
            ];
        }
    }

    private function getOffresEmploiStats()
    {
        try {
            $total = OffreEmploi::count();
            $active = OffreEmploi::where('statut', 'active')->count();
            $closed = OffreEmploi::where('statut', 'fermee')->count();
            $draft = OffreEmploi::where('statut', 'brouillon')->count();

            return [
                'total' => $total,
                'active' => $active,
                'closed' => $closed,
                'draft' => $draft,
                'by_type' => OffreEmploi::select('type_contrat', DB::raw('count(*) as count'))
                    ->groupBy('type_contrat')
                    ->get()
            ];
        } catch (\Exception $e) {
            return [
                'total' => 0,
                'active' => 0,
                'closed' => 0,
                'draft' => 0,
                'by_type' => []
            ];
        }
    }

    private function getGeneralStats()
    {
        return [
            'total_modules' => 12,
            'last_updated' => now()->toISOString(),
            'system_status' => 'operational'
        ];
    }
}
