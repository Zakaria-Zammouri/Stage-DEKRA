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
     * Tableau de suivi de la TVA.
     */
    public function index(Request $request)
    {
        $mois = $request->filled('mois')
            ? Carbon::parse($request->mois . '-01')
            : Carbon::now();

        // On récupère uniquement le taux Moto
        $rates = TvaRate::where('type_vehicule', 'Moto')->get();

        $recap = $this->buildRecap($mois);

        return view('tva.index', compact('rates', 'recap', 'mois'));
    }

    /**
     * Mise à jour du taux TVA Moto.
     */
    public function updateRates(Request $request)
    {
        $request->validate([
            'taux' => 'required|array',
            'taux.*' => 'required|numeric|min:0|max:100',
        ]);

        foreach ($request->taux as $id => $valeur) {
            TvaRate::where('id', $id)
                ->where('type_vehicule', 'Moto')
                ->update([
                    'taux' => $valeur,
                ]);
        }

        return redirect()
            ->route('tva.index')
            ->with('success', 'Taux de TVA Moto mis à jour avec succès.');
    }

    /**
     * Export PDF.
     */
    public function pdf(Request $request)
    {
        $mois = $request->filled('mois')
            ? Carbon::parse($request->mois . '-01')
            : Carbon::now();

        $recap = $this->buildRecap($mois);

        $pdf = Pdf::loadView('tva.pdf', compact('recap', 'mois'));

        return $pdf->download(
            'Etat-TVA-' . $mois->format('Y-m') . '.pdf'
        );
    }

    /**
     * Calcul du récapitulatif TVA Moto.
     */
    private function buildRecap(Carbon $mois): array
    {
        $factures = Invoice::where('user_id', auth()->id())
            ->where('type_vehicule', 'Moto')
            ->whereYear('date_facture', $mois->year)
            ->whereMonth('date_facture', $mois->month)
            ->get();

        $ht = $factures->sum('montant_ht');
        $tva = $factures->sum('tva');
        $ttc = $factures->sum('montant_ttc');

        return [
            'lignes' => [
                [
                    'type' => 'Moto',
                    'nombre' => $factures->count(),
                    'ht' => $ht,
                    'tva' => $tva,
                    'ttc' => $ttc,
                ]
            ],

            'total_ht' => $ht,
            'total_tva' => $tva,
            'total_ttc' => $ttc,
        ];
    }
}