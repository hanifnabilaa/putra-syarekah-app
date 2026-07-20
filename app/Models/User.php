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
        'parent_id',
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
            'sekretaris' => $this->role === UserRole::SEKRETARIS,
            'percetakan' => in_array($this->role, [UserRole::PERCETAKAN, UserRole::KARYAWAN_PERCETAKAN]),
            'gudang'     => in_array($this->role, [UserRole::GUDANG, UserRole::KARYAWAN_GUDANG]),
            'daerah'     => $this->role === UserRole::DAERAH,
            'keuangan'   => $this->role === UserRole::KEUANGAN,
            'atasan'     => $this->role === UserRole::ATASAN,
            'admin'      => in_array($this->role, [UserRole::SEKRETARIS, UserRole::PERCETAKAN, UserRole::KARYAWAN_PERCETAKAN, UserRole::GUDANG, UserRole::KARYAWAN_GUDANG, UserRole::KEUANGAN, UserRole::ATASAN]),
            default      => false,
        };
    }

    public function getMasterId(): int
    {
        return $this->parent_id ?? $this->id;
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────────

    public function isSekretaris(): bool
    {
        return $this->role === UserRole::SEKRETARIS;
    }

    public function isPercetakan(): bool
    {
        return in_array($this->role, [UserRole::PERCETAKAN, UserRole::KARYAWAN_PERCETAKAN]);
    }

    public function isGudang(): bool
    {
        return in_array($this->role, [UserRole::GUDANG, UserRole::KARYAWAN_GUDANG]);
    }

    public function isDaerah(): bool
    {
        return $this->role === UserRole::DAERAH;
    }

    public function isKeuangan(): bool
    {
        return $this->role === UserRole::KEUANGAN;
    }

    public function isAtasan(): bool
    {
        return $this->role === UserRole::ATASAN;
    }

    // ─── Relations ───────────────────────────────────────────────────────────────

    public function daerah()
    {
        return $this->hasOne(Daerah::class);
    }

    public function percetakan()
    {
        return $this->hasOne(Percetakan::class);
    }

    public function gudang()
    {
        return $this->hasOne(Gudang::class);
    }

    public function stockLogs()
    {
        return $this->hasMany(StockLog::class);
    }

    public function printingOrders(): HasMany
    {
        // Sekretaris owns PrintingOrders
        return $this->hasMany(PrintingOrder::class, 'sekretaris_id');
    }

    public function productStocks(): HasMany
    {
        return $this->hasMany(ProductStock::class, 'gudang_id');
    }

    public function confirmedPayments()
    {
        return $this->hasMany(Payment::class, 'confirmed_by');
    }

    public function productTransfersSent()
    {
        return $this->hasMany(ProductTransfer::class, 'from_gudang_id');
    }

    public function productTransfersReceived()
    {
        return $this->hasMany(ProductTransfer::class, 'to_gudang_id');
    }
}