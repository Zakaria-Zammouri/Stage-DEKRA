<?php

namespace App\Http\Controllers;

use App\Models\AssistantIA;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AssistantIAController extends Controller
{
    /**
     * URL du service Python (FastAPI + Gemma)
     */
    protected string $fastApiUrl = 'http://127.0.0.1:8001';

    /**
     * Afficher la page Assistant IA
     */
    public function index()
    {
        $historique = AssistantIA::where('user_id', auth()->id())
            ->latest()
            ->get();

        return view('assistant.index', compact('historique'));
    }

    /**
     * Envoyer une question à l'IA (fonctionnalité existante, inchangée)
     */
    public function ask(Request $request)
    {
        set_time_limit(150);

        $request->validate([
            'question' => 'required|string|max:1000',
        ]);

        $question = strtolower(trim($request->question));

        $reponse = "Impossible de contacter l'assistant IA.";

        try {

            $response = Http::timeout(30)->post(
                "{$this->fastApiUrl}/assistant",
                [
                    'question' => $question
                ]
            );

            if ($response->successful()) {

                $json = $response->json();

                $reponse = $json['reponse'] ?? "Aucune réponse.";

            }

        } catch (\Exception $e) {

            $reponse = "Erreur : " . $e->getMessage();

        }

        AssistantIA::create([
            'user_id' => auth()->id(),
            'type' => 'question',
            'question' => $question,
            'reponse' => $reponse,
        ]);

        $historique = AssistantIA::where('user_id', auth()->id())
            ->latest()
            ->get();

        return view(
            'assistant.index',
            compact(
                'reponse',
                'historique'
            )
        );
    }

    /**
     * NOUVELLE FONCTIONNALITÉ : analyser un document (PDF/image)
     * OCR + classification automatique + extraction JSON structurée via Gemma
     */
    public function analyserDocument(Request $request)
    {
        // IMPORTANT : doit être >= au timeout Guzzle ci-dessous (300s)
        // sinon PHP tue le script avant que Guzzle ait fini d'attendre.
        set_time_limit(310);

        $request->validate([
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240', // 10 Mo max
            'type_document' => 'nullable|string|in:Titre de propriété,Facture,Reçu,Virement,Autre',
        ]);

        $fichier = $request->file('file');

        $resultatAnalyse = [
            'type_document' => null,
            'categorie' => null,
            'titre' => null,
            'champs' => [],
            'texte' => '',
            'erreur' => "Impossible de contacter le service d'analyse.",
        ];

        try {

            $response = Http::timeout(300)
                ->connectTimeout(10)
                ->attach(
                    'file',
                    file_get_contents($fichier->getRealPath()),
                    $fichier->getClientOriginalName()
                )
                ->post(
                    "{$this->fastApiUrl}/analyser-document",
                    [
                        'type_document' => $request->input('type_document')
                    ]
                );

            if ($response->successful()) {

                $resultatAnalyse = $response->json();

            } else {

                $resultatAnalyse['erreur'] =
                    "Erreur {$response->status()} : " . $response->body();

            }

        } catch (\Illuminate\Http\Client\ConnectionException $e) {

            $resultatAnalyse['erreur'] =
                "Impossible de contacter le service Python (port 8001). " .
                "Vérifiez qu'uvicorn est bien lancé. Détail : " . $e->getMessage();

        } catch (\Exception $e) {

            $resultatAnalyse['erreur'] = $e->getMessage();

        }

        // Enregistrement dans le même historique que les questions, avec type = 'document'
        AssistantIA::create([
            'user_id' => auth()->id(),
            'type' => 'document',
            'fichier_nom' => $fichier->getClientOriginalName(),
            'type_document' => $resultatAnalyse['type_document'] ?? null,
            'champs_extraits' => $resultatAnalyse['champs'] ?? [],
        ]);

        $historique = AssistantIA::where('user_id', auth()->id())
            ->latest()
            ->get();

        return view(
            'assistant.index',
            compact(
                'resultatAnalyse',
                'historique'
            )
        );
    }
}