<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Candidat extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'telephone',
        'adresse',
        'ville',
        'code_postal',
        'pays',
        'date_naissance',
        'lieu_naissance',
        'nationalite',
        'civilite',
        'linkedin',
        'site_web',
        'bio',
        'competences',
        'experiences',
        'formation',
        'disponibilite',
        'pretention_salaire',
        'mobilite',
        'statut',
        'notes',
        'source_recrutement',
    ];

    protected $casts = [
        'date_naissance' => 'date',
        'pretention_salaire' => 'decimal:2',
    ];

    // Relation avec les candidatures
    public function candidatures()
    {
        return $this->hasMany(Candidature::class);
    }

    // Relation avec les évaluations
    public function evaluations()
    {
        return $this->morphMany(Evaluation::class, 'evaluable');
    }

    // Méthode pour obtenir le nom complet
    public function getNomCompletAttribute()
    {
        return $this->prenom . ' ' . $this->nom;
    }

    // Méthode pour calculer l'âge
    public function getAgeAttribute()
    {
        if (!$this->date_naissance) {
            return null;
        }
        return Carbon::parse($this->date_naissance)->age;
    }

    // Méthode pour obtenir l'adresse complète
    public function getAdresseCompleteAttribute()
    {
        $adresse = [];
        if ($this->adresse) $adresse[] = $this->adresse;
        if ($this->code_postal && $this->ville) {
            $adresse[] = $this->code_postal . ' ' . $this->ville;
        } elseif ($this->ville) {
            $adresse[] = $this->ville;
        }
        if ($this->pays && $this->pays !== 'France') {
            $adresse[] = $this->pays;
        }
        
        return implode(', ', $adresse);
    }

    // Méthode pour vérifier si le candidat est actif
    public function isActif()
    {
        return $this->statut === 'actif';
    }

    // Méthode pour vérifier si le candidat est blacklisté
    public function isBlacklist()
    {
        return $this->statut === 'blacklist';
    }

    // Méthode pour obtenir le statut traduit
    public function getStatutLabelAttribute()
    {
        return match($this->statut) {
            'actif' => 'Actif',
            'inactif' => 'Inactif',
            'blacklist' => 'Blacklisté',
            default => 'Inconnu'
        };
    }

    // Méthode pour obtenir la civilité traduite
    public function getCiviliteLabelAttribute()
    {
        return match($this->civilite) {
            'M' => 'Monsieur',
            'Mme' => 'Madame',
            'Mlle' => 'Mademoiselle',
            default => 'Non spécifié'
        };
    }

    // Scope pour filtrer par statut
    public function scopeStatut($query, $statut)
    {
        return $query->where('statut', $statut);
    }

    // Scope pour filtrer par source de recrutement
    public function scopeSource($query, $source)
    {
        return $query->where('source_recrutement', $source);
    }

    // Scope pour rechercher par nom/prénom/email
    public function scopeRecherche($query, $term)
    {
        return $query->where(function($q) use ($term) {
            $q->where('nom', 'like', "%{$term}%")
              ->orWhere('prenom', 'like', "%{$term}%")
              ->orWhere('email', 'like', "%{$term}%");
        });
    }

    // Scope pour les candidats actifs
    public function scopeActifs($query)
    {
        return $query->where('statut', 'actif');
    }

    // Scope pour les candidats récents (derniers 30 jours)
    public function scopeRecents($query)
    {
        return $query->where('created_at', '>=', now()->subDays(30));
    }

    // Méthode pour obtenir les candidatures actives
    public function candidaturesActives()
    {
        return $this->candidatures()->whereNotIn('statut', ['embauche', 'refusee', 'annulee']);
    }

    // Méthode pour obtenir la dernière candidature
    public function derniereCandidature()
    {
        return $this->candidatures()->latest()->first();
    }

    // Méthode pour obtenir le nombre de candidatures
    public function getNombreCandidaturesAttribute()
    {
        return $this->candidatures()->count();
    }

    // Méthode pour obtenir le nombre de candidatures actives
    public function getNombreCandidaturesActivesAttribute()
    {
        return $this->candidaturesActives()->count();
    }
}
