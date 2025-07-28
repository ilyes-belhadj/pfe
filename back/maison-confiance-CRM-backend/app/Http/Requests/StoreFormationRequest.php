<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFormationRequest extends FormRequest
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
            'description' => 'required|string',
            'formateur' => 'required|string|max:255',
            'date_debut' => 'required|date|after_or_equal:today',
            'date_fin' => 'required|date|after:date_debut',
            'duree_heures' => 'required|integer|min:1',
            'cout' => 'required|numeric|min:0',
            'statut' => 'sometimes|in:planifie,en_cours,termine,annule',
            'lieu' => 'nullable|string|max:255',
            'nombre_places' => 'required|integer|min:1',
            'places_occupees' => 'sometimes|integer|min:0',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'titre.required' => 'Le titre est obligatoire',
            'description.required' => 'La description est obligatoire',
            'formateur.required' => 'Le formateur est obligatoire',
            'date_debut.required' => 'La date de début est obligatoire',
            'date_debut.after_or_equal' => 'La date de début doit être aujourd\'hui ou dans le futur',
            'date_fin.required' => 'La date de fin est obligatoire',
            'date_fin.after' => 'La date de fin doit être après la date de début',
            'duree_heures.required' => 'La durée en heures est obligatoire',
            'duree_heures.min' => 'La durée doit être d\'au moins 1 heure',
            'cout.required' => 'Le coût est obligatoire',
            'cout.min' => 'Le coût doit être positif',
            'nombre_places.required' => 'Le nombre de places est obligatoire',
            'nombre_places.min' => 'Le nombre de places doit être d\'au moins 1',
        ];
    }
}
