<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;

class SauvegardeAutomatique extends Command
{
    protected $signature = 'backup:run';

    protected $description = 'Sauvegarde la base de données et les documents archivés';

    public function handle()
    {
        $horodatage = now()->format('Y-m-d_H-i-s');

        $dossierBackup = storage_path("app/backups/{$horodatage}");

        File::ensureDirectoryExists($dossierBackup);

        // -----------------------------------
        // 1. Dump de la base de données MySQL
        // -----------------------------------

        $fichierSql = "{$dossierBackup}/database.sql";

        $host = config('database.connections.mysql.host');
        $port = config('database.connections.mysql.port');
        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');

       $cheminMysqldump = env('MYSQLDUMP_PATH', 'mysqldump');

        $commande = [
            $cheminMysqldump,
            '--host=' . $host,
            '--port=' . $port,
            '--user=' . $username,
        ];

        if ($password) {
            $commande[] = '--password=' . $password;
        }

        $commande[] = $database;

        $process = new Process($commande);
        $process->setTimeout(300);

        try {
            $process->mustRun();
            File::put($fichierSql, $process->getOutput());
            $this->info("Base de données sauvegardée : {$fichierSql}");
        } catch (\Throwable $e) {
            $this->error('Erreur lors du dump de la base : ' . $e->getMessage());
        }

        // -----------------------------------
        // 2. Copie des documents archivés
        // -----------------------------------

        $sourceDocuments = storage_path('app/public');
        $destinationDocuments = "{$dossierBackup}/documents";

        if (File::exists($sourceDocuments)) {
            File::copyDirectory($sourceDocuments, $destinationDocuments);
            $this->info("Documents copiés vers : {$destinationDocuments}");
        }

        // -----------------------------------
        // 3. Nettoyage : garder seulement les 10 dernières sauvegardes
        // -----------------------------------

        $toutesLesSauvegardes = collect(File::directories(storage_path('app/backups')))
            ->sortByDesc(fn ($chemin) => $chemin);

        $aSupprimer = $toutesLesSauvegardes->slice(10);

        foreach ($aSupprimer as $ancienneSauvegarde) {
            File::deleteDirectory($ancienneSauvegarde);
        }

        $this->info('Sauvegarde terminée avec succès.');

        return self::SUCCESS;
    }
}