<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CandidatResource extends JsonResource
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
            'nom' => $this->nom,
            'prenom' => $this->prenom,
            'nom_complet' => $this->nom_complet,
            'email' => $this->email,
            'telephone' => $this->telephone,
            'adresse' => $this->adresse,
            'ville' => $this->ville,
            'code_postal' => $this->code_postal,
            'pays' => $this->pays,
            'adresse_complete' => $this->adresse_complete,
            'date_naissance' => $this->date_naissance?->format('Y-m-d'),
            'lieu_naissance' => $this->lieu_naissance,
            'nationalite' => $this->nationalite,
            'age' => $this->age,
            'civilite' => $this->civilite,
            'civilite_label' => $this->civilite_label,
            'linkedin' => $this->linkedin,
            'site_web' => $this->site_web,
            'bio' => $this->bio,
            'competences' => $this->competences,
            'experiences' => $this->experiences,
            'formation' => $this->formation,
            'disponibilite' => $this->disponibilite,
            'pretention_salaire' => $this->pretention_salaire,
            'mobilite' => $this->mobilite,
            'statut' => $this->statut,
            'statut_label' => $this->statut_label,
            'notes' => $this->notes,
            'source_recrutement' => $this->source_recrutement,
            'nombre_candidatures' => $this->nombre_candidatures,
            'nombre_candidatures_actives' => $this->nombre_candidatures_actives,
            'derniere_candidature' => $this->when($this->derniereCandidature(), function () {
                return [
                    'id' => $this->derniereCandidature()->id,
                    'poste_souhaite' => $this->derniereCandidature()->poste_souhaite,
                    'statut' => $this->derniereCandidature()->statut,
                    'date_candidature' => $this->derniereCandidature()->date_candidature?->format('Y-m-d'),
                ];
            }),
            'candidatures' => CandidatureResource::collection($this->whenLoaded('candidatures')),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
