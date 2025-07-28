<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCandidatureRequest extends FormRequest
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
            'candidat_id' => 'required|exists:candidats,id',
            'departement_id' => 'nullable|exists:departements,id',
            'poste_souhaite' => 'required|string|max:255',
            'lettre_motivation' => 'nullable|string|max:5000',
            'cv' => 'nullable|file|mimes:pdf,doc,docx|max:10240', // 10MB max
            'lettre_motivation_file' => 'nullable|file|mimes:pdf,doc,docx|max:5120', // 5MB max
            'statut' => 'nullable|in:nouvelle,en_cours,entretien_telephone,entretien_rh,entretien_technique,entretien_final,test_technique,reference_check,offre_envoyee,offre_acceptee,embauche,refusee,annulee',
            'priorite' => 'nullable|in:basse,normale,haute,urgente',
            'date_candidature' => 'nullable|date',
            'date_derniere_action' => 'nullable|date',
            'date_entretien' => 'nullable|date|after:today',
            'heure_entretien' => 'nullable|date_format:H:i',
            'lieu_entretien' => 'nullable|string|max:255',
            'notes_entretien' => 'nullable|string|max:2000',
            'evaluation' => 'nullable|string|max:2000',
            'note_globale' => 'nullable|numeric|min:0|max:10',
            'commentaires_rh' => 'nullable|string|max:2000',
            'commentaires_technique' => 'nullable|string|max:2000',
            'commentaires_manager' => 'nullable|string|max:2000',
            'source_candidature' => 'nullable|string|max:100',
            'campagne_recrutement' => 'nullable|string|max:100',
            'candidature_spontanee' => 'nullable|boolean',
            'offre_reference' => 'nullable|string|max:255',
            'salaire_propose' => 'nullable|numeric|min:0|max:999999.99',
            'date_debut_souhaite' => 'nullable|date|after:today',
            'motif_refus' => 'nullable|string|max:1000',
            'recruteur_id' => 'nullable|exists:users,id',
            'manager_id' => 'nullable|exists:users,id',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'candidat_id.required' => 'Le candidat est requis.',
            'candidat_id.exists' => 'Le candidat sélectionné n\'existe pas.',
            'departement_id.exists' => 'Le département sélectionné n\'existe pas.',
            'poste_souhaite.required' => 'Le poste souhaité est requis.',
            'poste_souhaite.max' => 'Le poste souhaité ne peut pas dépasser 255 caractères.',
            'lettre_motivation.max' => 'La lettre de motivation ne peut pas dépasser 5000 caractères.',
            'cv.file' => 'Le CV doit être un fichier.',
            'cv.mimes' => 'Le CV doit être au format PDF, DOC ou DOCX.',
            'cv.max' => 'Le CV ne peut pas dépasser 10MB.',
            'lettre_motivation_file.file' => 'La lettre de motivation doit être un fichier.',
            'lettre_motivation_file.mimes' => 'La lettre de motivation doit être au format PDF, DOC ou DOCX.',
            'lettre_motivation_file.max' => 'La lettre de motivation ne peut pas dépasser 5MB.',
            'statut.in' => 'Le statut doit être une valeur valide.',
            'priorite.in' => 'La priorité doit être basse, normale, haute ou urgente.',
            'date_candidature.date' => 'La date de candidature doit être une date valide.',
            'date_derniere_action.date' => 'La date de dernière action doit être une date valide.',
            'date_entretien.date' => 'La date d\'entretien doit être une date valide.',
            'date_entretien.after' => 'La date d\'entretien doit être postérieure à aujourd\'hui.',
            'heure_entretien.date_format' => 'L\'heure d\'entretien doit être au format HH:MM.',
            'lieu_entretien.max' => 'Le lieu d\'entretien ne peut pas dépasser 255 caractères.',
            'notes_entretien.max' => 'Les notes d\'entretien ne peuvent pas dépasser 2000 caractères.',
            'evaluation.max' => 'L\'évaluation ne peut pas dépasser 2000 caractères.',
            'note_globale.numeric' => 'La note globale doit être un nombre.',
            'note_globale.min' => 'La note globale ne peut pas être négative.',
            'note_globale.max' => 'La note globale ne peut pas dépasser 10.',
            'commentaires_rh.max' => 'Les commentaires RH ne peuvent pas dépasser 2000 caractères.',
            'commentaires_technique.max' => 'Les commentaires techniques ne peuvent pas dépasser 2000 caractères.',
            'commentaires_manager.max' => 'Les commentaires manager ne peuvent pas dépasser 2000 caractères.',
            'source_candidature.max' => 'La source de candidature ne peut pas dépasser 100 caractères.',
            'campagne_recrutement.max' => 'La campagne de recrutement ne peut pas dépasser 100 caractères.',
            'candidature_spontanee.boolean' => 'Le champ candidature spontanée doit être vrai ou faux.',
            'offre_reference.max' => 'La référence d\'offre ne peut pas dépasser 255 caractères.',
            'salaire_propose.numeric' => 'Le salaire proposé doit être un nombre.',
            'salaire_propose.min' => 'Le salaire proposé ne peut pas être négatif.',
            'salaire_propose.max' => 'Le salaire proposé ne peut pas dépasser 999999.99.',
            'date_debut_souhaite.date' => 'La date de début souhaitée doit être une date valide.',
            'date_debut_souhaite.after' => 'La date de début souhaitée doit être postérieure à aujourd\'hui.',
            'motif_refus.max' => 'Le motif de refus ne peut pas dépasser 1000 caractères.',
            'recruteur_id.exists' => 'Le recruteur sélectionné n\'existe pas.',
            'manager_id.exists' => 'Le manager sélectionné n\'existe pas.',
        ];
    }
}
