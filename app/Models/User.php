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
        'username',
        'email',
        'password',
        'role',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
        'role'              => UserRole::class,
    ];

    // ─── Filament Access ─────────────────────────────────────────────────────────

    public function canAccessPanel(Panel $panel): bool
    {
        return match ($panel->getId()) {
            'percetakan' => $this->role === UserRole::ADMIN_PERCETAKAN,
            'keuangan'   => $this->role === UserRole::ADMIN_KEUANGAN,
            'admin'      => in_array($this->role, [UserRole::ADMIN_PERCETAKAN, UserRole::ADMIN_KEUANGAN]),
            default      => false,
        };
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────────

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

    // ─── Relations ───────────────────────────────────────────────────────────────

    public function daerah()
    {
        return $this->hasOne(Daerah::class);
    }

    public function stockLogs()
    {
        return $this->hasMany(StockLog::class);
    }

    public function confirmedPayments()
    {
        return $this->hasMany(Payment::class, 'confirmed_by');
    }
}