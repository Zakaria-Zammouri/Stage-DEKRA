<?php

namespace App\Http\Controllers;

use App\Models\Rappel;
use Illuminate\Http\Request;
use Carbon\Carbon;

class RappelController extends Controller
{
    /**
     * Liste des rappels
     */
   public function index(Request $request)
{
    $query = Rappel::where('user_id', auth()->id());

    // Recherche
    if ($request->filled('search')) {

        $query->where(function ($q) use ($request) {

            $q->where('titre', 'like', '%' . $request->search . '%')
              ->orWhere('description', 'like', '%' . $request->search . '%');

        });

    }

    // Filtre par statut
    if ($request->filled('statut')) {

    switch ($request->statut) {

        case "À venir":
            $query->where('termine', false)
                  ->whereDate('date_rappel', '>', Carbon::today());
            break;

        case "Aujourd'hui":
            $query->where('termine', false)
                  ->whereDate('date_rappel', Carbon::today());
            break;

        case "En retard":
            $query->where('termine', false)
                  ->whereDate('date_rappel', '<', Carbon::today());
            break;

        case "Terminé":
            $query->where('termine', true);
            break;
    }

}

    // Filtre par date
    if ($request->filled('date')) {

        $query->whereDate('date_rappel', $request->date);

    }

    $rappels = $query
        ->orderBy('date_rappel', 'asc')
        ->get();

    return view('rappels.index', compact('rappels'));
}

    /**
     * Formulaire d'ajout
     */
    public function create()
    {
        return view('rappels.create');
    }

    /**
     * Enregistrer un rappel
     */
    public function store(Request $request)
    {
        $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date_rappel' => 'required|date',
        ]);

        Rappel::create([
            'user_id' => auth()->id(),
            'titre' => $request->titre,
            'description' => $request->description,
            'date_rappel' => $request->date_rappel,
        ]);

        return redirect()
            ->route('rappels.index')
            ->with('success', 'Rappel ajouté avec succès.');
    }

    /**
     * Afficher un rappel
     */
    public function show(Rappel $rappel)
    {
        abort_if($rappel->user_id != auth()->id(), 403);

        return view('rappels.show', compact('rappel'));
    }

    /**
     * Formulaire de modification
     */
    public function edit(Rappel $rappel)
    {
        abort_if($rappel->user_id != auth()->id(), 403);

        return view('rappels.edit', compact('rappel'));
    }

    /**
     * Mettre à jour un rappel
     */
    public function update(Request $request, Rappel $rappel)
    {
        abort_if($rappel->user_id != auth()->id(), 403);

        $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date_rappel' => 'required|date',
        ]);

        $rappel->update([
            'titre' => $request->titre,
            'description' => $request->description,
            'date_rappel' => $request->date_rappel,
        ]);

        return redirect()
            ->route('rappels.index')
            ->with('success', 'Rappel modifié avec succès.');
    }

    /**
     * Supprimer un rappel
     */
    public function destroy(Rappel $rappel)
    {
        abort_if($rappel->user_id != auth()->id(), 403);

        $rappel->delete();

        return redirect()
            ->route('rappels.index')
            ->with('success', 'Rappel supprimé avec succès.');
    }
    public function terminer(Rappel $rappel)
{
    abort_if($rappel->user_id != auth()->id(), 403);

    $rappel->update([
        'termine' => true,
    ]);

    return redirect()
        ->route('rappels.index')
        ->with('success', 'Le rappel a été marqué comme terminé.');
}
}