<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Versement;
use App\Services\ControleCoherenceService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class VersementController extends Controller
{
    /**
     * Journal des versements + rapprochement avec les factures.
     */
    public function index(Request $request)
    {
        $mois = $request->filled('mois')
            ? Carbon::parse($request->mois . '-01')
            : Carbon::now();

        $query = Versement::where('user_id', auth()->id());

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('reference', 'like', '%' . $request->search . '%')
                    ->orWhere('client', 'like', '%' . $request->search . '%')
                    ->orWhere('notes', 'like', '%' . $request->search . '%');
            });
        }

        $versements = $query
            ->whereYear('date_versement', $mois->year)
            ->whereMonth('date_versement', $mois->month)
            ->orderBy('date_versement', 'desc')
            ->get();

        $totalFacture = Invoice::where('user_id', auth()->id())
            ->whereYear('date_facture', $mois->year)
            ->whereMonth('date_facture', $mois->month)
            ->sum('montant_ttc');

        $totalVerse = $versements->sum('montant');
        $ecart = $totalFacture - $totalVerse;

        return view('versements.index', compact(
            'versements',
            'mois',
            'totalFacture',
            'totalVerse',
            'ecart'
        ));
    }

    /**
     * Formulaire d'ajout.
     */
    public function create()
    {
        return view('versements.create');
    }

    /**
     * Enregistrer un versement.
     */
    public function store(Request $request)
    {
        $request->validate([
            'reference' => 'nullable|string|max:255',
            'client' => 'nullable|string|max:255',
            'montant' => 'required|numeric|min:0',
            'date_versement' => 'required|date',
            'mode_reglement' => 'required|in:Espèces,Chèque,Virement,Carte bancaire',
            'notes' => 'nullable|string',
        ]);

        if (!$request->boolean('confirmer_malgre_alertes')) {

            $alertes = ControleCoherenceService::verifierVersement(
                $request->only(['montant', 'client', 'date_versement']),
                auth()->id()
            );

            if (!empty($alertes)) {
                return back()->withInput()->with('alertes_coherence', $alertes);
            }
        }

        Versement::create([
            'user_id' => auth()->id(),
            'reference' => $request->reference,
            'client' => $request->client,
            'montant' => $request->montant,
            'date_versement' => $request->date_versement,
            'mode_reglement' => $request->mode_reglement,
            'notes' => $request->notes,
        ]);

        return redirect()
            ->route('versements.index')
            ->with('success', 'Versement enregistré avec succès.');
    }

    /**
     * Afficher un versement.
     */
    public function show(Versement $versement)
    {
        abort_if($versement->user_id !== auth()->id(), 403);

        return view('versements.show', compact('versement'));
    }

    /**
     * Formulaire de modification.
     */
    public function edit(Versement $versement)
    {
        abort_if($versement->user_id !== auth()->id(), 403);

        return view('versements.edit', compact('versement'));
    }

    /**
     * Mise à jour.
     */
    public function update(Request $request, Versement $versement)
    {
        abort_if($versement->user_id !== auth()->id(), 403);

        $request->validate([
            'reference' => 'nullable|string|max:255',
            'client' => 'nullable|string|max:255',
            'montant' => 'required|numeric|min:0',
            'date_versement' => 'required|date',
            'mode_reglement' => 'required|in:Espèces,Chèque,Virement,Carte bancaire',
            'notes' => 'nullable|string',
        ]);

        if (!$request->boolean('confirmer_malgre_alertes')) {

            $alertes = ControleCoherenceService::verifierVersement(
                $request->only(['montant', 'client', 'date_versement']),
                auth()->id(),
                $versement->id
            );

            if (!empty($alertes)) {
                return back()->withInput()->with('alertes_coherence', $alertes);
            }
        }

        $versement->update($request->only([
            'reference', 'client', 'montant', 'date_versement', 'mode_reglement', 'notes',
        ]));

        return redirect()
            ->route('versements.index')
            ->with('success', 'Versement modifié avec succès.');
    }

    /**
     * Supprimer un versement.
     */
    public function destroy(Versement $versement)
    {
        abort_if($versement->user_id !== auth()->id(), 403);

        $versement->delete();

        return redirect()
            ->route('versements.index')
            ->with('success', 'Versement supprimé avec succès.');
    }
}