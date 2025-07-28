<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nom' => $this->nom,
            'prenom' => $this->prenom,
            'email' => $this->email,
            'date_embauche' => $this->date_embauche->format('Y-m-d'),
            'salaire' => number_format($this->salaire, 2),
            'departement' => new DepartementResource($this->whenLoaded('departement')), // Si la relation est chargée
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            // N'exposez pas les données sensibles ici
        ];
    }
}