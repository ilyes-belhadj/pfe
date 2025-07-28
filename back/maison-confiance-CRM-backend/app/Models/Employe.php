<?php

namespace App\Models;

use App\Models\Departement;
use App\Models\Absence;
use App\Models\Formation;
use App\Models\Paie;
use App\Models\Pointage;
use App\Models\Evaluation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employe extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'date_embauche',
        'salaire',
        'departement_id', // Si vous avez une relation avec Departement
        // ... autres champs
    ];

    // Exemple de relation : un employé appartient à un département
    public function departement()
    {
        return $this->belongsTo(Departement::class);
    }

    public function absence()
    {
        return $this->hasMany(Absence::class);
    }

    // Relation avec les formations (many-to-many)
    public function formations()
    {
        return $this->belongsToMany(Formation::class, 'formation_employe')
                    ->withPivot('date_inscription', 'statut_participation', 'notes')
                    ->withTimestamps();
    }

    // Si un employé est un utilisateur de l'application
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relation avec les paies
    public function paies()
    {
        return $this->hasMany(Paie::class);
    }

    // Relation avec les pointages
    public function pointages()
    {
        return $this->hasMany(Pointage::class);
    }

    // Relation avec les évaluations
    public function evaluations()
    {
        return $this->morphMany(Evaluation::class, 'evaluable');
    }
}