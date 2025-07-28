<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEvaluationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'titre' => 'sometimes|string|max:255',
            'description' => 'sometimes|string|max:1000',
            'type' => 'sometimes|in:candidat,employe,periode_essai,annuelle,performance',
            'statut' => 'sometimes|in:brouillon,en_cours,terminee,validee,rejetee',
            'priorite' => 'sometimes|in:basse,normale,haute,urgente',
            
            // Relations polymorphiques
            'candidat_id' => 'sometimes|exists:candidats,id',
            'employe_id' => 'sometimes|exists:employes,id',
            
            // Évaluateur et manager
            'evaluateur_id' => 'sometimes|exists:users,id',
            'manager_id' => 'sometimes|exists:users,id',
            'departement_id' => 'sometimes|exists:departements,id',
            
            // Dates
            'date_evaluation' => 'sometimes|date',
            'date_limite' => 'sometimes|date|after_or_equal:date_evaluation',
            'date_validation' => 'sometimes|date|after_or_equal:date_evaluation',
            'prochaine_evaluation' => 'sometimes|date|after:date_evaluation',
            
            // Critères et résultats (JSON)
            'criteres_evaluation' => 'sometimes|array',
            'resultats' => 'sometimes|array',
            
            // Notes
            'note_globale' => 'sometimes|numeric|min:0|max:10',
            'note_competences' => 'sometimes|numeric|min:0|max:10',
            'note_performance' => 'sometimes|numeric|min:0|max:10',
            'note_comportement' => 'sometimes|numeric|min:0|max:10',
            'note_potentiel' => 'sometimes|numeric|min:0|max:10',
            
            // Évaluations détaillées
            'forces' => 'sometimes|string|max:2000',
            'axes_amelioration' => 'sometimes|string|max:2000',
            'objectifs' => 'sometimes|string|max:2000',
            'recommandations' => 'sometimes|string|max:2000',
            
            // Commentaires
            'commentaires_evaluateur' => 'sometimes|string|max:2000',
            'commentaires_evalue' => 'sometimes|string|max:2000',
            'commentaires_manager' => 'sometimes|string|max:2000',
            'commentaires_rh' => 'sometimes|string|max:2000',
            
            // Validation
            'validee_par_evalue' => 'sometimes|boolean',
            'validee_par_manager' => 'sometimes|boolean',
            'validee_par_rh' => 'sometimes|boolean',
            'date_validation_evalue' => 'sometimes|date',
            'date_validation_manager' => 'sometimes|date',
            'date_validation_rh' => 'sometimes|date',
            
            // Métadonnées
            'version_grille' => 'sometimes|string|max:50',
            'reference' => 'sometimes|string|max:100',
            'notes_internes' => 'sometimes|string|max:2000',
            
            // Recommandation
            'recommandation' => 'sometimes|in:embauche,confirmation,promotion,formation,sanction,licenciement',
            'justification_recommandation' => 'sometimes|string|max:1000',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'titre.max' => 'Le titre ne peut pas dépasser 255 caractères.',
            'description.max' => 'La description ne peut pas dépasser 1000 caractères.',
            'type.in' => 'Le type doit être candidat, employe, periode_essai, annuelle ou performance.',
            'statut.in' => 'Le statut doit être une valeur valide.',
            'priorite.in' => 'La priorité doit être basse, normale, haute ou urgente.',
            'candidat_id.exists' => 'Le candidat sélectionné n\'existe pas.',
            'employe_id.exists' => 'L\'employé sélectionné n\'existe pas.',
            'evaluateur_id.exists' => 'L\'évaluateur sélectionné n\'existe pas.',
            'manager_id.exists' => 'Le manager sélectionné n\'existe pas.',
            'departement_id.exists' => 'Le département sélectionné n\'existe pas.',
            'date_evaluation.date' => 'La date d\'évaluation doit être une date valide.',
            'date_limite.date' => 'La date limite doit être une date valide.',
            'date_limite.after_or_equal' => 'La date limite doit être postérieure ou égale à la date d\'évaluation.',
            'date_validation.date' => 'La date de validation doit être une date valide.',
            'date_validation.after_or_equal' => 'La date de validation doit être postérieure ou égale à la date d\'évaluation.',
            'prochaine_evaluation.date' => 'La date de prochaine évaluation doit être une date valide.',
            'prochaine_evaluation.after' => 'La date de prochaine évaluation doit être postérieure à la date d\'évaluation.',
            'criteres_evaluation.array' => 'Les critères d\'évaluation doivent être un tableau.',
            'resultats.array' => 'Les résultats doivent être un tableau.',
            'note_globale.numeric' => 'La note globale doit être un nombre.',
            'note_globale.min' => 'La note globale ne peut pas être négative.',
            'note_globale.max' => 'La note globale ne peut pas dépasser 10.',
            'note_competences.numeric' => 'La note compétences doit être un nombre.',
            'note_competences.min' => 'La note compétences ne peut pas être négative.',
            'note_competences.max' => 'La note compétences ne peut pas dépasser 10.',
            'note_performance.numeric' => 'La note performance doit être un nombre.',
            'note_performance.min' => 'La note performance ne peut pas être négative.',
            'note_performance.max' => 'La note performance ne peut pas dépasser 10.',
            'note_comportement.numeric' => 'La note comportement doit être un nombre.',
            'note_comportement.min' => 'La note comportement ne peut pas être négative.',
            'note_comportement.max' => 'La note comportement ne peut pas dépasser 10.',
            'note_potentiel.numeric' => 'La note potentiel doit être un nombre.',
            'note_potentiel.min' => 'La note potentiel ne peut pas être négative.',
            'note_potentiel.max' => 'La note potentiel ne peut pas dépasser 10.',
            'forces.max' => 'Les forces ne peuvent pas dépasser 2000 caractères.',
            'axes_amelioration.max' => 'Les axes d\'amélioration ne peuvent pas dépasser 2000 caractères.',
            'objectifs.max' => 'Les objectifs ne peuvent pas dépasser 2000 caractères.',
            'recommandations.max' => 'Les recommandations ne peuvent pas dépasser 2000 caractères.',
            'commentaires_evaluateur.max' => 'Les commentaires de l\'évaluateur ne peuvent pas dépasser 2000 caractères.',
            'commentaires_evalue.max' => 'Les commentaires de l\'évalué ne peuvent pas dépasser 2000 caractères.',
            'commentaires_manager.max' => 'Les commentaires du manager ne peuvent pas dépasser 2000 caractères.',
            'commentaires_rh.max' => 'Les commentaires RH ne peuvent pas dépasser 2000 caractères.',
            'validee_par_evalue.boolean' => 'Le champ validée par évalué doit être vrai ou faux.',
            'validee_par_manager.boolean' => 'Le champ validée par manager doit être vrai ou faux.',
            'validee_par_rh.boolean' => 'Le champ validée par RH doit être vrai ou faux.',
            'date_validation_evalue.date' => 'La date de validation évalué doit être une date valide.',
            'date_validation_manager.date' => 'La date de validation manager doit être une date valide.',
            'date_validation_rh.date' => 'La date de validation RH doit être une date valide.',
            'version_grille.max' => 'La version de la grille ne peut pas dépasser 50 caractères.',
            'reference.max' => 'La référence ne peut pas dépasser 100 caractères.',
            'notes_internes.max' => 'Les notes internes ne peuvent pas dépasser 2000 caractères.',
            'recommandation.in' => 'La recommandation doit être une valeur valide.',
            'justification_recommandation.max' => 'La justification de la recommandation ne peut pas dépasser 1000 caractères.',
        ];
    }
}
