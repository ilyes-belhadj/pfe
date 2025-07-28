<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEvaluationRequest extends FormRequest
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
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'type' => 'required|in:candidat,employe,periode_essai,annuelle,performance',
            'statut' => 'nullable|in:brouillon,en_cours,terminee,validee,rejetee',
            'priorite' => 'nullable|in:basse,normale,haute,urgente',
            
            // Relations polymorphiques - au moins une requise
            'candidat_id' => 'nullable|exists:candidats,id',
            'employe_id' => 'nullable|exists:employes,id',
            
            // Évaluateur et manager
            'evaluateur_id' => 'required|exists:users,id',
            'manager_id' => 'nullable|exists:users,id',
            'departement_id' => 'nullable|exists:departements,id',
            
            // Dates
            'date_evaluation' => 'required|date',
            'date_limite' => 'nullable|date|after_or_equal:date_evaluation',
            'date_validation' => 'nullable|date|after_or_equal:date_evaluation',
            'prochaine_evaluation' => 'nullable|date|after:date_evaluation',
            
            // Critères et résultats (JSON)
            'criteres_evaluation' => 'nullable|array',
            'resultats' => 'nullable|array',
            
            // Notes
            'note_globale' => 'nullable|numeric|min:0|max:10',
            'note_competences' => 'nullable|numeric|min:0|max:10',
            'note_performance' => 'nullable|numeric|min:0|max:10',
            'note_comportement' => 'nullable|numeric|min:0|max:10',
            'note_potentiel' => 'nullable|numeric|min:0|max:10',
            
            // Évaluations détaillées
            'forces' => 'nullable|string|max:2000',
            'axes_amelioration' => 'nullable|string|max:2000',
            'objectifs' => 'nullable|string|max:2000',
            'recommandations' => 'nullable|string|max:2000',
            
            // Commentaires
            'commentaires_evaluateur' => 'nullable|string|max:2000',
            'commentaires_evalue' => 'nullable|string|max:2000',
            'commentaires_manager' => 'nullable|string|max:2000',
            'commentaires_rh' => 'nullable|string|max:2000',
            
            // Validation
            'validee_par_evalue' => 'nullable|boolean',
            'validee_par_manager' => 'nullable|boolean',
            'validee_par_rh' => 'nullable|boolean',
            'date_validation_evalue' => 'nullable|date',
            'date_validation_manager' => 'nullable|date',
            'date_validation_rh' => 'nullable|date',
            
            // Métadonnées
            'version_grille' => 'nullable|string|max:50',
            'reference' => 'nullable|string|max:100',
            'notes_internes' => 'nullable|string|max:2000',
            
            // Recommandation
            'recommandation' => 'nullable|in:embauche,confirmation,promotion,formation,sanction,licenciement',
            'justification_recommandation' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Vérifier qu'au moins candidat_id ou employe_id est fourni
            if (!$this->filled('candidat_id') && !$this->filled('employe_id')) {
                $validator->errors()->add('evaluable', 'Vous devez spécifier soit un candidat soit un employé.');
            }
            
            // Vérifier qu'on ne spécifie pas les deux à la fois
            if ($this->filled('candidat_id') && $this->filled('employe_id')) {
                $validator->errors()->add('evaluable', 'Vous ne pouvez pas spécifier à la fois un candidat et un employé.');
            }
        });
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'titre.required' => 'Le titre est requis.',
            'titre.max' => 'Le titre ne peut pas dépasser 255 caractères.',
            'description.max' => 'La description ne peut pas dépasser 1000 caractères.',
            'type.required' => 'Le type d\'évaluation est requis.',
            'type.in' => 'Le type doit être candidat, employe, periode_essai, annuelle ou performance.',
            'statut.in' => 'Le statut doit être une valeur valide.',
            'priorite.in' => 'La priorité doit être basse, normale, haute ou urgente.',
            'candidat_id.exists' => 'Le candidat sélectionné n\'existe pas.',
            'employe_id.exists' => 'L\'employé sélectionné n\'existe pas.',
            'evaluateur_id.required' => 'L\'évaluateur est requis.',
            'evaluateur_id.exists' => 'L\'évaluateur sélectionné n\'existe pas.',
            'manager_id.exists' => 'Le manager sélectionné n\'existe pas.',
            'departement_id.exists' => 'Le département sélectionné n\'existe pas.',
            'date_evaluation.required' => 'La date d\'évaluation est requise.',
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
