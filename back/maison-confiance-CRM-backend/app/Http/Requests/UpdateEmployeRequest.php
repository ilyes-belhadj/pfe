<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule; // Importez la classe Rule pour les règles de validation

class UpdateEmployeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * Détermine si l'utilisateur est autorisé à effectuer cette requête.
     */
    public function authorize(): bool
    {
        // Pour l'instant, nous mettons `true` pour permettre toute requête.
        // Dans un vrai projet, vous ajouteriez une logique d'autorisation ici,
        // par exemple, vérifier si l'utilisateur est un administrateur ou
        // s'il a la permission de modifier cet employé.
        // Exemple avec une Policy (plus tard) : $this->user()->can('update', $this->route('employe'));
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     * Récupère les règles de validation qui s'appliquent à la requête.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Récupère l'ID de l'employé depuis les paramètres de la route
        // C'est nécessaire pour la règle `unique` pour ignorer l'employé actuel
        // lors de la vérification de l'unicité de l'email.
        $employeId = $this->route('employe')->id ?? null;

        return [
            'nom' => 'sometimes|string|max:255', // `sometimes` signifie que le champ est optionnel pour la mise à jour
            'prenom' => 'sometimes|string|max:255',
            'email' => [
                'sometimes',
                'string',
                'email',
                'max:255',
                // Rule::unique('employes', 'email')->ignore($employeId), // Ignore l'employé actuel par son ID
                // La ligne ci-dessous est une alternative plus concise à la précédente
                Rule::unique('users')->ignore($employeId, 'id'), // Supposons que l'email est dans la table `users`
                // S'il est dans la table `employes` (si email est unique sur employes), utilisez :
                // Rule::unique('employes')->ignore($employeId),

            ],
            'date_embauche' => 'sometimes|date',
            'salaire' => 'sometimes|numeric|min:0',
            'departement_id' => 'sometimes|nullable|exists:departements,id',
            // Ajoutez ici d'autres champs que vous voulez pouvoir mettre à jour
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     * Récupère les messages d'erreur personnalisés pour les règles de validation.
     * (Optionnel : si vous voulez des messages plus spécifiques)
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'email.unique' => 'Cet email est déjà utilisé par un autre employé.',
            'departement_id.exists' => 'Le département sélectionné est invalide.',
            // ... autres messages personnalisés
        ];
    }
}