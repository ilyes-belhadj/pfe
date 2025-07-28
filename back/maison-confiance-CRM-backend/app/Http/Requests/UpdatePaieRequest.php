<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePaieRequest extends FormRequest
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
            'employe_id' => 'sometimes|exists:employes,id',
            'periode' => 'sometimes|string|max:7',
            'date_paiement' => 'sometimes|date',
            'salaire_base' => 'sometimes|numeric|min:0',
            'heures_travaillees' => 'sometimes|numeric|min:0',
            'taux_horaire' => 'sometimes|numeric|min:0',
            'salaire_brut' => 'sometimes|numeric|min:0',
            'primes' => 'sometimes|numeric|min:0',
            'deductions' => 'sometimes|numeric|min:0',
            'cotisations_sociales' => 'sometimes|numeric|min:0',
            'impots' => 'sometimes|numeric|min:0',
            'salaire_net' => 'sometimes|numeric',
            'statut' => 'sometimes|in:en_attente,paye,annule',
            'notes' => 'sometimes|string|max:1000',
            'mode_paiement' => 'sometimes|in:virement,cheque,especes',
            'numero_cheque' => 'sometimes|string|max:50',
            'reference_paiement' => 'sometimes|string|max:100',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'employe_id.exists' => 'L\'employé sélectionné n\'existe pas.',
            'periode.string' => 'La période doit être une chaîne de caractères.',
            'periode.max' => 'La période ne peut pas dépasser 7 caractères.',
            'date_paiement.date' => 'La date de paiement doit être une date valide.',
            'salaire_base.numeric' => 'Le salaire de base doit être un nombre.',
            'salaire_base.min' => 'Le salaire de base ne peut pas être négatif.',
            'heures_travaillees.numeric' => 'Les heures travaillées doivent être un nombre.',
            'heures_travaillees.min' => 'Les heures travaillées ne peuvent pas être négatives.',
            'taux_horaire.numeric' => 'Le taux horaire doit être un nombre.',
            'taux_horaire.min' => 'Le taux horaire ne peut pas être négatif.',
            'salaire_brut.numeric' => 'Le salaire brut doit être un nombre.',
            'salaire_brut.min' => 'Le salaire brut ne peut pas être négatif.',
            'primes.numeric' => 'Les primes doivent être un nombre.',
            'primes.min' => 'Les primes ne peuvent pas être négatives.',
            'deductions.numeric' => 'Les déductions doivent être un nombre.',
            'deductions.min' => 'Les déductions ne peuvent pas être négatives.',
            'cotisations_sociales.numeric' => 'Les cotisations sociales doivent être un nombre.',
            'cotisations_sociales.min' => 'Les cotisations sociales ne peuvent pas être négatives.',
            'impots.numeric' => 'Les impôts doivent être un nombre.',
            'impots.min' => 'Les impôts ne peuvent pas être négatifs.',
            'salaire_net.numeric' => 'Le salaire net doit être un nombre.',
            'statut.in' => 'Le statut doit être en_attente, paye ou annule.',
            'notes.max' => 'Les notes ne peuvent pas dépasser 1000 caractères.',
            'mode_paiement.in' => 'Le mode de paiement doit être virement, cheque ou especes.',
            'numero_cheque.max' => 'Le numéro de chèque ne peut pas dépasser 50 caractères.',
            'reference_paiement.max' => 'La référence de paiement ne peut pas dépasser 100 caractères.',
        ];
    }
}
