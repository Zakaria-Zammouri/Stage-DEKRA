<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;

class GenericMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $sujet;
    public string $corps;
    public ?string $cheminPieceJointe;
    public ?string $contenuPieceJointe;
    public ?string $nomPieceJointe;

    public function __construct(
        string $sujet,
        string $corps,
        ?string $cheminPieceJointe = null,
        ?string $contenuPieceJointe = null,
        ?string $nomPieceJointe = null
    ) {
        $this->sujet = $sujet;
        $this->corps = $corps;
        $this->cheminPieceJointe = $cheminPieceJointe;
        $this->contenuPieceJointe = $contenuPieceJointe;
        $this->nomPieceJointe = $nomPieceJointe;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->sujet,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.generic',
        );
    }

    public function attachments(): array
    {
        // Facture ou virement : PDF généré à la volée, en mémoire
        if ($this->contenuPieceJointe) {
            return [
                Attachment::fromData(
                    fn () => $this->contenuPieceJointe,
                    $this->nomPieceJointe ?? 'piece-jointe.pdf'
                ),
            ];
        }

        // Document : fichier déjà stocké sur le disque
        if ($this->cheminPieceJointe && file_exists(storage_path('app/public/' . $this->cheminPieceJointe))) {
            return [
                Attachment::fromPath(storage_path('app/public/' . $this->cheminPieceJointe)),
            ];
        }

        return [];
    }
}