<?php

namespace App\Http\Controllers;

use App\Mail\GenericMail;
use App\Models\Document;
use App\Models\Email;
use App\Models\Invoice;
use App\Models\Virement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use setasign\Fpdi\Fpdi;

class EmailController extends Controller
{
    /**
     * Historique des mails envoyés.
     */
    public function index(Request $request)
    {
        $query = Email::where('user_id', auth()->id());

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('destinataire', 'like', '%' . $request->search . '%')
                    ->orWhere('objet', 'like', '%' . $request->search . '%');
            });
        }

        $emails = $query->latest('date_generation')->get();

        return view('mails.index', compact('emails'));
    }

    /**
     * Formulaire de composition d'un mail.
     */
    public function create()
    {
        $documents = Document::where('user_id', auth()->id())
            ->latest()
            ->get();

        $invoices = Invoice::where('user_id', auth()->id())
            ->latest()
            ->get();

        $virements = Virement::where('user_id', auth()->id())
            ->latest()
            ->get();

        return view('mails.create', compact('documents', 'invoices', 'virements'));
    }

    /**
     * Générer un mail via l'IA (Ollama/Gemma).
     */
    public function genererIA(Request $request)
    {
        set_time_limit(150);

        $request->validate([
            'type_modele'  => 'required|string',
            'destinataire' => 'nullable|string',
            'contexte'     => 'nullable|string',
        ]);

        try {
            $response = \Illuminate\Support\Facades\Http::timeout(120)->post('http://127.0.0.1:8001/generer-mail', [
                'type_modele'  => $request->type_modele,
                'destinataire' => $request->destinataire,
                'contexte'     => $request->contexte ?? '',
            ]);

            if ($response->successful()) {
                return response()->json($response->json());
            }
        } catch (\Throwable $e) {
            // on retombe sur la réponse d'erreur ci-dessous
        }

        return response()->json([
            'objet' => '',
            'corps' => '',
            'erreur' => "Impossible de contacter l'IA pour le moment.",
        ], 500);
    }

    /**
     * Envoyer et enregistrer le mail.
     */
    public function store(Request $request)
    {
        $request->validate([
            'destinataire'  => 'required|email',
            'objet'         => 'required|string|max:255',
            'type_modele'   => 'required|string|max:255',
            'contenu'       => 'required|string',
            'piece_jointe'  => 'nullable|string',
        ]);

        $documentId = null;
        $invoiceId = null;
        $virementId = null;
        $cheminFichier = null;
        $contenuFichier = null;
        $nomFichier = null;

        if ($request->filled('piece_jointe')) {

            [$type, $id] = array_pad(explode(':', $request->piece_jointe, 2), 2, null);

            if ($type === 'document') {

                $document = Document::where('user_id', auth()->id())->find($id);

                if ($document) {
                    $documentId = $document->id;
                    $cheminFichier = $document->chemin_fichier;
                }

            } elseif ($type === 'facture') {

                $invoice = Invoice::where('user_id', auth()->id())->find($id);

                if ($invoice) {
                    $invoiceId = $invoice->id;
                    $contenuFichier = Pdf::loadView('invoices.pdf', ['invoice' => $invoice])->output();
                    $nomFichier = 'Facture-' . $invoice->numero . '.pdf';
                }

            } elseif ($type === 'virement') {

                $virement = Virement::where('user_id', auth()->id())->find($id);

                if ($virement) {
                    $virementId = $virement->id;
                    $contenuFichier = $this->genererPdfVirement($virement);
                    $nomFichier = 'Virement-' . $virement->numero . '.pdf';
                }
            }
        }

        $statut = 'Envoyé';

        try {
            Mail::to($request->destinataire)->send(
                new GenericMail(
                    $request->objet,
                    $request->contenu,
                    $cheminFichier,
                    $contenuFichier,
                    $nomFichier
                )
            );
        } catch (\Throwable $e) {
            $statut = 'Échec';
            Log::error('Échec envoi mail : ' . $e->getMessage());
        }

        Email::create([
            'user_id'         => auth()->id(),
            'document_id'     => $documentId,
            'invoice_id'      => $invoiceId,
            'virement_id'     => $virementId,
            'destinataire'    => $request->destinataire,
            'objet'           => $request->objet,
            'type_modele'     => $request->type_modele,
            'contenu'         => $request->contenu,
            'statut_envoi'    => $statut,
            'date_generation' => now(),
        ]);

        $message = $statut === 'Envoyé'
            ? 'Mail envoyé avec succès.'
            : "Le mail n'a pas pu être envoyé, mais il a été enregistré dans l'historique.";

        return redirect()
            ->route('mails.index')
            ->with($statut === 'Envoyé' ? 'success' : 'error', $message);
    }

    /**
     * Détail d'un mail envoyé.
     */
    public function show(Email $email)
    {
        abort_if($email->user_id !== auth()->id(), 403);

        return view('mails.show', compact('email'));
    }
    /**
     * Télécharger la pièce jointe associée à un mail envoyé.
     */
    public function telechargerPieceJointe(Email $email)
    {
        abort_if($email->user_id !== auth()->id(), 403);

        if ($email->invoice_id && $email->invoice) {
            $pdf = Pdf::loadView('invoices.pdf', ['invoice' => $email->invoice]);
            return $pdf->download('Facture-' . $email->invoice->numero . '.pdf');
        }

        if ($email->virement_id && $email->virement) {
            $contenu = $this->genererPdfVirement($email->virement);
            return response($contenu)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="Virement-' . $email->virement->numero . '.pdf"');
        }

        if ($email->document_id && $email->document) {

            $chemin = storage_path('app/public/' . $email->document->chemin_fichier);

            if (file_exists($chemin)) {
                return response()->download($chemin, $email->document->titre);
            }
        }

        abort(404, "Aucune pièce jointe disponible pour ce mail.");
    }

    /**
     * Génère le PDF d'un virement en mémoire (même logique que VirementController@pdf).
     */
    private function genererPdfVirement(Virement $virement): string
    {
        $pdf = new Fpdi();

        $templates = [
            'DEKRA' => 'ordre_de_virement_DEKRA.pdf',
            'SECURITY DOCUMENT' => 'ordre_de_virement_SECURITY-DOCUMENT.pdf',
        ];

        $nomTemplate = $templates[$virement->beneficiaire] ?? 'ordre_de_virement_DEKRA.pdf';

        $template = storage_path("app/templates/{$nomTemplate}");

        $pdf->setSourceFile($template);
        $tpl = $pdf->importPage(1);
        $size = $pdf->getTemplateSize($tpl);
        $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
        $pdf->useTemplate($tpl);

        $pdf->SetFont('Helvetica', 'B', 11);
        $pdf->SetXY(165, 13);
        $pdf->Write(5, $virement->numero);

        $pdf->SetFont('Helvetica', '', 10);
        $pdf->SetXY(151, 232);
        $pdf->Write(5, \Carbon\Carbon::parse($virement->date_virement)->format('d/m/Y'));

        $pdf->SetFont('Helvetica', '', 11);
        $pdf->SetXY(145, 95);
        $pdf->Write(5, number_format($virement->montant, 2, ',', ' ') . ' DH');

        $pdf->SetXY(60, 105);
        $pdf->MultiCell(130, 5, utf8_decode($virement->montant_lettres));

        $pdf->SetXY(48, 182);
        $pdf->Write(5, utf8_decode($virement->motif));

        return $pdf->Output('S');
    }
}