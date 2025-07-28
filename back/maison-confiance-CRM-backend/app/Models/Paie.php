<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paie extends Model
{
    use HasFactory;

    protected $fillable = [
        'employe_id',
        'periode',
        'date_paiement',
        'salaire_base',
        'heures_travaillees',
        'taux_horaire',
        'salaire_brut',
        'primes',
        'deductions',
        'cotisations_sociales',
        'impots',
        'salaire_net',
        'statut',
        'notes',
        'mode_paiement',
        'numero_cheque',
        'reference_paiement',
    ];

    protected $casts = [
        'date_paiement' => 'date',
        'salaire_base' => 'decimal:2',
        'heures_travaillees' => 'decimal:2',
        'taux_horaire' => 'decimal:2',
        'salaire_brut' => 'decimal:2',
        'primes' => 'decimal:2',
        'deductions' => 'decimal:2',
        'cotisations_sociales' => 'decimal:2',
        'impots' => 'decimal:2',
        'salaire_net' => 'decimal:2',
    ];

    // Relation avec l'employé
    public function employe()
    {
        return $this->belongsTo(Employe::class);
    }

    // Méthode pour calculer le salaire brut
    public function calculerSalaireBrut()
    {
        $salaireHoraire = $this->heures_travaillees * $this->taux_horaire;
        return $this->salaire_base + $salaireHoraire;
    }

    // Méthode pour calculer le salaire net
    public function calculerSalaireNet()
    {
        $totalDeductions = $this->deductions + $this->cotisations_sociales + $this->impots;
        return $this->salaire_brut + $this->primes - $totalDeductions;
    }

    // Méthode pour vérifier si la paie est payée
    public function isPayee()
    {
        return $this->statut === 'paye';
    }

    // Méthode pour vérifier si la paie est en attente
    public function isEnAttente()
    {
        return $this->statut === 'en_attente';
    }

    // Méthode pour marquer comme payée
    public function marquerCommePayee()
    {
        $this->update(['statut' => 'paye']);
    }

    // Méthode pour obtenir le total des gains
    public function getTotalGainsAttribute()
    {
        return $this->salaire_brut + $this->primes;
    }

    // Méthode pour obtenir le total des déductions
    public function getTotalDeductionsAttribute()
    {
        return $this->deductions + $this->cotisations_sociales + $this->impots;
    }

    // Scope pour filtrer par période
    public function scopePeriode($query, $periode)
    {
        return $query->where('periode', $periode);
    }

    // Scope pour filtrer par statut
    public function scopeStatut($query, $statut)
    {
        return $query->where('statut', $statut);
    }

    // Scope pour filtrer par employé
    public function scopeEmploye($query, $employeId)
    {
        return $query->where('employe_id', $employeId);
    }
}
