<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Virement;
use App\Models\Versement;
use Carbon\Carbon;

class ControleCoherenceService
{
    public static function verifierFacture(array $donnees, int $userId, ?int $ignorerId = null): array
    {
        $alertes = [];

        $montantHt = (float) ($donnees['montant_ht'] ?? 0);
        $montantTtc = $montantHt + (float) ($donnees['tva'] ?? 0);

        if ($montantHt <= 0) {
            $alertes[] = "Le montant HT est nul ou négatif.";
        }

        $moyenne = Invoice::where('user_id', $userId)
            ->when($ignorerId, fn ($q) => $q->where('id', '!=', $ignorerId))
            ->avg('montant_ht');

        if ($moyenne && $montantHt > $moyenne * 5) {
            $alertes[] = "Ce montant HT (" . number_format($montantHt, 2, ',', ' ') . " DH) est anormalement élevé par rapport à la moyenne habituelle (" . number_format($moyenne, 2, ',', ' ') . " DH).";
        }

        if (!empty($donnees['client']) && !empty($donnees['date_facture'])) {

            $doublon = Invoice::where('user_id', $userId)
                ->where('client', $donnees['client'])
                ->where('montant_ttc', $montantTtc)
                ->whereBetween('date_facture', [
                    Carbon::parse($donnees['date_facture'])->subDays(3),
                    Carbon::parse($donnees['date_facture'])->addDays(3),
                ])
                ->when($ignorerId, fn ($q) => $q->where('id', '!=', $ignorerId))
                ->exists();

            if ($doublon) {
                $alertes[] = "Une facture similaire existe déjà pour ce client (même montant, date proche). Doublon potentiel.";
            }
        }

        return $alertes;
    }

    public static function verifierVirement(array $donnees, int $userId, ?int $ignorerId = null): array
    {
        $alertes = [];

        $montant = (float) ($donnees['montant'] ?? 0);

        if ($montant <= 0) {
            $alertes[] = "Le montant du virement est nul ou négatif.";
        }

        $moyenne = Virement::where('user_id', $userId)
            ->when($ignorerId, fn ($q) => $q->where('id', '!=', $ignorerId))
            ->avg('montant');

        if ($moyenne && $montant > $moyenne * 5) {
            $alertes[] = "Ce montant (" . number_format($montant, 2, ',', ' ') . " DH) est anormalement élevé par rapport à la moyenne habituelle (" . number_format($moyenne, 2, ',', ' ') . " DH).";
        }

        if (!empty($donnees['beneficiaire']) && !empty($donnees['date_virement'])) {

            $doublon = Virement::where('user_id', $userId)
                ->where('beneficiaire', $donnees['beneficiaire'])
                ->where('montant', $montant)
                ->whereBetween('date_virement', [
                    Carbon::parse($donnees['date_virement'])->subDays(3),
                    Carbon::parse($donnees['date_virement'])->addDays(3),
                ])
                ->when($ignorerId, fn ($q) => $q->where('id', '!=', $ignorerId))
                ->exists();

            if ($doublon) {
                $alertes[] = "Un virement similaire existe déjà vers ce bénéficiaire (même montant, date proche). Doublon potentiel.";
            }
        }

        return $alertes;
    }

    public static function verifierVersement(array $donnees, int $userId, ?int $ignorerId = null): array
    {
        $alertes = [];

        $montant = (float) ($donnees['montant'] ?? 0);

        if ($montant <= 0) {
            $alertes[] = "Le montant du versement est nul ou négatif.";
        }

        $moyenne = Versement::where('user_id', $userId)
            ->when($ignorerId, fn ($q) => $q->where('id', '!=', $ignorerId))
            ->avg('montant');

        if ($moyenne && $montant > $moyenne * 5) {
            $alertes[] = "Ce montant (" . number_format($montant, 2, ',', ' ') . " DH) est anormalement élevé par rapport à la moyenne habituelle (" . number_format($moyenne, 2, ',', ' ') . " DH).";
        }

        if (!empty($donnees['client']) && !empty($donnees['date_versement'])) {

            $doublon = Versement::where('user_id', $userId)
                ->where('client', $donnees['client'])
                ->where('montant', $montant)
                ->whereBetween('date_versement', [
                    Carbon::parse($donnees['date_versement'])->subDays(3),
                    Carbon::parse($donnees['date_versement'])->addDays(3),
                ])
                ->when($ignorerId, fn ($q) => $q->where('id', '!=', $ignorerId))
                ->exists();

            if ($doublon) {
                $alertes[] = "Un versement similaire existe déjà pour ce client (même montant, date proche). Doublon potentiel.";
            }
        }

        return $alertes;
    }
}