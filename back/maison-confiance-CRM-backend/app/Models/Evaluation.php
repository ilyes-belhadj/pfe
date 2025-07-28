<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Evaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        'titre',
        'description',
        'type',
        'statut',
        'priorite',
        'evaluable_type',
        'evaluable_id',
        'evaluateur_id',
        'manager_id',
        'departement_id',
        'date_evaluation',
        'date_limite',
        'date_validation',
        'prochaine_evaluation',
        'criteres_evaluation',
        'resultats',
        'note_globale',
        'note_competences',
        'note_performance',
        'note_comportement',
        'note_potentiel',
        'forces',
        'axes_amelioration',
        'objectifs',
        'recommandations',
        'commentaires_evaluateur',
        'commentaires_evalue',
        'commentaires_manager',
        'commentaires_rh',
        'validee_par_evalue',
        'validee_par_manager',
        'validee_par_rh',
        'date_validation_evalue',
        'date_validation_manager',
        'date_validation_rh',
        'version_grille',
        'reference',
        'notes_internes',
        'recommandation',
        'justification_recommandation',
    ];

    protected $casts = [
        'date_evaluation' => 'date',
        'date_limite' => 'date',
        'date_validation' => 'date',
        'prochaine_evaluation' => 'date',
        'criteres_evaluation' => 'array',
        'resultats' => 'array',
        'note_globale' => 'decimal:1',
        'note_competences' => 'decimal:1',
        'note_performance' => 'decimal:1',
        'note_comportement' => 'decimal:1',
        'note_potentiel' => 'decimal:1',
        'validee_par_evalue' => 'boolean',
        'validee_par_manager' => 'boolean',
        'validee_par_rh' => 'boolean',
        'date_validation_evalue' => 'date',
        'date_validation_manager' => 'date',
        'date_validation_rh' => 'date',
    ];

    // Relation polymorphique avec les candidats et employés
    public function evaluable()
    {
        return $this->morphTo();
    }

    // Relation avec l'évaluateur
    public function evaluateur()
    {
        return $this->belongsTo(User::class, 'evaluateur_id');
    }

    // Relation avec le manager
    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    // Relation avec le département
    public function departement()
    {
        return $this->belongsTo(Departement::class);
    }

    // Méthode pour obtenir le type traduit
    public function getTypeLabelAttribute()
    {
        return match($this->type) {
            'candidat' => 'Candidat',
            'employe' => 'Employé',
            'periode_essai' => 'Période d\'essai',
            'annuelle' => 'Évaluation annuelle',
            'performance' => 'Évaluation performance',
            default => 'Inconnu'
        };
    }

    // Méthode pour obtenir le statut traduit
    public function getStatutLabelAttribute()
    {
        return match($this->statut) {
            'brouillon' => 'Brouillon',
            'en_cours' => 'En cours',
            'terminee' => 'Terminée',
            'validee' => 'Validée',
            'rejetee' => 'Rejetée',
            default => 'Inconnu'
        };
    }

    // Méthode pour obtenir la priorité traduite
    public function getPrioriteLabelAttribute()
    {
        return match($this->priorite) {
            'basse' => 'Basse',
            'normale' => 'Normale',
            'haute' => 'Haute',
            'urgente' => 'Urgente',
            default => 'Non spécifiée'
        };
    }

    // Méthode pour obtenir la recommandation traduite
    public function getRecommandationLabelAttribute()
    {
        return match($this->recommandation) {
            'embauche' => 'Embauche',
            'confirmation' => 'Confirmation',
            'promotion' => 'Promotion',
            'formation' => 'Formation',
            'sanction' => 'Sanction',
            'licenciement' => 'Licenciement',
            default => 'Non spécifiée'
        };
    }

    // Méthode pour vérifier si l'évaluation est terminée
    public function isTerminee()
    {
        return in_array($this->statut, ['terminee', 'validee', 'rejetee']);
    }

    // Méthode pour vérifier si l'évaluation est en cours
    public function isEnCours()
    {
        return $this->statut === 'en_cours';
    }

    // Méthode pour vérifier si l'évaluation est validée
    public function isValidee()
    {
        return $this->statut === 'validee';
    }

    // Méthode pour vérifier si l'évaluation est en retard
    public function isEnRetard()
    {
        return $this->date_limite && $this->date_limite->isPast() && !$this->isTerminee();
    }

    // Méthode pour calculer la note moyenne
    public function getNoteMoyenneAttribute()
    {
        $notes = array_filter([
            $this->note_competences,
            $this->note_performance,
            $this->note_comportement,
            $this->note_potentiel
        ]);

        return !empty($notes) ? round(array_sum($notes) / count($notes), 1) : null;
    }

    // Méthode pour obtenir le niveau de performance
    public function getNiveauPerformanceAttribute()
    {
        if (!$this->note_globale) return null;

        return match(true) {
            $this->note_globale >= 9 => 'Excellent',
            $this->note_globale >= 8 => 'Très bien',
            $this->note_globale >= 7 => 'Bien',
            $this->note_globale >= 6 => 'Satisfaisant',
            $this->note_globale >= 5 => 'Moyen',
            default => 'Insuffisant'
        };
    }

    // Méthode pour valider l'évaluation
    public function valider($par = 'evaluateur')
    {
        $this->update([
            "validee_par_$par" => true,
            "date_validation_$par" => now()
        ]);

        // Si tous les validateurs ont validé, marquer comme validée
        if ($this->validee_par_evaluateur && $this->validee_par_manager && $this->validee_par_rh) {
            $this->update(['statut' => 'validee', 'date_validation' => now()]);
        }
    }

    // Méthode pour terminer l'évaluation
    public function terminer()
    {
        $this->update([
            'statut' => 'terminee',
            'date_validation' => now()
        ]);
    }

    // Méthode pour calculer la durée de l'évaluation
    public function getDureeAttribute()
    {
        if (!$this->date_evaluation) {
            return null;
        }
        
        $fin = $this->date_validation ?? now();
        return Carbon::parse($this->date_evaluation)->diffInDays($fin);
    }

    // Scope pour filtrer par type
    public function scopeType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Scope pour filtrer par statut
    public function scopeStatut($query, $statut)
    {
        return $query->where('statut', $statut);
    }

    // Scope pour filtrer par priorité
    public function scopePriorite($query, $priorite)
    {
        return $query->where('priorite', $priorite);
    }

    // Scope pour filtrer par évaluateur
    public function scopeEvaluateur($query, $evaluateurId)
    {
        return $query->where('evaluateur_id', $evaluateurId);
    }

    // Scope pour filtrer par département
    public function scopeDepartement($query, $departementId)
    {
        return $query->where('departement_id', $departementId);
    }

    // Scope pour les évaluations en cours
    public function scopeEnCours($query)
    {
        return $query->where('statut', 'en_cours');
    }

    // Scope pour les évaluations terminées
    public function scopeTerminees($query)
    {
        return $query->whereIn('statut', ['terminee', 'validee']);
    }

    // Scope pour les évaluations en retard
    public function scopeEnRetard($query)
    {
        return $query->where('date_limite', '<', now())
                     ->whereNotIn('statut', ['terminee', 'validee']);
    }

    // Scope pour les évaluations récentes (derniers 30 jours)
    public function scopeRecentes($query)
    {
        return $query->where('date_evaluation', '>=', now()->subDays(30));
    }

    // Scope pour rechercher par titre
    public function scopeRecherche($query, $term)
    {
        return $query->where('titre', 'like', "%{$term}%");
    }
}
