<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Email extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'titre_propriete_id',
        'document_id',
        'invoice_id',
        'virement_id',
        'destinataire',
        'objet',
        'type_modele',
        'contenu',
        'statut_envoi',
        'date_generation',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function titrePropriete()
    {
        return $this->belongsTo(TitrePropriete::class);
    }

    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function virement()
    {
        return $this->belongsTo(Virement::class);
    }
}