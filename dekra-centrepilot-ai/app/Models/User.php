<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Invoice;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // =========================
    // Relations
    // =========================

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function rappels()
    {
        return $this->hasMany(Rappel::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function virements()
{
    return $this->hasMany(Virement::class);
}

public function versements()
{
    return $this->hasMany(Versement::class);
}
   
    public function titreProprietes()
    {
        return $this->hasMany(TitrePropriete::class);
    }

    public function emails()
    {
        return $this->hasMany(Email::class);
    }

    public function assistantIA()
{
    return $this->hasMany(AssistantIA::class);
}
public function invoices()
{
    return $this->hasMany(Invoice::class);
}
}