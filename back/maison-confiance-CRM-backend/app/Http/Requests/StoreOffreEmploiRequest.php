<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOffreEmploiRequest extends FormRequest
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
            'description' => 'required|string|max:5000',
            'profil_recherche' => 'nullable|string|max:2000',
            'missions' => 'nullable|string|max:2000',
            'competences_requises' => 'nullable|string|max:2000',
            'avantages' => 'nullable|string|max:2000',
            
            // Informations du poste
            'type_contrat' => 'required|in:CDI,CDD,Stage,Alternance,Freelance,Interim',
            'niveau_experience' => 'required|in:debutant,intermediaire,confirme,expert',
            'niveau_etude' => 'nullable|string|max:100',
            'lieu_travail' => 'required|string|max:255',
            'mode_travail' => 'nullable|in:presentiel,hybride,teletravail',
            'nombre_poste' => 'nullable|integer|min:1',
            
            // Rémunération
            'salaire_min' => 'nullable|numeric|min:0',
            'salaire_max' => 'nullable|numeric|min:0|gte:salaire_min',
            'devise_salaire' => 'nullable|string|max:10',
            'periode_salaire' => 'nullable|in:horaire,mensuel,annuel',
            
            // Dates
            'date_publication' => 'nullable|date',
            'date_limite_candidature' => 'nullable|date|after_or_equal:date_publication',
            'date_debut_poste' => 'nullable|date|after_or_equal:date_publication',
            'date_fin_publication' => 'nullable|date|after:date_publication',
            
            // Statut et visibilité
            'statut' => 'nullable|in:brouillon,active,en_cours,terminee,archivee',
            'publiee' => 'nullable|boolean',
            'urgente' => 'nullable|boolean',
            'sponsorisee' => 'nullable|boolean',
            
            // Relations
            'departement_id' => 'nullable|exists:departements,id',
            'recruteur_id' => 'nullable|exists:users,id',
            'manager_id' => 'nullable|exists:users,id',
            
            // Métadonnées
            'reference' => 'nullable|string|max:100',
            'source' => 'nullable|string|max:255',
            'notes_internes' => 'nullable|string|max:2000',
            'tags' => 'nullable|array',
            
            // SEO et marketing
            'slug' => 'nullable|string|max:255|unique:offre_emplois,slug',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:500',
            
            // Configuration
            'auto_archive' => 'nullable|boolean',
            'notifications_email' => 'nullable|boolean',
            'notifications_sms' => 'nullable|boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'titre.required' => 'Le titre est requis.',
            'titre.max' => 'Le titre ne peut pas dépasser 255 caractères.',
            'description.required' => 'La description est requise.',
            'description.max' => 'La description ne peut pas dépasser 5000 caractères.',
            'profil_recherche.max' => 'Le profil recherché ne peut pas dépasser 2000 caractères.',
            'missions.max' => 'Les missions ne peuvent pas dépasser 2000 caractères.',
            'competences_requises.max' => 'Les compétences requises ne peuvent pas dépasser 2000 caractères.',
            'avantages.max' => 'Les avantages ne peuvent pas dépasser 2000 caractères.',
            'type_contrat.required' => 'Le type de contrat est requis.',
            'type_contrat.in' => 'Le type de contrat doit être CDI, CDD, Stage, Alternance, Freelance ou Intérim.',
            'niveau_experience.required' => 'Le niveau d\'expérience est requis.',
            'niveau_experience.in' => 'Le niveau d\'expérience doit être débutant, intermédiaire, confirmé ou expert.',
            'niveau_etude.max' => 'Le niveau d\'étude ne peut pas dépasser 100 caractères.',
            'lieu_travail.required' => 'Le lieu de travail est requis.',
            'lieu_travail.max' => 'Le lieu de travail ne peut pas dépasser 255 caractères.',
            'mode_travail.in' => 'Le mode de travail doit être présentiel, hybride ou télétravail.',
            'nombre_poste.integer' => 'Le nombre de postes doit être un nombre entier.',
            'nombre_poste.min' => 'Le nombre de postes doit être au moins 1.',
            'salaire_min.numeric' => 'Le salaire minimum doit être un nombre.',
            'salaire_min.min' => 'Le salaire minimum ne peut pas être négatif.',
            'salaire_max.numeric' => 'Le salaire maximum doit être un nombre.',
            'salaire_max.min' => 'Le salaire maximum ne peut pas être négatif.',
            'salaire_max.gte' => 'Le salaire maximum doit être supérieur ou égal au salaire minimum.',
            'devise_salaire.max' => 'La devise du salaire ne peut pas dépasser 10 caractères.',
            'periode_salaire.in' => 'La période de salaire doit être horaire, mensuel ou annuel.',
            'date_publication.date' => 'La date de publication doit être une date valide.',
            'date_limite_candidature.date' => 'La date limite de candidature doit être une date valide.',
            'date_limite_candidature.after_or_equal' => 'La date limite de candidature doit être postérieure ou égale à la date de publication.',
            'date_debut_poste.date' => 'La date de début de poste doit être une date valide.',
            'date_debut_poste.after_or_equal' => 'La date de début de poste doit être postérieure ou égale à la date de publication.',
            'date_fin_publication.date' => 'La date de fin de publication doit être une date valide.',
            'date_fin_publication.after' => 'La date de fin de publication doit être postérieure à la date de publication.',
            'statut.in' => 'Le statut doit être une valeur valide.',
            'publiee.boolean' => 'Le champ publiée doit être vrai ou faux.',
            'urgente.boolean' => 'Le champ urgente doit être vrai ou faux.',
            'sponsorisee.boolean' => 'Le champ sponsorisée doit être vrai ou faux.',
            'departement_id.exists' => 'Le département sélectionné n\'existe pas.',
            'recruteur_id.exists' => 'Le recruteur sélectionné n\'existe pas.',
            'manager_id.exists' => 'Le manager sélectionné n\'existe pas.',
            'reference.max' => 'La référence ne peut pas dépasser 100 caractères.',
            'source.max' => 'La source ne peut pas dépasser 255 caractères.',
            'notes_internes.max' => 'Les notes internes ne peuvent pas dépasser 2000 caractères.',
            'tags.array' => 'Les tags doivent être un tableau.',
            'slug.max' => 'Le slug ne peut pas dépasser 255 caractères.',
            'slug.unique' => 'Ce slug est déjà utilisé.',
            'meta_description.max' => 'La meta description ne peut pas dépasser 500 caractères.',
            'meta_keywords.max' => 'Les meta keywords ne peuvent pas dépasser 500 caractères.',
            'auto_archive.boolean' => 'Le champ auto archive doit être vrai ou faux.',
            'notifications_email.boolean' => 'Le champ notifications email doit être vrai ou faux.',
            'notifications_sms.boolean' => 'Le champ notifications SMS doit être vrai ou faux.',
        ];
    }
}
