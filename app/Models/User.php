<?php

namespace App\Models;

use App\Enums\UserRole;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'role' => UserRole::class,
    ];

    public function canAccessPanel(Panel $panel): bool
    {
        return match ($panel->getId()) {
            'admin' => in_array($this->role, [UserRole::ADMIN_PERCETAKAN, UserRole::ADMIN_KEUANGAN]),
            'percetakan' => $this->role === UserRole::ADMIN_PERCETAKAN,
            'keuangan' => $this->role === UserRole::ADMIN_KEUANGAN,
            default => false,
        };
    }

    public function daerah()
    {
        return $this->hasOne(Daerah::class);
    }

     public function stokLogs()
    {
        return $this->hasMany(StockLog::class);
    }

    public function pembayaranDikonfirmasi()
    {
        return $this->hasMany(Payment::class, 'confirmed_by');
    }

    // Helper
    public function isPercetakan(): bool
    {
        return $this->role === UserRole::ADMIN_PERCETAKAN;
    }

    public function isKeuangan(): bool
    {
        return $this->role === UserRole::ADMIN_KEUANGAN;
    }

    public function isDaerah(): bool
    {
        return $this->role === UserRole::DAERAH;
    }
}