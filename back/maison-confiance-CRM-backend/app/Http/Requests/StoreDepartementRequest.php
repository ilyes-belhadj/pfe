<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDepartementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // À modifier pour une autorisation réelle
    }

    public function rules(): array
    {
        return [
            'nom' => 'required|string|max:255|unique:departements,nom', // Nom unique
            'description' => 'nullable|string',
        ];
    }
}