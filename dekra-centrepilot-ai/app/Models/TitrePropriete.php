<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TitrePropriete extends Model
{
    use HasFactory;

   protected $fillable = [
    'user_id',
    'nom_proprietaire',
    'numero_immatriculation',
    'marque',
    'modele',
    'description',
    'chemin_fichier',
    'date_extraction',
];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function emails()
    {
        return $this->hasMany(Email::class);
    }
}