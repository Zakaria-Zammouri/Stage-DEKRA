<?php

namespace App\Http\Controllers;

use App\Models\Virement;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use NumberToWords\NumberToWords;
use setasign\Fpdi\Fpdi;
use App\Services\ControleCoherenceService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;


class VirementController extends Controller
{
    /**
     * Liste des virements.
     */
    public function index()
    {
        $virements = Virement::where('user_id', auth()->id())
            ->latest()
            ->get();

        return view('virements.index', compact('virements'));
    }

    /**
     * Formulaire d'ajout.
     */
    public function create()
    {
        return view('virements.create');
    }

    /**
     * Enregistrer un virement.
     */
    public function store(Request $request)
    {
        $request->validate([
            'beneficiaire' => 'required|in:DEKRA,SECURITY DOCUMENT',
            'montant' => 'required|numeric|min:0',
            'motif' => 'nullable|string|max:255',
            'date_virement' => 'required|date',
        ]);

        if (!$request->boolean('confirmer_malgre_alertes')) {

            $alertes = ControleCoherenceService::verifierVirement(
                $request->only(['montant', 'beneficiaire', 'date_virement']),
                auth()->id()
            );

            if (!empty($alertes)) {
                return back()->withInput()->with('alertes_coherence', $alertes);
            }
        }

        $numberToWords = new NumberToWords();

        $transformer = $numberToWords->getNumberTransformer('fr');

        $montantLettres = ucfirst(
            $transformer->toWords((int) $request->montant)
        ) . ' dirhams';

        $ribsBeneficiaires = [
            'DEKRA' => '007780000340400000048033',
            'SECURITY DOCUMENT' => '230780443969122100950096',
        ];

        // On génère le numéro ET on l'insère dans une seule transaction
        // verrouillée. Cela évite les collisions qui se produisaient avec
        // Virement::max('id') + 1 hors transaction :
        //   - lorsque des virements avaient été soft-deleted (SoftDeletes
        //     exclut ces lignes du calcul du max, ce qui pouvait faire
        //     régénérer un numéro déjà utilisé) ;
        //   - lorsque deux requêtes arrivaient en même temps (double clic,
        //     double soumission) et lisaient le même "dernier id" avant
        //     qu'aucune des deux insertions ne soit validée.
        // lockForUpdate() force les requêtes concurrentes à attendre leur
        // tour au lieu de lire la même valeur en parallèle.
        $virement = DB::transaction(function () use ($request, $ribsBeneficiaires, $montantLettres) {
            $dernierId = Virement::withTrashed()->lockForUpdate()->max('id') ?? 0;

            $numero = 'OV-' . date('Y') . '-' . str_pad(
                $dernierId + 1,
                5,
                '0',
                STR_PAD_LEFT
            );

            return Virement::create([
                'user_id'           => auth()->id(),
                'beneficiaire'      => $request->beneficiaire,
                'rib_beneficiaire'  => $ribsBeneficiaires[$request->beneficiaire] ?? '',
                'motif'             => $request->motif,
                'statut'            => 'En attente',
                'montant'           => $request->montant,
                'montant_lettres'   => $montantLettres,
                'numero'            => $numero,
                'date_virement'     => $request->date_virement,
            ]);
        });

        return redirect()
            ->route('virements.index')
            ->with('success', 'Virement ajouté avec succès.');
    }

    /**
     * Afficher un virement.
     */
    public function show(Virement $virement)
    {
        abort_if($virement->user_id != auth()->id(), 403);

        return view('virements.show', compact('virement'));
    }

    /**
     * Formulaire de modification.
     */
    public function edit(Virement $virement)
    {
        abort(404);
    }

    /**
     * Mise à jour.
     */
    public function update(Request $request, Virement $virement)
    {
        abort(404);
    }

    /**
     * Marquer un virement comme traité.
     */
    public function marquerTraite(Virement $virement)
    {
        abort_if($virement->user_id != auth()->id(), 403);

        $virement->update(['statut' => 'Traité']);

        return redirect()
            ->route('virements.index')
            ->with('success', 'Virement marqué comme traité.');
    }

    /**
     * Supprimer un virement.
     */
    public function destroy(Virement $virement)
    {
        abort_if($virement->user_id != auth()->id(), 403);

        $virement->delete();

        return redirect()
            ->route('virements.index')
            ->with('success', 'Virement supprimé avec succès.');
    }

    public function pdf(Virement $virement)
    {
        $pdf = new Fpdi();

        $templates = [
            'DEKRA' => 'ordre_de_virement_DEKRA.pdf',
            'SECURITY DOCUMENT' => 'ordre_de_virement_SECURITY-DOCUMENT.pdf',
        ];

        $nomTemplate = $templates[$virement->beneficiaire] ?? 'ordre_de_virement_DEKRA.pdf';

        $template = storage_path("app/templates/{$nomTemplate}");

        $pageCount = $pdf->setSourceFile($template);

        $tpl = $pdf->importPage(1);

        $size = $pdf->getTemplateSize($tpl);

        $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);

        $pdf->useTemplate($tpl);

        $pdf->SetFont('Helvetica', 'B', 11);
        $pdf->SetXY(165, 13);
        $pdf->Write(5, $virement->numero);

        $pdf->SetFont('Helvetica', '', 10);
        $pdf->SetXY(151, 232);
        $pdf->Write(
            5,
            \Carbon\Carbon::parse($virement->date_virement)->format('d/m/Y')
        );

        $pdf->SetFont('Helvetica', '', 11);
        $pdf->SetXY(145, 95);
        $pdf->Write(
            5,
            number_format($virement->montant, 2, ',', ' ') . ' DH'
        );

        $pdf->SetXY(60, 105);
        $pdf->MultiCell(
            130,
            5,
            utf8_decode($virement->montant_lettres)
        );

        $pdf->SetXY(48, 182);
        $pdf->Write(5, utf8_decode($virement->motif));

        return response($pdf->Output('S'))
            ->header('Content-Type', 'application/pdf')
            ->header(
                'Content-Disposition',
                'inline; filename="ordre-virement-' . $virement->numero . '.pdf"'
            );
    }
}