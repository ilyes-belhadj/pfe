<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class OffreEmploi extends Model
{
    use HasFactory;

    protected $fillable = [
        'titre',
        'description',
        'profil_recherche',
        'missions',
        'competences_requises',
        'avantages',
        'type_contrat',
        'niveau_experience',
        'niveau_etude',
        'lieu_travail',
        'mode_travail',
        'nombre_poste',
        'salaire_min',
        'salaire_max',
        'devise_salaire',
        'periode_salaire',
        'date_publication',
        'date_limite_candidature',
        'date_debut_poste',
        'date_fin_publication',
        'statut',
        'publiee',
        'urgente',
        'sponsorisee',
        'departement_id',
        'recruteur_id',
        'manager_id',
        'reference',
        'source',
        'notes_internes',
        'tags',
        'nombre_vues',
        'nombre_candidatures',
        'nombre_candidatures_acceptees',
        'nombre_candidatures_rejetees',
        'slug',
        'meta_description',
        'meta_keywords',
        'auto_archive',
        'notifications_email',
        'notifications_sms',
    ];

    protected $casts = [
        'date_publication' => 'date',
        'date_limite_candidature' => 'date',
        'date_debut_poste' => 'date',
        'date_fin_publication' => 'date',
        'publiee' => 'boolean',
        'urgente' => 'boolean',
        'sponsorisee' => 'boolean',
        'tags' => 'array',
        'salaire_min' => 'decimal:2',
        'salaire_max' => 'decimal:2',
        'auto_archive' => 'boolean',
        'notifications_email' => 'boolean',
        'notifications_sms' => 'boolean',
    ];

    // Relations
    public function departement()
    {
        return $this->belongsTo(Departement::class);
    }

    public function recruteur()
    {
        return $this->belongsTo(User::class, 'recruteur_id');
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function candidatures()
    {
        return $this->hasMany(Candidature::class);
    }

    public function evaluations()
    {
        return $this->hasMany(Evaluation::class, 'evaluable_id')->where('evaluable_type', OffreEmploi::class);
    }

    // Méthodes pour obtenir les labels traduits
    public function getTypeContratLabelAttribute()
    {
        return match($this->type_contrat) {
            'CDI' => 'CDI',
            'CDD' => 'CDD',
            'Stage' => 'Stage',
            'Alternance' => 'Alternance',
            'Freelance' => 'Freelance',
            'Interim' => 'Intérim',
            default => 'Non spécifié'
        };
    }

    public function getNiveauExperienceLabelAttribute()
    {
        return match($this->niveau_experience) {
            'debutant' => 'Débutant',
            'intermediaire' => 'Intermédiaire',
            'confirme' => 'Confirmé',
            'expert' => 'Expert',
            default => 'Non spécifié'
        };
    }

    public function getModeTravailLabelAttribute()
    {
        return match($this->mode_travail) {
            'presentiel' => 'Présentiel',
            'hybride' => 'Hybride',
            'teletravail' => 'Télétravail',
            default => 'Non spécifié'
        };
    }

    public function getStatutLabelAttribute()
    {
        return match($this->statut) {
            'brouillon' => 'Brouillon',
            'active' => 'Active',
            'en_cours' => 'En cours',
            'terminee' => 'Terminée',
            'archivee' => 'Archivée',
            default => 'Inconnu'
        };
    }

    // Méthodes utilitaires
    public function isActive()
    {
        return $this->statut === 'active' && $this->publiee;
    }

    public function isExpired()
    {
        return $this->date_limite_candidature && $this->date_limite_candidature->isPast();
    }

    public function isUrgent()
    {
        return $this->urgente;
    }

    public function isSponsorisee()
    {
        return $this->sponsorisee;
    }

    public function getSalaireFormattedAttribute()
    {
        if (!$this->salaire_min && !$this->salaire_max) {
            return 'À négocier';
        }

        $min = $this->salaire_min ? number_format($this->salaire_min, 0, ',', ' ') : '';
        $max = $this->salaire_max ? number_format($this->salaire_max, 0, ',', ' ') : '';

        if ($min && $max) {
            return "$min - $max {$this->devise_salaire}";
        } elseif ($min) {
            return "À partir de $min {$this->devise_salaire}";
        } elseif ($max) {
            return "Jusqu'à $max {$this->devise_salaire}";
        }

        return 'À négocier';
    }

    public function getDureePublicationAttribute()
    {
        if (!$this->date_publication) {
            return null;
        }
        
        $fin = $this->date_fin_publication ?? now();
        return Carbon::parse($this->date_publication)->diffInDays($fin);
    }

    public function incrementVues()
    {
        $this->increment('nombre_vues');
    }

    public function incrementCandidatures()
    {
        $this->increment('nombre_candidatures');
    }

    public function incrementCandidaturesAcceptees()
    {
        $this->increment('nombre_candidatures_acceptees');
    }

    public function incrementCandidaturesRejetees()
    {
        $this->increment('nombre_candidatures_rejetees');
    }

    // Publier l'offre
    public function publier()
    {
        $this->update([
            'statut' => 'active',
            'publiee' => true,
            'date_publication' => now()
        ]);
    }

    // Archiver l'offre
    public function archiver()
    {
        $this->update([
            'statut' => 'archivee',
            'publiee' => false
        ]);
    }

    // Terminer l'offre
    public function terminer()
    {
        $this->update([
            'statut' => 'terminee',
            'publiee' => false
        ]);
    }

    // Scopes pour le filtrage
    public function scopeActive($query)
    {
        return $query->where('statut', 'active')->where('publiee', true);
    }

    public function scopePubliee($query)
    {
        return $query->where('publiee', true);
    }

    public function scopeUrgente($query)
    {
        return $query->where('urgente', true);
    }

    public function scopeSponsorisee($query)
    {
        return $query->where('sponsorisee', true);
    }

    public function scopeTypeContrat($query, $type)
    {
        return $query->where('type_contrat', $type);
    }

    public function scopeNiveauExperience($query, $niveau)
    {
        return $query->where('niveau_experience', $niveau);
    }

    public function scopeLieuTravail($query, $lieu)
    {
        return $query->where('lieu_travail', 'like', "%$lieu%");
    }

    public function scopeDepartement($query, $departementId)
    {
        return $query->where('departement_id', $departementId);
    }

    public function scopeRecruteur($query, $recruteurId)
    {
        return $query->where('recruteur_id', $recruteurId);
    }

    public function scopeRecente($query)
    {
        return $query->where('date_publication', '>=', now()->subDays(30));
    }

    public function scopeExpiree($query)
    {
        return $query->where('date_limite_candidature', '<', now());
    }

    public function scopeNonExpiree($query)
    {
        return $query->where(function($q) {
            $q->whereNull('date_limite_candidature')
              ->orWhere('date_limite_candidature', '>=', now());
        });
    }

    public function scopeRecherche($query, $term)
    {
        return $query->where(function($q) use ($term) {
            $q->where('titre', 'like', "%$term%")
              ->orWhere('description', 'like', "%$term%")
              ->orWhere('profil_recherche', 'like', "%$term%")
              ->orWhere('lieu_travail', 'like', "%$term%");
        });
    }
}
