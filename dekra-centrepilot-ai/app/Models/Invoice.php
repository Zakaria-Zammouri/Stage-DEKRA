<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Loggable;

class Invoice extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Loggable;

    protected $fillable = [
        'user_id',
        'numero',
        'client',
        'objet',
        'type_vehicule',
        'montant_ht',
        'tva',
        'montant_ttc',
        'date_facture',
        'statut',
    ];

    protected $casts = [
        'date_facture' => 'date',
        'montant_ht' => 'decimal:2',
        'tva' => 'decimal:2',
        'montant_ttc' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function document()
    {
        return $this->hasOne(Document::class);
    }
}