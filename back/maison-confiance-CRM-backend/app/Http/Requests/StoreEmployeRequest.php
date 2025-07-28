<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Ou Auth::check() / $this->user()->can('create', Employe::class);
    }

    public function rules(): array
    {
        return [
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|unique:employes,email',
            'date_embauche' => 'required|date',
            'salaire' => 'nullable|numeric|min:0',
            'departement_id' => 'nullable|exists:departements,id',
        ];
    }
}