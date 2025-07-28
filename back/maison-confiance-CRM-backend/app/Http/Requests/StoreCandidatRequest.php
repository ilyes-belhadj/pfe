<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCandidatRequest extends FormRequest
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
            'nom' => 'required|string|max:100',
            'prenom' => 'required|string|max:100',
            'email' => 'required|email|unique:candidats,email',
            'telephone' => 'nullable|string|max:20',
            'adresse' => 'nullable|string|max:255',
            'ville' => 'nullable|string|max:100',
            'code_postal' => 'nullable|string|max:10',
            'pays' => 'nullable|string|max:100',
            'date_naissance' => 'nullable|date|before:today',
            'lieu_naissance' => 'nullable|string|max:100',
            'nationalite' => 'nullable|string|max:100',
            'civilite' => 'nullable|in:M,Mme,Mlle',
            'linkedin' => 'nullable|url|max:255',
            'site_web' => 'nullable|url|max:255',
            'bio' => 'nullable|string|max:1000',
            'competences' => 'nullable|string|max:1000',
            'experiences' => 'nullable|string|max:2000',
            'formation' => 'nullable|string|max:1000',
            'disponibilite' => 'nullable|string|max:100',
            'pretention_salaire' => 'nullable|numeric|min:0|max:999999.99',
            'mobilite' => 'nullable|string|max:100',
            'statut' => 'nullable|in:actif,inactif,blacklist',
            'notes' => 'nullable|string|max:2000',
            'source_recrutement' => 'nullable|string|max:100',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'nom.required' => 'Le nom est requis.',
            'nom.max' => 'Le nom ne peut pas dépasser 100 caractères.',
            'prenom.required' => 'Le prénom est requis.',
            'prenom.max' => 'Le prénom ne peut pas dépasser 100 caractères.',
            'email.required' => 'L\'email est requis.',
            'email.email' => 'L\'email doit être une adresse email valide.',
            'email.unique' => 'Cette adresse email est déjà utilisée.',
            'telephone.max' => 'Le numéro de téléphone ne peut pas dépasser 20 caractères.',
            'adresse.max' => 'L\'adresse ne peut pas dépasser 255 caractères.',
            'ville.max' => 'La ville ne peut pas dépasser 100 caractères.',
            'code_postal.max' => 'Le code postal ne peut pas dépasser 10 caractères.',
            'pays.max' => 'Le pays ne peut pas dépasser 100 caractères.',
            'date_naissance.date' => 'La date de naissance doit être une date valide.',
            'date_naissance.before' => 'La date de naissance doit être antérieure à aujourd\'hui.',
            'lieu_naissance.max' => 'Le lieu de naissance ne peut pas dépasser 100 caractères.',
            'nationalite.max' => 'La nationalité ne peut pas dépasser 100 caractères.',
            'civilite.in' => 'La civilité doit être M, Mme ou Mlle.',
            'linkedin.url' => 'Le lien LinkedIn doit être une URL valide.',
            'linkedin.max' => 'Le lien LinkedIn ne peut pas dépasser 255 caractères.',
            'site_web.url' => 'Le site web doit être une URL valide.',
            'site_web.max' => 'Le site web ne peut pas dépasser 255 caractères.',
            'bio.max' => 'La biographie ne peut pas dépasser 1000 caractères.',
            'competences.max' => 'Les compétences ne peuvent pas dépasser 1000 caractères.',
            'experiences.max' => 'Les expériences ne peuvent pas dépasser 2000 caractères.',
            'formation.max' => 'La formation ne peut pas dépasser 1000 caractères.',
            'disponibilite.max' => 'La disponibilité ne peut pas dépasser 100 caractères.',
            'pretention_salaire.numeric' => 'Les prétentions salariales doivent être un nombre.',
            'pretention_salaire.min' => 'Les prétentions salariales ne peuvent pas être négatives.',
            'pretention_salaire.max' => 'Les prétentions salariales ne peuvent pas dépasser 999999.99.',
            'mobilite.max' => 'La mobilité ne peut pas dépasser 100 caractères.',
            'statut.in' => 'Le statut doit être actif, inactif ou blacklist.',
            'notes.max' => 'Les notes ne peuvent pas dépasser 2000 caractères.',
            'source_recrutement.max' => 'La source de recrutement ne peut pas dépasser 100 caractères.',
        ];
    }
}
