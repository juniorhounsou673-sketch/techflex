<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name', 'email', 'phone', 'password', 'role', 'status', 'blocked_at', 'blocked_reason',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'blocked_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function reseller(): HasOne
    {
        return $this->hasOne(Reseller::class);
    }

    public function isAdmin(): bool     { return $this->role === 'admin'; }
    public function isReseller(): bool  { return $this->role === 'revendeur'; }
    public function isBlocked(): bool   { return $this->status === 'bloque'; }
}
