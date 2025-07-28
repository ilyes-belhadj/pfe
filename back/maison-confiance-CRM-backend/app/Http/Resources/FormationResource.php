<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FormationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'titre' => $this->titre,
            'description' => $this->description,
            'formateur' => $this->formateur,
            'date_debut' => $this->date_debut->format('Y-m-d'),
            'date_fin' => $this->date_fin->format('Y-m-d'),
            'duree_heures' => $this->duree_heures,
            'cout' => $this->cout,
            'statut' => $this->statut,
            'lieu' => $this->lieu,
            'nombre_places' => $this->nombre_places,
            'places_occupees' => $this->places_occupees,
            'places_restantes' => $this->places_restantes,
            'is_complete' => $this->isComplete(),
            'is_available' => $this->isAvailable(),
            'employes' => $this->whenLoaded('employes', function () {
                return $this->employes->map(function ($employe) {
                    return [
                        'id' => $employe->id,
                        'nom' => $employe->nom,
                        'prenom' => $employe->prenom,
                        'email' => $employe->email,
                        'pivot' => [
                            'date_inscription' => $employe->pivot->date_inscription,
                            'statut_participation' => $employe->pivot->statut_participation,
                            'notes' => $employe->pivot->notes,
                        ]
                    ];
                });
            }),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
