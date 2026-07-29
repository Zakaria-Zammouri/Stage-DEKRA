<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssistantIA extends Model
{
    use HasFactory;

    protected $table = 'assistant_i_a_s';

    protected $fillable = [
        'user_id',
        'type',
        'question',
        'reponse',
        'fichier_nom',
        'type_document',
        'champs_extraits',
    ];

    protected $casts = [
        'champs_extraits' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}