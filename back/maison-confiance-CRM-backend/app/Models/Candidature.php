<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Candidature extends Model
{
    use HasFactory;

    protected $fillable = [
        'candidat_id',
        'departement_id',
        'offre_emploi_id',
        'poste_souhaite',
        'lettre_motivation',
        'cv_path',
        'cv_filename',
        'cv_mime_type',
        'cv_size',
        'lettre_motivation_path',
        'lettre_motivation_filename',
        'lettre_motivation_mime_type',
        'lettre_motivation_size',
        'statut',
        'priorite',
        'date_candidature',
        'date_derniere_action',
        'date_entretien',
        'heure_entretien',
        'lieu_entretien',
        'notes_entretien',
        'evaluation',
        'note_globale',
        'commentaires_rh',
        'commentaires_technique',
        'commentaires_manager',
        'source_candidature',
        'campagne_recrutement',
        'candidature_spontanee',
        'offre_reference',
        'salaire_propose',
        'date_debut_souhaite',
        'motif_refus',
        'recruteur_id',
        'manager_id',
    ];

    protected $casts = [
        'date_candidature' => 'date',
        'date_derniere_action' => 'date',
        'date_entretien' => 'date',
        'heure_entretien' => 'datetime:H:i',
        'note_globale' => 'decimal:1',
        'salaire_propose' => 'decimal:2',
        'date_debut_souhaite' => 'date',
        'candidature_spontanee' => 'boolean',
    ];

    // Relation avec le candidat
    public function candidat()
    {
        return $this->belongsTo(Candidat::class);
    }

    // Relation avec le département
    public function departement()
    {
        return $this->belongsTo(Departement::class);
    }

    // Relation avec l'offre d'emploi
    public function offreEmploi()
    {
        return $this->belongsTo(OffreEmploi::class);
    }

    // Relation avec le recruteur
    public function recruteur()
    {
        return $this->belongsTo(User::class, 'recruteur_id');
    }

    // Relation avec le manager
    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    // Méthode pour obtenir le statut traduit
    public function getStatutLabelAttribute()
    {
        return match($this->statut) {
            'nouvelle' => 'Nouvelle',
            'en_cours' => 'En cours',
            'entretien_telephone' => 'Entretien téléphone',
            'entretien_rh' => 'Entretien RH',
            'entretien_technique' => 'Entretien technique',
            'entretien_final' => 'Entretien final',
            'test_technique' => 'Test technique',
            'reference_check' => 'Vérification références',
            'offre_envoyee' => 'Offre envoyée',
            'offre_acceptee' => 'Offre acceptée',
            'embauche' => 'Embauché',
            'refusee' => 'Refusée',
            'annulee' => 'Annulée',
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

    // Méthode pour vérifier si la candidature est active
    public function isActive()
    {
        return !in_array($this->statut, ['embauche', 'refusee', 'annulee']);
    }

    // Méthode pour vérifier si la candidature est terminée
    public function isTerminee()
    {
        return in_array($this->statut, ['embauche', 'refusee', 'annulee']);
    }

    // Méthode pour vérifier si la candidature est en cours
    public function isEnCours()
    {
        return in_array($this->statut, [
            'en_cours',
            'entretien_telephone',
            'entretien_rh',
            'entretien_technique',
            'entretien_final',
            'test_technique',
            'reference_check',
            'offre_envoyee',
            'offre_acceptee'
        ]);
    }

    // Méthode pour obtenir la durée de la candidature
    public function getDureeAttribute()
    {
        if (!$this->date_candidature) {
            return null;
        }
        
        $fin = $this->date_derniere_action ?? now();
        return Carbon::parse($this->date_candidature)->diffInDays($fin);
    }

    // Méthode pour obtenir la taille du CV formatée
    public function getCvSizeFormattedAttribute()
    {
        if (!$this->cv_size) {
            return null;
        }
        
        $bytes = $this->cv_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    // Méthode pour obtenir la taille de la lettre formatée
    public function getLettreSizeFormattedAttribute()
    {
        if (!$this->lettre_motivation_size) {
            return null;
        }
        
        $bytes = $this->lettre_motivation_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    // Méthode pour mettre à jour la date de dernière action
    public function updateDerniereAction()
    {
        $this->update(['date_derniere_action' => now()]);
    }

    // Méthode pour changer le statut
    public function changerStatut($nouveauStatut)
    {
        $this->update([
            'statut' => $nouveauStatut,
            'date_derniere_action' => now()
        ]);
    }

    // Méthode pour planifier un entretien
    public function planifierEntretien($date, $heure, $lieu)
    {
        $this->update([
            'date_entretien' => $date,
            'heure_entretien' => $heure,
            'lieu_entretien' => $lieu,
            'statut' => 'en_cours',
            'date_derniere_action' => now()
        ]);
    }

    // Méthode pour évaluer la candidature
    public function evaluer($note, $commentaires = null)
    {
        $this->update([
            'note_globale' => $note,
            'evaluation' => $commentaires,
            'date_derniere_action' => now()
        ]);
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

    // Scope pour filtrer par département
    public function scopeDepartement($query, $departementId)
    {
        return $query->where('departement_id', $departementId);
    }

    // Scope pour filtrer par recruteur
    public function scopeRecruteur($query, $recruteurId)
    {
        return $query->where('recruteur_id', $recruteurId);
    }

    // Scope pour les candidatures actives
    public function scopeActives($query)
    {
        return $query->whereNotIn('statut', ['embauche', 'refusee', 'annulee']);
    }

    // Scope pour les candidatures récentes
    public function scopeRecentes($query)
    {
        return $query->where('date_candidature', '>=', now()->subDays(30));
    }

    // Scope pour les candidatures spontanées
    public function scopeSpontanees($query)
    {
        return $query->where('candidature_spontanee', true);
    }

    // Scope pour rechercher
    public function scopeRecherche($query, $term)
    {
        return $query->where('poste_souhaite', 'like', "%$term%");
    }
}
