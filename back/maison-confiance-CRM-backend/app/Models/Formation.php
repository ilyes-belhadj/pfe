<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Formation extends Model
{
    use HasFactory;

    protected $fillable = [
        'titre',
        'description',
        'formateur',
        'date_debut',
        'date_fin',
        'duree_heures',
        'cout',
        'statut',
        'lieu',
        'nombre_places',
        'places_occupees',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
        'cout' => 'decimal:2',
    ];

    // Relation avec les employés (many-to-many)
    public function employes()
    {
        return $this->belongsToMany(Employe::class, 'formation_employe')
                    ->withPivot('date_inscription', 'statut_participation')
                    ->withTimestamps();
    }

    // Méthode pour vérifier si la formation est complète
    public function isComplete()
    {
        return $this->places_occupees >= $this->nombre_places;
    }

    // Méthode pour vérifier si la formation est disponible
    public function isAvailable()
    {
        return $this->statut === 'planifie' && !$this->isComplete();
    }

    // Méthode pour calculer les places restantes
    public function getPlacesRestantesAttribute()
    {
        return $this->nombre_places - $this->places_occupees;
    }
}
