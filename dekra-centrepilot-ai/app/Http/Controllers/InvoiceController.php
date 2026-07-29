<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use App\Models\TvaRate;
use App\Services\ControleCoherenceService;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    /**
     * Liste des factures avec recherche et filtre.
     */
    public function index(Request $request)
    {
        $query = Invoice::where('user_id', auth()->id());

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('numero', 'like', '%' . $request->search . '%')
                    ->orWhere('client', 'like', '%' . $request->search . '%')
                    ->orWhere('objet', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        $invoices = $query->latest()->get();

        return view('invoices.index', compact('invoices'));
    }

    /**
     * Formulaire d'ajout.
     */
    public function create()
    {
        $rates = TvaRate::pluck('taux', 'type_vehicule');

        return view('invoices.create', compact('rates'));
    }

    /**
     * Enregistrer une facture.
     */
    public function store(Request $request)
    {
        $request->validate([
            'client'        => 'required|string|max:255',
            'objet'         => 'required|string|max:255',
            'type_vehicule' => 'required|in:Voiture,Moto,Poids lourd',
            'montant_ht'    => 'required|numeric|min:0',
            'tva'           => 'required|numeric|min:0',
            'date_facture'  => 'required|date',
            'statut'        => 'required|in:Payée,Non payée',
        ]);

        if (!$request->boolean('confirmer_malgre_alertes')) {

            $alertes = ControleCoherenceService::verifierFacture(
                $request->only(['montant_ht', 'tva', 'client', 'date_facture']),
                auth()->id()
            );

            if (!empty($alertes)) {
                return back()->withInput()->with('alertes_coherence', $alertes);
            }
        }

        $montantTTC = $request->montant_ht + $request->tva;

        // Le numéro contient la date du jour (FAC-YYYYMMDD-XXXX), donc le
        // compteur doit repartir à 1 chaque jour : on compte uniquement les
        // factures créées aujourd'hui, pas le total depuis le début.
        //
        // On utilise withTrashed() pour inclure les factures soft-deleted
        // dans le calcul : sans ça, supprimer une facture du jour libère
        // artificiellement un numéro déjà attribué et le fait régénérer,
        // ce qui provoquait l'erreur de contrainte unique.
        //
        // La génération + l'insertion sont faites dans une transaction
        // verrouillée (lockForUpdate) pour empêcher deux requêtes
        // concurrentes (double clic, double soumission) de lire le même
        // compte avant qu'aucune des deux insertions ne soit validée.
        $invoice = DB::transaction(function () use ($request, $montantTTC) {
            $aujourdHui = now()->format('Ymd');

            $nombreFacturesDuJour = Invoice::withTrashed()
                ->whereRaw("DATE_FORMAT(created_at, '%Y%m%d') = ?", [$aujourdHui])
                ->lockForUpdate()
                ->count();

            $numero = 'FAC-' . $aujourdHui . '-' . str_pad(
                $nombreFacturesDuJour + 1,
                4,
                '0',
                STR_PAD_LEFT
            );

            return Invoice::create([
                'user_id'       => auth()->id(),
                'numero'        => $numero,
                'client'        => $request->client,
                'objet'         => $request->objet,
                'type_vehicule' => $request->type_vehicule,
                'montant_ht'    => $request->montant_ht,
                'tva'           => $request->tva,
                'montant_ttc'   => $montantTTC,
                'date_facture'  => $request->date_facture,
                'statut'        => $request->statut,
            ]);
        });

        return redirect()
            ->route('invoices.index')
            ->with('success', 'Facture créée avec succès.');
    }

    /**
     * Afficher une facture.
     */
    public function show(Invoice $invoice)
    {
        abort_if($invoice->user_id !== auth()->id(), 403);

        return view('invoices.show', compact('invoice'));
    }

    /**
     * Formulaire de modification.
     */
    public function edit(Invoice $invoice)
    {
        abort_if($invoice->user_id !== auth()->id(), 403);

        $rates = TvaRate::pluck('taux', 'type_vehicule');

        return view('invoices.edit', compact('invoice', 'rates'));
    }

    /**
     * Mettre à jour une facture.
     */
    public function update(Request $request, Invoice $invoice)
    {
        abort_if($invoice->user_id !== auth()->id(), 403);

        $request->validate([
            'client'        => 'required|string|max:255',
            'objet'         => 'required|string|max:255',
            'type_vehicule' => 'required|in:Voiture,Moto,Poids lourd',
            'montant_ht'    => 'required|numeric|min:0',
            'tva'           => 'required|numeric|min:0',
            'date_facture'  => 'required|date',
            'statut'        => 'required|in:Payée,Non payée',
        ]);

        if (!$request->boolean('confirmer_malgre_alertes')) {

            $alertes = ControleCoherenceService::verifierFacture(
                $request->only(['montant_ht', 'tva', 'client', 'date_facture']),
                auth()->id(),
                $invoice->id
            );

            if (!empty($alertes)) {
                return back()->withInput()->with('alertes_coherence', $alertes);
            }
        }

        $invoice->update([
            'client'        => $request->client,
            'objet'         => $request->objet,
            'type_vehicule' => $request->type_vehicule,
            'montant_ht'    => $request->montant_ht,
            'tva'           => $request->tva,
            'montant_ttc'   => $request->montant_ht + $request->tva,
            'date_facture'  => $request->date_facture,
            'statut'        => $request->statut,
        ]);

        return redirect()
            ->route('invoices.index')
            ->with('success', 'Facture modifiée avec succès.');
    }

    /**
     * Supprimer une facture.
     */
    public function destroy(Invoice $invoice)
    {
        abort_if($invoice->user_id !== auth()->id(), 403);

        $invoice->delete();

        return redirect()
            ->route('invoices.index')
            ->with('success', 'Facture supprimée avec succès.');
    }

    /**
     * Générer le PDF.
     */
    public function pdf(Invoice $invoice)
    {
        abort_if($invoice->user_id !== auth()->id(), 403);

        $pdf = Pdf::loadView('invoices.pdf', [
            'invoice' => $invoice
        ]);

        return $pdf->download('Facture-' . $invoice->numero . '.pdf');
    }
}