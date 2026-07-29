<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Loggable;

class Versement extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Loggable;

    protected $fillable = [
        'user_id',
        'reference',
        'client',
        'montant',
        'date_versement',
        'mode_reglement',
        'notes',
    ];

    protected $casts = [
        'date_versement' => 'date',
        'montant' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}