<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFormationRequest extends FormRequest
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
            'description' => 'sometimes|string',
            'formateur' => 'sometimes|string|max:255',
            'date_debut' => 'sometimes|date',
            'date_fin' => 'sometimes|date|after:date_debut',
            'duree_heures' => 'sometimes|integer|min:1',
            'cout' => 'sometimes|numeric|min:0',
            'statut' => 'sometimes|in:planifie,en_cours,termine,annule',
            'lieu' => 'sometimes|nullable|string|max:255',
            'nombre_places' => 'sometimes|integer|min:1',
            'places_occupees' => 'sometimes|integer|min:0',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'titre.string' => 'Le titre doit être une chaîne de caractères',
            'titre.max' => 'Le titre ne peut pas dépasser 255 caractères',
            'description.string' => 'La description doit être une chaîne de caractères',
            'formateur.string' => 'Le formateur doit être une chaîne de caractères',
            'formateur.max' => 'Le nom du formateur ne peut pas dépasser 255 caractères',
            'date_debut.date' => 'La date de début doit être une date valide',
            'date_fin.date' => 'La date de fin doit être une date valide',
            'date_fin.after' => 'La date de fin doit être après la date de début',
            'duree_heures.integer' => 'La durée doit être un nombre entier',
            'duree_heures.min' => 'La durée doit être d\'au moins 1 heure',
            'cout.numeric' => 'Le coût doit être un nombre',
            'cout.min' => 'Le coût doit être positif',
            'statut.in' => 'Le statut doit être planifié, en cours, terminé ou annulé',
            'lieu.string' => 'Le lieu doit être une chaîne de caractères',
            'lieu.max' => 'Le lieu ne peut pas dépasser 255 caractères',
            'nombre_places.integer' => 'Le nombre de places doit être un nombre entier',
            'nombre_places.min' => 'Le nombre de places doit être d\'au moins 1',
            'places_occupees.integer' => 'Le nombre de places occupées doit être un nombre entier',
            'places_occupees.min' => 'Le nombre de places occupées doit être positif',
        ];
    }
}
