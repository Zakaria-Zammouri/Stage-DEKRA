<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    /**
     * Liste des documents.
     */
    public function index(Request $request)
    {
        $query = Document::where('user_id', auth()->id());

        if ($request->filled('recherche')) {

            $mot = trim($request->recherche);

            $query->where(function ($q) use ($mot) {

                $q->where('titre', 'like', "%{$mot}%")
                    ->orWhere('type', 'like', "%{$mot}%")
                    ->orWhere('categorie', 'like', "%{$mot}%")
                    ->orWhere('nom_proprietaire', 'like', "%{$mot}%")
                    ->orWhere('immatriculation', 'like', "%{$mot}%")
                    ->orWhere('marque', 'like', "%{$mot}%")
                    ->orWhere('modele', 'like', "%{$mot}%")
                    ->orWhere('texte_ocr', 'like', "%{$mot}%");

            });
        }

        $documents = $query->orderBy('created_at', 'desc')->get();

        return view('documents.index', compact('documents'));
    }

    /**
     * Formulaire d'ajout.
     */
    public function create()
    {
        return view('documents.create');
    }

    /**
     * Enregistrer un document.
     */
    public function store(Request $request)
    {
        $request->validate([
            'titre' => 'nullable|string|max:255',
            'type' => 'required|string|max:255',
            'categorie' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'fichier' => 'required|file|max:5120'
        ]);

        $path = $request->file('fichier')->store('documents', 'public');

        $texteOCR = '';
        $nom = '';
        $immatriculation = '';
        $marque = '';
        $modele = '';

        // OCR déclenché pour TOUS les types de documents importés
        try {

            $response = Http::timeout(120)
                ->attach(
                    'file',
                    file_get_contents($request->file('fichier')->getRealPath()),
                    $request->file('fichier')->getClientOriginalName()
                )
                ->post('http://127.0.0.1:8001/ocr');

            if ($response->successful()) {

                $ocr = $response->json();

                $texteOCR = $ocr['texte'] ?? '';
                $nom = $ocr['nom_proprietaire'] ?? '';
                $immatriculation = $ocr['numero_immatriculation'] ?? '';
                $marque = $ocr['marque'] ?? '';
                $modele = $ocr['modele'] ?? '';
            }

        } catch (\Exception $e) {

            $texteOCR = "Erreur OCR : ".$e->getMessage();

        }

        $type = $request->type;
        $categorie = $request->categorie;
        $titre = $request->titre;

        // Classement automatique par l'IA (si demandé)
        if ($type === 'Auto' && $texteOCR && !str_starts_with($texteOCR, 'Erreur OCR')) {

            try {

                set_time_limit(150);

                $classification = Http::timeout(120)
                    ->post('http://127.0.0.1:8001/classifier-document', [
                        'texte' => $texteOCR,
                    ]);

                if ($classification->successful()) {

                    $data = $classification->json();

                    $type = $data['type'] ?? 'Autre';

                    if (empty($categorie) && !empty($data['categorie'])) {
                        $categorie = $data['categorie'];
                    }

                    if (empty($titre) && !empty($data['titre'])) {
                        $titre = $data['titre'];
                    }

                } else {
                    $type = 'Autre';
                }

            } catch (\Throwable $e) {
                $type = 'Autre';
            }

        } elseif ($type === 'Auto') {
            $type = 'Autre';
        }

        // Les champs véhicule ne sont pertinents que pour les Titres de propriété
        if ($type !== 'Titre de propriété') {
            $nom = '';
            $immatriculation = '';
            $marque = '';
            $modele = '';
        }
$document = Document::create([
            'user_id' => auth()->id(),
            'titre' => $titre ?: 'Document sans titre',
            'type' => $type,
            'categorie' => $categorie ?: 'Non classé',
            'chemin_fichier' => $path,
            'description' => $request->description ?? '',
            'texte_ocr' => $texteOCR,
            'nom_proprietaire' => $nom,
            'immatriculation' => $immatriculation,
            'marque' => $marque,
            'modele' => $modele,
            'date_ajout' => now(),
        ]);
        $message = "Document ajouté et analysé par l'IA avec succès.";

        if ($request->type === 'Auto') {
            $message .= " Type détecté automatiquement : {$type}.";
        }

        return redirect()
            ->route('documents.show', $document)
            ->with('success', $message);
    }

    /**
     * Afficher un document.
     */
    public function show(Document $document)
    {
        abort_if($document->user_id !== auth()->id(), 403);

        return view('documents.show', compact('document'));
    }

    /**
     * Formulaire de modification.
     */
    public function edit(Document $document)
    {
        abort_if($document->user_id !== auth()->id(), 403);

        return view('documents.edit', compact('document'));
    }

    /**
     * Mise à jour.
     */
    public function update(Request $request, Document $document)
    {
        abort_if($document->user_id !== auth()->id(), 403);

        $request->validate([
            'titre' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'categorie' => 'required|string|max:255',
            'description' => 'nullable|string',
            'fichier' => 'nullable|file|max:5120'
        ]);

        $data = [
            'titre' => $request->titre,
            'type' => $request->type,
            'categorie' => $request->categorie,
            'description' => $request->description,
        ];

        if ($request->hasFile('fichier')) {

            if (Storage::disk('public')->exists($document->chemin_fichier)) {
                Storage::disk('public')->delete($document->chemin_fichier);
            }

            $data['chemin_fichier'] =
                $request->file('fichier')->store('documents', 'public');

            try {

                $response = Http::timeout(120)
                    ->attach(
                        'file',
                        file_get_contents($request->file('fichier')->getRealPath()),
                        $request->file('fichier')->getClientOriginalName()
                    )
                    ->post('http://127.0.0.1:8001/ocr');

                if ($response->successful()) {

                    $ocr = $response->json();

                    $data['texte_ocr'] = $ocr['texte'] ?? '';

                    if ($request->type === "Titre de propriété") {
                        $data['nom_proprietaire'] = $ocr['nom_proprietaire'] ?? '';
                        $data['immatriculation'] = $ocr['numero_immatriculation'] ?? '';
                        $data['marque'] = $ocr['marque'] ?? '';
                        $data['modele'] = $ocr['modele'] ?? '';
                    } else {
                        $data['nom_proprietaire'] = null;
                        $data['immatriculation'] = null;
                        $data['marque'] = null;
                        $data['modele'] = null;
                    }

                }

            } catch (\Exception $e) {

                $data['texte_ocr'] = "Erreur OCR : ".$e->getMessage();

            }

        }

        $document->update($data);

        return redirect()
            ->route('documents.index')
            ->with('success', 'Document modifié avec succès.');
    }

    /**
     * Supprimer un document.
     */
    public function destroy(Document $document)
    {
        abort_if($document->user_id !== auth()->id(), 403);

        if (Storage::disk('public')->exists($document->chemin_fichier)) {
            Storage::disk('public')->delete($document->chemin_fichier);
        }

        $document->delete();

        return redirect()
            ->route('documents.index')
            ->with('success', 'Document supprimé avec succès.');
    }
}