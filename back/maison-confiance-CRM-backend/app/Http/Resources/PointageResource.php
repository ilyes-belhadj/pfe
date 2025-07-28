<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PointageResource extends JsonResource
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
            'employe_id' => $this->employe_id,
            'employe' => $this->whenLoaded('employe', function () {
                return [
                    'id' => $this->employe->id,
                    'nom' => $this->employe->nom,
                    'prenom' => $this->employe->prenom,
                    'email' => $this->employe->email,
                    'matricule' => $this->employe->matricule,
                ];
            }),
            'date_pointage' => $this->date_pointage->format('Y-m-d'),
            'heure_entree' => $this->heure_entree,
            'heure_sortie' => $this->heure_sortie,
            'heure_pause_debut' => $this->heure_pause_debut,
            'heure_pause_fin' => $this->heure_pause_fin,
            'heures_travaillees' => $this->heures_travaillees,
            'heures_pause' => $this->heures_pause,
            'heures_net' => $this->heures_net,
            'duree_formatee' => $this->duree_formatee,
            'statut' => $this->statut,
            'statut_label' => $this->statut_label,
            'commentaire' => $this->commentaire,
            'lieu_pointage' => $this->lieu_pointage,
            'lieu_label' => $this->lieu_label,
            'methode_pointage' => $this->methode_pointage,
            'ip_address' => $this->ip_address,
            'user_agent' => $this->user_agent,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'valide' => $this->valide,
            'valide_par' => $this->valide_par,
            'valide_le' => $this->valide_le?->format('Y-m-d H:i:s'),
            'valide_par_info' => $this->whenLoaded('validePar', function () {
                return [
                    'id' => $this->validePar->id,
                    'name' => $this->validePar->name,
                    'email' => $this->validePar->email,
                ];
            }),
            'is_complet' => $this->isComplet(),
            'is_en_pause' => $this->isEnPause(),
            'is_present' => $this->isPresent(),
            'is_absent' => $this->isAbsent(),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
