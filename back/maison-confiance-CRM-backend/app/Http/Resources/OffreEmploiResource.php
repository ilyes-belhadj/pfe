<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OffreEmploiResource extends JsonResource
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
            'profil_recherche' => $this->profil_recherche,
            'missions' => $this->missions,
            'competences_requises' => $this->competences_requises,
            'avantages' => $this->avantages,
            
            // Informations du poste
            'type_contrat' => $this->type_contrat,
            'type_contrat_label' => $this->type_contrat_label,
            'niveau_experience' => $this->niveau_experience,
            'niveau_experience_label' => $this->niveau_experience_label,
            'niveau_etude' => $this->niveau_etude,
            'lieu_travail' => $this->lieu_travail,
            'mode_travail' => $this->mode_travail,
            'mode_travail_label' => $this->mode_travail_label,
            'nombre_poste' => $this->nombre_poste,
            
            // Rémunération
            'salaire_min' => $this->salaire_min,
            'salaire_max' => $this->salaire_max,
            'devise_salaire' => $this->devise_salaire,
            'periode_salaire' => $this->periode_salaire,
            'salaire_formatted' => $this->salaire_formatted,
            
            // Dates
            'date_publication' => $this->date_publication?->format('Y-m-d'),
            'date_limite_candidature' => $this->date_limite_candidature?->format('Y-m-d'),
            'date_debut_poste' => $this->date_debut_poste?->format('Y-m-d'),
            'date_fin_publication' => $this->date_fin_publication?->format('Y-m-d'),
            
            // Statut et visibilité
            'statut' => $this->statut,
            'statut_label' => $this->statut_label,
            'publiee' => $this->publiee,
            'urgente' => $this->urgente,
            'sponsorisee' => $this->sponsorisee,
            
            // Relations
            'departement_id' => $this->departement_id,
            'recruteur_id' => $this->recruteur_id,
            'manager_id' => $this->manager_id,
            
            // Métadonnées
            'reference' => $this->reference,
            'source' => $this->source,
            'notes_internes' => $this->notes_internes,
            'tags' => $this->tags,
            
            // Statistiques
            'nombre_vues' => $this->nombre_vues,
            'nombre_candidatures' => $this->nombre_candidatures,
            'nombre_candidatures_acceptees' => $this->nombre_candidatures_acceptees,
            'nombre_candidatures_rejetees' => $this->nombre_candidatures_rejetees,
            
            // SEO et marketing
            'slug' => $this->slug,
            'meta_description' => $this->meta_description,
            'meta_keywords' => $this->meta_keywords,
            
            // Configuration
            'auto_archive' => $this->auto_archive,
            'notifications_email' => $this->notifications_email,
            'notifications_sms' => $this->notifications_sms,
            
            // États calculés
            'is_active' => $this->isActive(),
            'is_expired' => $this->isExpired(),
            'is_urgent' => $this->isUrgent(),
            'is_sponsorisee' => $this->isSponsorisee(),
            'duree_publication' => $this->duree_publication,
            
            // Relations
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
