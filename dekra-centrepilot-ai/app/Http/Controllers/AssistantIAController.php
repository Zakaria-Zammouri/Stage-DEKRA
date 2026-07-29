<?php

namespace App\Http\Controllers;

use App\Models\AssistantIA;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AssistantIAController extends Controller
{
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
     * Envoyer une question à l'IA
     */
  public function ask(Request $request)
    {
        set_time_limit(150);

        $request->validate([
            'question' => 'required|string|max:1000',
        ]);

        $question = $request->question;

        $reponse = "Impossible de contacter l'assistant IA.";

        try {

            $response = Http::timeout(120)->post(
                'http://127.0.0.1:8001/assistant-ia',
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
}