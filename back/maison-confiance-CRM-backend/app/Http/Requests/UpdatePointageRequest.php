<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePointageRequest extends FormRequest
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
            'date_pointage' => 'sometimes|date',
            'heure_entree' => 'sometimes|date_format:H:i:s',
            'heure_sortie' => 'sometimes|date_format:H:i:s',
            'heure_pause_debut' => 'sometimes|date_format:H:i:s',
            'heure_pause_fin' => 'sometimes|date_format:H:i:s',
            'heures_travaillees' => 'sometimes|numeric|min:0|max:24',
            'heures_pause' => 'sometimes|numeric|min:0|max:8',
            'heures_net' => 'sometimes|numeric|min:0|max:24',
            'statut' => 'sometimes|in:present,absent,en_pause,retard,depart_anticipé',
            'commentaire' => 'sometimes|string|max:1000',
            'lieu_pointage' => 'sometimes|string|max:100',
            'methode_pointage' => 'sometimes|in:manuel,badge,application,geolocalisation',
            'ip_address' => 'sometimes|ip',
            'user_agent' => 'sometimes|string|max:500',
            'latitude' => 'sometimes|numeric|between:-90,90',
            'longitude' => 'sometimes|numeric|between:-180,180',
            'valide' => 'sometimes|boolean',
            'valide_par' => 'sometimes|exists:users,id',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'employe_id.exists' => 'L\'employé sélectionné n\'existe pas.',
            'date_pointage.date' => 'La date de pointage doit être une date valide.',
            'heure_entree.date_format' => 'L\'heure d\'entrée doit être au format HH:MM:SS.',
            'heure_sortie.date_format' => 'L\'heure de sortie doit être au format HH:MM:SS.',
            'heure_pause_debut.date_format' => 'L\'heure de début de pause doit être au format HH:MM:SS.',
            'heure_pause_fin.date_format' => 'L\'heure de fin de pause doit être au format HH:MM:SS.',
            'heures_travaillees.numeric' => 'Les heures travaillées doivent être un nombre.',
            'heures_travaillees.min' => 'Les heures travaillées ne peuvent pas être négatives.',
            'heures_travaillees.max' => 'Les heures travaillées ne peuvent pas dépasser 24 heures.',
            'heures_pause.numeric' => 'Les heures de pause doivent être un nombre.',
            'heures_pause.min' => 'Les heures de pause ne peuvent pas être négatives.',
            'heures_pause.max' => 'Les heures de pause ne peuvent pas dépasser 8 heures.',
            'heures_net.numeric' => 'Les heures nettes doivent être un nombre.',
            'heures_net.min' => 'Les heures nettes ne peuvent pas être négatives.',
            'heures_net.max' => 'Les heures nettes ne peuvent pas dépasser 24 heures.',
            'statut.in' => 'Le statut doit être present, absent, en_pause, retard ou depart_anticipé.',
            'commentaire.max' => 'Le commentaire ne peut pas dépasser 1000 caractères.',
            'lieu_pointage.max' => 'Le lieu de pointage ne peut pas dépasser 100 caractères.',
            'methode_pointage.in' => 'La méthode de pointage doit être manuel, badge, application ou geolocalisation.',
            'ip_address.ip' => 'L\'adresse IP doit être valide.',
            'user_agent.max' => 'L\'agent utilisateur ne peut pas dépasser 500 caractères.',
            'latitude.numeric' => 'La latitude doit être un nombre.',
            'latitude.between' => 'La latitude doit être comprise entre -90 et 90.',
            'longitude.numeric' => 'La longitude doit être un nombre.',
            'longitude.between' => 'La longitude doit être comprise entre -180 et 180.',
            'valide.boolean' => 'Le champ valide doit être vrai ou faux.',
            'valide_par.exists' => 'L\'utilisateur de validation sélectionné n\'existe pas.',
        ];
    }
}
