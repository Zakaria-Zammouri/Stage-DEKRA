<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Loggable;

class Rappel extends Model
{
    use SoftDeletes;
    use Loggable;

    protected $fillable = [
        'user_id',
        'titre',
        'description',
        'type',
        'date_rappel',
        'termine',
    ];
}