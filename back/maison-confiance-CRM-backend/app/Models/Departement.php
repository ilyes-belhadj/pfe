<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Departement extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'departements'; // C'est la convention par défaut de Laravel, donc souvent pas nécessaire de la spécifier

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nom',
        'description',
        // Ajoutez ici tous les champs que vous souhaitez pouvoir assigner en masse (ex: lors de la création ou mise à jour)
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        // 'password', // Souvent pour les utilisateurs
        // 'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        // 'email_verified_at' => 'datetime',
        // 'password' => 'hashed',
    ];

    // Relation avec les employés
    public function employes()
    {
        return $this->hasMany(Employe::class, 'departement_id');
    }

    // Relation avec les candidatures
    public function candidatures()
    {
        return $this->hasMany(Candidature::class);
    }
}