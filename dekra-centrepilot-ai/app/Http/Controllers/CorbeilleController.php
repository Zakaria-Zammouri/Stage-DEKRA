<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Virement;
use App\Models\Versement;
use App\Models\Document;
use App\Models\Rappel;

class CorbeilleController extends Controller
{
    /**
     * Liste des éléments supprimés (corbeille).
     */
    public function index()
    {
        $invoices = Invoice::onlyTrashed()
            ->where('user_id', auth()->id())
            ->latest('deleted_at')
            ->get();

        $virements = Virement::onlyTrashed()
            ->where('user_id', auth()->id())
            ->latest('deleted_at')
            ->get();

        $versements = Versement::onlyTrashed()
            ->where('user_id', auth()->id())
            ->latest('deleted_at')
            ->get();

        $documents = Document::onlyTrashed()
            ->where('user_id', auth()->id())
            ->latest('deleted_at')
            ->get();

        $rappels = Rappel::onlyTrashed()
            ->where('user_id', auth()->id())
            ->latest('deleted_at')
            ->get();

        return view('corbeille.index', compact(
            'invoices', 'virements', 'versements', 'documents', 'rappels'
        ));
    }

    /**
     * Restaurer un élément supprimé.
     */
    public function restore(string $type, int $id)
    {
        $modele = $this->resoudreModele($type);

        $item = $modele::onlyTrashed()
            ->where('user_id', auth()->id())
            ->findOrFail($id);

        $item->restore();

        return back()->with('success', 'Élément restauré avec succès.');
    }

    /**
     * Supprimer définitivement un élément (irréversible).
     */
    public function supprimerDefinitivement(string $type, int $id)
    {
        $modele = $this->resoudreModele($type);

        $item = $modele::onlyTrashed()
            ->where('user_id', auth()->id())
            ->findOrFail($id);

        $item->forceDelete();

        return back()->with('success', 'Élément supprimé définitivement.');
    }

    /**
     * Associe un identifiant texte au bon modèle Eloquent.
     */
    private function resoudreModele(string $type): string
    {
        return match ($type) {
            'facture' => Invoice::class,
            'virement' => Virement::class,
            'versement' => Versement::class,
            'document' => Document::class,
            'rappel' => Rappel::class,
            default => abort(404),
        };
    }
}