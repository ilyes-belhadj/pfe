<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDepartementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // À modifier pour une autorisation réelle
    }

    public function rules(): array
    {
        $departementId = $this->route('departement')->id ?? null; // Récupère l'ID du département

        return [
            'name' => [
                'sometimes',
                'string',
                'max:255',
                Rule::unique('departements', 'name')->ignore($departementId), // Ignore le département actuel
            ],
            'description' => 'sometimes|nullable|string',
        ];
    }
}