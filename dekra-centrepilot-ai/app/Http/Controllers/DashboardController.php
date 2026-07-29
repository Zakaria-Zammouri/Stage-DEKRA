<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\User;
use Carbon\Carbon;
use App\Models\Rappel;
use App\Models\Invoice;
use App\Models\Virement;
use App\Models\Email;

class DashboardController extends Controller
{
    public function index()
    {
        // Nombre total de documents
        $documentsCount = Document::count();

        // Nombre de titres de propriété (documents analysés par OCR)
        $ocrCount = Document::whereNotNull('texte_ocr')->count();

        // Documents ajoutés cette semaine
        $recentCount = Document::where(
            'created_at',
            '>=',
            Carbon::now()->startOfWeek()
        )->count();

        // Nombre d'emails envoyés
        $emailsCount = Email::count();

        // Nombre d'utilisateurs
        $usersCount = User::count();

        $invoicesCount = Invoice::count();

        // ------------------------------------------------
        // Indicateurs demandés par le cahier des charges (6.2)
        // ------------------------------------------------

        // Factures du mois en cours
        $facturesMoisCount = Invoice::where('user_id', auth()->id())
            ->whereYear('date_facture', now()->year)
            ->whereMonth('date_facture', now()->month)
            ->count();

        // Montant de TVA à déclarer (sur les factures du mois en cours)
        $tvaADeclarer = Invoice::where('user_id', auth()->id())
            ->whereYear('date_facture', now()->year)
            ->whereMonth('date_facture', now()->month)
            ->sum('tva');

        // Virements en attente
        $virementsEnAttenteCount = Virement::where('user_id', auth()->id())
            ->where('statut', 'En attente')
            ->count();

        // Rappels à venir (nombre, pour la carte statistique)
        $rappelsAVenirCount = Rappel::where('user_id', auth()->id())
            ->whereDate('date_rappel', '>=', now())
            ->count();

        // ------------------------------------------------
        // Graphique d'évolution mensuelle (12 derniers mois)
        // ------------------------------------------------

        $mois = [];
        $totauxFactures = [];

        for ($i = 11; $i >= 0; $i--) {

            $date = now()->subMonths($i);

            $mois[] = $date->translatedFormat('M Y');

            $totauxFactures[] = Invoice::where('user_id', auth()->id())
                ->whereYear('date_facture', $date->year)
                ->whereMonth('date_facture', $date->month)
                ->sum('montant_ttc');
        }

        // Les 5 derniers documents
        $lastDocuments = Document::latest()
            ->take(5)
            ->get();

        $rappels = Rappel::where('user_id', auth()->id())
            ->whereDate('date_rappel', '>=', now())
            ->orderBy('date_rappel')
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'documentsCount',
            'ocrCount',
            'recentCount',
            'emailsCount',
            'usersCount',
            'lastDocuments',
            'rappels',
            'invoicesCount',
            'facturesMoisCount',
            'tvaADeclarer',
            'virementsEnAttenteCount',
            'rappelsAVenirCount',
            'mois',
            'totauxFactures'
        ));
    }
}