<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\TvaRate;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TvaController extends Controller
{
    /**
     * Tableau de suivi de la TVA : taux configurables + récapitulatif par période.
     */
    public function index(Request $request)
    {
        $mois = $request->filled('mois')
            ? Carbon::parse($request->mois . '-01')
            : Carbon::now();

        $rates = TvaRate::orderBy('type_vehicule')->get();
        $recap = $this->buildRecap($mois);

        return view('tva.index', compact('rates', 'recap', 'mois'));
    }

    /**
     * Mettre à jour les taux de TVA configurables.
     */
    public function updateRates(Request $request)
    {
        $request->validate([
            'taux' => 'required|array',
            'taux.*' => 'required|numeric|min:0|max:100',
        ]);

        foreach ($request->taux as $id => $valeur) {
            TvaRate::where('id', $id)->update(['taux' => $valeur]);
        }

        return redirect()
            ->route('tva.index')
            ->with('success', 'Taux de TVA mis à jour avec succès.');
    }

    /**
     * Export PDF de l'état récapitulatif de TVA du mois sélectionné.
     */
    public function pdf(Request $request)
    {
        $mois = $request->filled('mois')
            ? Carbon::parse($request->mois . '-01')
            : Carbon::now();

        $recap = $this->buildRecap($mois);

        $pdf = Pdf::loadView('tva.pdf', compact('recap', 'mois'));

        return $pdf->download('Etat-TVA-' . $mois->format('Y-m') . '.pdf');
    }

    /**
     * Construit le récapitulatif de TVA par type de véhicule pour un mois donné.
     */
    private function buildRecap(Carbon $mois): array
    {
        $types = ['Voiture', 'Moto', 'Poids lourd'];

        $lignes = [];
        $totalHt = 0;
        $totalTva = 0;
        $totalTtc = 0;

        foreach ($types as $type) {
            $factures = Invoice::where('user_id', auth()->id())
                ->where('type_vehicule', $type)
                ->whereYear('date_facture', $mois->year)
                ->whereMonth('date_facture', $mois->month)
                ->get();

            $ht = $factures->sum('montant_ht');
            $tva = $factures->sum('tva');
            $ttc = $factures->sum('montant_ttc');

            $lignes[] = [
                'type' => $type,
                'nombre' => $factures->count(),
                'ht' => $ht,
                'tva' => $tva,
                'ttc' => $ttc,
            ];

            $totalHt += $ht;
            $totalTva += $tva;
            $totalTtc += $ttc;
        }

        return [
            'lignes' => $lignes,
            'total_ht' => $totalHt,
            'total_tva' => $totalTva,
            'total_ttc' => $totalTtc,
        ];
    }
}