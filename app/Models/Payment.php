<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Validation\Rule;

class Payment extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'bill_id',
        'amount',
        'confirmed_by',
        'payment_date',
        'confirmed_at',
        'notes',
        'proof_image',
    ];

    protected $casts = [
        'amount'       => 'decimal:2',
        'payment_date' => 'date',
        'confirmed_at' => 'datetime',
    ];

    // ─── Validation ─────────────────────────────────────────────────────────────

    public static function validationRules(): array
    {
        return [
            'amount'       => ['required', 'numeric', 'gt:0'],
            'confirmed_by' => ['required', 'uuid', 'exists:users,id'],
            'payment_date' => ['required', 'date'],
            'notes'        => ['nullable', 'string', 'max:1000'],
            'proof_image'  => ['nullable', 'image', 'max:2048'],
        ];
    }

    // ─── Accessors ──────────────────────────────────────────────────────────────

    public function getProofImageUrlAttribute(): ?string
    {
        return $this->proof_image ? \Illuminate\Support\Facades\Storage::disk('public')->url($this->proof_image) : null;
    }

    // ─── Relations ───────────────────────────────────────────────────────────────

    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }

    public function confirmedBy()
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }
}
