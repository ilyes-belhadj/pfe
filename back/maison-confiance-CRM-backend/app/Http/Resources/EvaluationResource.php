<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EvaluationResource extends JsonResource
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
            'type' => $this->type,
            'type_label' => $this->type_label,
            'statut' => $this->statut,
            'statut_label' => $this->statut_label,
            'priorite' => $this->priorite,
            'priorite_label' => $this->priorite_label,
            
            // Relations polymorphiques
            'evaluable_type' => $this->evaluable_type,
            'evaluable_id' => $this->evaluable_id,
            
            // Évaluateur et manager
            'evaluateur_id' => $this->evaluateur_id,
            'manager_id' => $this->manager_id,
            'departement_id' => $this->departement_id,
            
            // Dates
            'date_evaluation' => $this->date_evaluation?->format('Y-m-d'),
            'date_limite' => $this->date_limite?->format('Y-m-d'),
            'date_validation' => $this->date_validation?->format('Y-m-d'),
            'prochaine_evaluation' => $this->prochaine_evaluation?->format('Y-m-d'),
            
            // Critères et résultats
            'criteres_evaluation' => $this->criteres_evaluation,
            'resultats' => $this->resultats,
            
            // Notes
            'note_globale' => $this->note_globale,
            'note_competences' => $this->note_competences,
            'note_performance' => $this->note_performance,
            'note_comportement' => $this->note_comportement,
            'note_potentiel' => $this->note_potentiel,
            'note_moyenne' => $this->note_moyenne,
            'niveau_performance' => $this->niveau_performance,
            
            // Évaluations détaillées
            'forces' => $this->forces,
            'axes_amelioration' => $this->axes_amelioration,
            'objectifs' => $this->objectifs,
            'recommandations' => $this->recommandations,
            
            // Commentaires
            'commentaires_evaluateur' => $this->commentaires_evaluateur,
            'commentaires_evalue' => $this->commentaires_evalue,
            'commentaires_manager' => $this->commentaires_manager,
            'commentaires_rh' => $this->commentaires_rh,
            
            // Validation
            'validee_par_evalue' => $this->validee_par_evalue,
            'validee_par_manager' => $this->validee_par_manager,
            'validee_par_rh' => $this->validee_par_rh,
            'date_validation_evalue' => $this->date_validation_evalue?->format('Y-m-d'),
            'date_validation_manager' => $this->date_validation_manager?->format('Y-m-d'),
            'date_validation_rh' => $this->date_validation_rh?->format('Y-m-d'),
            
            // Métadonnées
            'version_grille' => $this->version_grille,
            'reference' => $this->reference,
            'notes_internes' => $this->notes_internes,
            
            // Recommandation
            'recommandation' => $this->recommandation,
            'recommandation_label' => $this->recommandation_label,
            'justification_recommandation' => $this->justification_recommandation,
            
            // États calculés
            'is_terminee' => $this->isTerminee(),
            'is_en_cours' => $this->isEnCours(),
            'is_validee' => $this->isValidee(),
            'is_en_retard' => $this->isEnRetard(),
            'duree' => $this->duree,
            
            // Relations
            'evaluable' => $this->whenLoaded('evaluable', function () {
                if ($this->evaluable_type === 'App\Models\Candidat') {
                    return [
                        'id' => $this->evaluable->id,
                        'nom_complet' => $this->evaluable->nom_complet,
                        'email' => $this->evaluable->email,
                        'type' => 'candidat'
                    ];
                } elseif ($this->evaluable_type === 'App\Models\Employe') {
                    return [
                        'id' => $this->evaluable->id,
                        'nom_complet' => $this->evaluable->nom . ' ' . $this->evaluable->prenom,
                        'email' => $this->evaluable->email,
                        'type' => 'employe'
                    ];
                }
                return null;
            }),
            'evaluateur' => $this->whenLoaded('evaluateur', function () {
                return [
                    'id' => $this->evaluateur->id,
                    'name' => $this->evaluateur->name,
                    'email' => $this->evaluateur->email,
                ];
            }),
            'manager' => $this->whenLoaded('manager', function () {
                return [
                    'id' => $this->manager->id,
                    'name' => $this->manager->name,
                    'email' => $this->manager->email,
                ];
            }),
            'departement' => $this->whenLoaded('departement', function () {
                return [
                    'id' => $this->departement->id,
                    'nom' => $this->departement->nom,
                    'description' => $this->departement->description,
                ];
            }),
            
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
