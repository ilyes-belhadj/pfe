<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CandidatureResource extends JsonResource
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
            'candidat_id' => $this->candidat_id,
            'departement_id' => $this->departement_id,
            'poste_souhaite' => $this->poste_souhaite,
            'lettre_motivation' => $this->lettre_motivation,
            'cv_path' => $this->cv_path,
            'cv_filename' => $this->cv_filename,
            'cv_mime_type' => $this->cv_mime_type,
            'cv_size' => $this->cv_size,
            'cv_size_formatted' => $this->cv_size_formatted,
            'lettre_motivation_path' => $this->lettre_motivation_path,
            'lettre_motivation_filename' => $this->lettre_motivation_filename,
            'lettre_motivation_mime_type' => $this->lettre_motivation_mime_type,
            'lettre_motivation_size' => $this->lettre_motivation_size,
            'lettre_size_formatted' => $this->lettre_size_formatted,
            'statut' => $this->statut,
            'statut_label' => $this->statut_label,
            'priorite' => $this->priorite,
            'priorite_label' => $this->priorite_label,
            'date_candidature' => $this->date_candidature?->format('Y-m-d'),
            'date_derniere_action' => $this->date_derniere_action?->format('Y-m-d'),
            'date_entretien' => $this->date_entretien?->format('Y-m-d'),
            'heure_entretien' => $this->heure_entretien?->format('H:i'),
            'lieu_entretien' => $this->lieu_entretien,
            'notes_entretien' => $this->notes_entretien,
            'evaluation' => $this->evaluation,
            'note_globale' => $this->note_globale,
            'commentaires_rh' => $this->commentaires_rh,
            'commentaires_technique' => $this->commentaires_technique,
            'commentaires_manager' => $this->commentaires_manager,
            'source_candidature' => $this->source_candidature,
            'campagne_recrutement' => $this->campagne_recrutement,
            'candidature_spontanee' => $this->candidature_spontanee,
            'offre_reference' => $this->offre_reference,
            'salaire_propose' => $this->salaire_propose,
            'date_debut_souhaite' => $this->date_debut_souhaite?->format('Y-m-d'),
            'motif_refus' => $this->motif_refus,
            'recruteur_id' => $this->recruteur_id,
            'manager_id' => $this->manager_id,
            'duree' => $this->duree,
            'is_active' => $this->isActive(),
            'is_terminee' => $this->isTerminee(),
            'is_en_cours' => $this->isEnCours(),
            'candidat' => $this->whenLoaded('candidat', function () {
                return [
                    'id' => $this->candidat->id,
                    'nom' => $this->candidat->nom,
                    'prenom' => $this->candidat->prenom,
                    'nom_complet' => $this->candidat->nom_complet,
                    'email' => $this->candidat->email,
                    'telephone' => $this->candidat->telephone,
                    'statut' => $this->candidat->statut,
                ];
            }),
            'departement' => $this->whenLoaded('departement', function () {
                return [
                    'id' => $this->departement->id,
                    'nom' => $this->departement->nom,
                    'description' => $this->departement->description,
                ];
            }),
            'recruteur' => $this->whenLoaded('recruteur', function () {
                return [
                    'id' => $this->recruteur->id,
                    'name' => $this->recruteur->name,
                    'email' => $this->recruteur->email,
                ];
            }),
            'manager' => $this->whenLoaded('manager', function () {
                return [
                    'id' => $this->manager->id,
                    'name' => $this->manager->name,
                    'email' => $this->manager->email,
                ];
            }),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
