<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Loggable;

class Virement extends Model
{
    use SoftDeletes;
    use Loggable;

    protected $table = 'virements';

    protected $fillable = [
        'beneficiaire',
        'rib_beneficiaire',
        'motif',
        'statut',
        'montant',
        'montant_lettres',
        'numero',
        'date_virement',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}