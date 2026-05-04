<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens, Notifiable, HasFactory;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'role',
        'organization_id',
        'status',
        'fcm_token',
        'rejection_reason',
        'can_review_accounts',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function canAccessPanel(Panel $panel): bool
    {
        return ($this->role === 'admin' || $this->can_review_accounts)
            && ($this->status ?? 'approved') === 'approved';
    }

    public function canReviewAccounts(): bool
    {
        return $this->role === 'admin' || (bool) $this->can_review_accounts;
    }
}
