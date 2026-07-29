<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Loggable;

class Document extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Loggable;

    protected $fillable = [
        'user_id',
        'invoice_id',
        'titre',
        'type',
        'categorie',
        'chemin_fichier',
        'description',
        'texte_ocr',
        'nom_proprietaire',
        'immatriculation',
        'marque',
        'modele',
        'date_ajout'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}