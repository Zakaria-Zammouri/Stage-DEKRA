<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;

class BackupController extends Controller
{
    public function index()
    {
        $dossierBackups = storage_path('app/backups');

        $sauvegardes = collect();

        if (File::exists($dossierBackups)) {

            foreach (File::directories($dossierBackups) as $chemin) {

                $nom = basename($chemin);

                $tailleOctets = 0;
                foreach (File::allFiles($chemin) as $fichier) {
                    $tailleOctets += $fichier->getSize();
                }

                $sauvegardes->push([
                    'nom' => $nom,
                    'taille' => round($tailleOctets / 1024 / 1024, 2),
                    'contient_sql' => File::exists("{$chemin}/database.sql"),
                ]);
            }
        }

        $sauvegardes = $sauvegardes->sortByDesc('nom')->values();

        return view('backups.index', compact('sauvegardes'));
    }
}