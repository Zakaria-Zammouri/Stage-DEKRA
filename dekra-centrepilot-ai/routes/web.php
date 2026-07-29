<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\AssistantIAController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\VirementController;
use App\Http\Controllers\VersementController;
use App\Http\Controllers\RappelController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\TvaController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\CorbeilleController;
use App\Http\Controllers\JournalController;
use App\Http\Controllers\BackupController;



Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Profil
    |--------------------------------------------------------------------------
    */

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    /*
    |--------------------------------------------------------------------------
    | Utilisateurs
    |--------------------------------------------------------------------------
    */



    Route::resource('rappels', RappelController::class);

    Route::resource('invoices', InvoiceController::class);
    Route::get('/invoices/{invoice}/pdf', [InvoiceController::class, 'pdf'])
        ->name('invoices.pdf');



        /*
    |--------------------------------------------------------------------------
    | Mails
    |--------------------------------------------------------------------------
    */

   Route::get('/mails', [EmailController::class, 'index'])->name('mails.index');
    Route::get('/mails/nouveau', [EmailController::class, 'create'])->name('mails.create');
    Route::post('/mails', [EmailController::class, 'store'])->name('mails.store');
    Route::post('/mails/generer-ia', [EmailController::class, 'genererIA'])->name('mails.generer-ia');
    Route::get('/mails/{email}', [EmailController::class, 'show'])->name('mails.show');
    Route::get('/mails/{email}/piece-jointe', [EmailController::class, 'telechargerPieceJointe'])->name('mails.piece-jointe');
    /*
    |--------------------------------------------------------------------------
    | Gestion des documents
    |--------------------------------------------------------------------------
    */

    Route::resource('documents', DocumentController::class);

    Route::get('/documents/{document}/telecharger', [DocumentController::class, 'telecharger'])
    ->name('documents.telecharger');

    /*
    |--------------------------------------------------------------------------
    | Virements bancaires (ordres de virement pré-remplis)
    |--------------------------------------------------------------------------
    */

    Route::resource('virements', VirementController::class);
    Route::get(
        '/virements/{virement}/pdf',
        [VirementController::class, 'pdf']
    )->name('virements.pdf');
    Route::patch(
        '/virements/{virement}/traiter',
        [VirementController::class, 'marquerTraite']
    )->name('virements.marquer-traite');


    /*
    |--------------------------------------------------------------------------
    | TVA
    |--------------------------------------------------------------------------
    */

    Route::get('/tva', [TvaController::class, 'index'])->name('tva.index');
    Route::put('/tva/taux', [TvaController::class, 'updateRates'])->name('tva.rates.update');
    Route::get('/tva/pdf', [TvaController::class, 'pdf'])->name('tva.pdf');


    /*
    |--------------------------------------------------------------------------
    | Corbeille
    |--------------------------------------------------------------------------
    */

    Route::get('/corbeille', [CorbeilleController::class, 'index'])->name('corbeille.index');
    Route::post('/corbeille/{type}/{id}/restaurer', [CorbeilleController::class, 'restore'])->name('corbeille.restore');
    Route::delete('/corbeille/{type}/{id}', [CorbeilleController::class, 'supprimerDefinitivement'])->name('corbeille.force-delete');


    Route::get('/journal', [JournalController::class, 'index'])->name('journal.index');

    Route::get('/sauvegardes', [BackupController::class, 'index'])->name('backups.index');

    /*
    |--------------------------------------------------------------------------
    | Versements (journal des paiements reçus)
    |--------------------------------------------------------------------------
    */

    Route::resource('versements', VersementController::class);

    Route::patch('/rappels/{rappel}/terminer', [RappelController::class, 'terminer'])
        ->name('rappels.terminer')
        ->middleware('auth');

    /*
    |--------------------------------------------------------------------------
    | Assistant IA
    |--------------------------------------------------------------------------
    */

    Route::get('/assistant', [AssistantIAController::class, 'index'])
        ->name('assistant.index');

    Route::post('/assistant', [AssistantIAController::class, 'ask'])
        ->name('assistant.ask');

    Route::post('/assistant/analyser-document', [AssistantIAController::class, 'analyserDocument'])
    ->name('assistant.analyser-document');

});

require __DIR__ . '/auth.php';