<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TvaRate extends Model
{
    protected $fillable = ['type_vehicule', 'taux'];

    protected $casts = [
        'taux' => 'decimal:2',
    ];
}