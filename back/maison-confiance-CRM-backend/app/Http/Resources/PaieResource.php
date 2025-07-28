<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaieResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'employe_id' => $this->employe_id,
            'employe' => [
                'id' => $this->employe->id,
                'nom' => $this->employe->nom,
                'prenom' => $this->employe->prenom,
                'nom_complet' => $this->employe->nom . ' ' . $this->employe->prenom,
                'email' => $this->employe->email,
                'departement' => $this->employe->departement->nom ?? 'Non assigné'
            ],
            'periode' => $this->periode,
            'date_paiement' => $this->date_paiement->format('Y-m-d'),
            'date_paiement_formatted' => $this->date_paiement->format('d/m/Y'),
            'salaire_base' => (float) $this->salaire_base,
            'heures_travaillees' => (float) $this->heures_travaillees,
            'taux_horaire' => (float) $this->taux_horaire,
            'salaire_brut' => (float) $this->salaire_brut,
            'primes' => (float) $this->primes,
            'deductions' => (float) $this->deductions,
            'cotisations_sociales' => (float) $this->cotisations_sociales,
            'impots' => (float) $this->impots,
            'salaire_net' => (float) $this->salaire_net,
            'statut' => $this->statut,
            'statut_label' => $this->getStatutLabel(),
            'notes' => $this->notes,
            'mode_paiement' => $this->mode_paiement,
            'mode_paiement_label' => $this->getModePaiementLabel(),
            'numero_cheque' => $this->numero_cheque,
            'reference_paiement' => $this->reference_paiement,
            'total_gains' => (float) $this->total_gains,
            'total_deductions' => (float) $this->total_deductions,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Obtenir le label du statut
     */
    private function getStatutLabel(): string
    {
        return match($this->statut) {
            'en_attente' => 'En attente',
            'paye' => 'Payé',
            'annule' => 'Annulé',
            default => 'Inconnu'
        };
    }

    /**
     * Obtenir le label du mode de paiement
     */
    private function getModePaiementLabel(): string
    {
        return match($this->mode_paiement) {
            'virement' => 'Virement bancaire',
            'cheque' => 'Chèque',
            'especes' => 'Espèces',
            default => 'Non défini'
        };
    }
}
