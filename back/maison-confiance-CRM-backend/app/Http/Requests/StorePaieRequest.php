<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePaieRequest extends FormRequest
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
            'employe_id' => 'required|exists:employes,id',
            'periode' => 'required|string|max:7', // Format: YYYY-MM
            'date_paiement' => 'required|date',
            'salaire_base' => 'required|numeric|min:0',
            'heures_travaillees' => 'nullable|numeric|min:0',
            'taux_horaire' => 'nullable|numeric|min:0',
            'salaire_brut' => 'nullable|numeric|min:0',
            'primes' => 'nullable|numeric|min:0',
            'deductions' => 'nullable|numeric|min:0',
            'cotisations_sociales' => 'nullable|numeric|min:0',
            'impots' => 'nullable|numeric|min:0',
            'salaire_net' => 'nullable|numeric',
            'statut' => 'nullable|in:en_attente,paye,annule',
            'notes' => 'nullable|string|max:1000',
            'mode_paiement' => 'nullable|in:virement,cheque,especes',
            'numero_cheque' => 'nullable|string|max:50',
            'reference_paiement' => 'nullable|string|max:100',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'employe_id.required' => 'L\'employé est requis.',
            'employe_id.exists' => 'L\'employé sélectionné n\'existe pas.',
            'periode.required' => 'La période est requise.',
            'periode.string' => 'La période doit être une chaîne de caractères.',
            'periode.max' => 'La période ne peut pas dépasser 7 caractères.',
            'date_paiement.required' => 'La date de paiement est requise.',
            'date_paiement.date' => 'La date de paiement doit être une date valide.',
            'salaire_base.required' => 'Le salaire de base est requis.',
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
