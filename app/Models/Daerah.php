<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Daerah extends Model
{
    protected $fillable = [
        'user_id',
        'nama_daerah',
        'nama_penanggung_jawab',
        'no_telepon_pj',
        'alamat_default',
        'no_telepon',
        'is_active',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}