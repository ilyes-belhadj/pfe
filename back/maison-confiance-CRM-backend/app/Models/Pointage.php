<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Pointage extends Model
{
    use HasFactory;

    protected $fillable = [
        'employe_id',
        'date_pointage',
        'heure_entree',
        'heure_sortie',
        'heure_pause_debut',
        'heure_pause_fin',
        'heures_travaillees',
        'heures_pause',
        'heures_net',
        'statut',
        'commentaire',
        'lieu_pointage',
        'methode_pointage',
        'ip_address',
        'user_agent',
        'latitude',
        'longitude',
        'valide',
        'valide_par',
        'valide_le',
    ];

    protected $casts = [
        'date_pointage' => 'date',
        'heure_entree' => 'datetime:H:i',
        'heure_sortie' => 'datetime:H:i',
        'heure_pause_debut' => 'datetime:H:i',
        'heure_pause_fin' => 'datetime:H:i',
        'heures_travaillees' => 'decimal:2',
        'heures_pause' => 'decimal:2',
        'heures_net' => 'decimal:2',
        'valide' => 'boolean',
        'valide_le' => 'datetime',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    // Relation avec l'employé
    public function employe()
    {
        return $this->belongsTo(Employe::class);
    }

    // Relation avec l'utilisateur qui a validé
    public function validePar()
    {
        return $this->belongsTo(User::class, 'valide_par');
    }

    // Méthode pour calculer les heures travaillées
    public function calculerHeuresTravaillees()
    {
        if (!$this->heure_entree || !$this->heure_sortie) {
            return 0;
        }

        $entree = Carbon::parse($this->date_pointage . ' ' . $this->heure_entree);
        $sortie = Carbon::parse($this->date_pointage . ' ' . $this->heure_sortie);
        
        return $entree->diffInHours($sortie, true);
    }

    // Méthode pour calculer les heures de pause
    public function calculerHeuresPause()
    {
        if (!$this->heure_pause_debut || !$this->heure_pause_fin) {
            return 0;
        }

        $pause_debut = Carbon::parse($this->date_pointage . ' ' . $this->heure_pause_debut);
        $pause_fin = Carbon::parse($this->date_pointage . ' ' . $this->heure_pause_fin);
        
        return $pause_debut->diffInHours($pause_fin, true);
    }

    // Méthode pour calculer les heures nettes
    public function calculerHeuresNet()
    {
        return $this->heures_travaillees - $this->heures_pause;
    }

    // Méthode pour vérifier si le pointage est complet
    public function isComplet()
    {
        return $this->heure_entree && $this->heure_sortie;
    }

    // Méthode pour vérifier si l'employé est en pause
    public function isEnPause()
    {
        return $this->statut === 'en_pause';
    }

    // Méthode pour vérifier si l'employé est présent
    public function isPresent()
    {
        return $this->statut === 'present';
    }

    // Méthode pour vérifier si l'employé est absent
    public function isAbsent()
    {
        return $this->statut === 'absent';
    }

    // Méthode pour marquer comme validé
    public function marquerValide(User $user)
    {
        $this->update([
            'valide' => true,
            'valide_par' => $user->id,
            'valide_le' => now(),
        ]);
    }

    // Méthode pour marquer comme non validé
    public function marquerNonValide()
    {
        $this->update([
            'valide' => false,
            'valide_par' => null,
            'valide_le' => null,
        ]);
    }

    // Méthode pour obtenir la durée de travail formatée
    public function getDureeFormateeAttribute()
    {
        $heures = floor($this->heures_net);
        $minutes = round(($this->heures_net - $heures) * 60);
        
        return sprintf('%02d:%02d', $heures, $minutes);
    }

    // Méthode pour obtenir le statut traduit
    public function getStatutLabelAttribute()
    {
        return match($this->statut) {
            'present' => 'Présent',
            'absent' => 'Absent',
            'en_pause' => 'En pause',
            'retard' => 'Retard',
            'depart_anticipé' => 'Départ anticipé',
            default => 'Inconnu'
        };
    }

    // Méthode pour obtenir le lieu traduit
    public function getLieuLabelAttribute()
    {
        return match($this->lieu_pointage) {
            'bureau' => 'Bureau',
            'teletravail' => 'Télétravail',
            'deplacement' => 'Déplacement',
            'formation' => 'Formation',
            'conges' => 'Congés',
            default => $this->lieu_pointage ?? 'Non spécifié'
        };
    }

    // Scope pour filtrer par employé
    public function scopeEmploye($query, $employeId)
    {
        return $query->where('employe_id', $employeId);
    }

    // Scope pour filtrer par date
    public function scopeDate($query, $date)
    {
        return $query->where('date_pointage', $date);
    }

    // Scope pour filtrer par période
    public function scopePeriode($query, $debut, $fin)
    {
        return $query->whereBetween('date_pointage', [$debut, $fin]);
    }

    // Scope pour filtrer par statut
    public function scopeStatut($query, $statut)
    {
        return $query->where('statut', $statut);
    }

    // Scope pour filtrer par validation
    public function scopeValide($query, $valide = true)
    {
        return $query->where('valide', $valide);
    }

    // Scope pour les pointages du jour
    public function scopeAujourdhui($query)
    {
        return $query->where('date_pointage', now()->toDateString());
    }

    // Scope pour les pointages de la semaine
    public function scopeCetteSemaine($query)
    {
        return $query->whereBetween('date_pointage', [
            now()->startOfWeek()->toDateString(),
            now()->endOfWeek()->toDateString()
        ]);
    }

    // Scope pour les pointages du mois
    public function scopeCeMois($query)
    {
        return $query->whereBetween('date_pointage', [
            now()->startOfMonth()->toDateString(),
            now()->endOfMonth()->toDateString()
        ]);
    }
}
