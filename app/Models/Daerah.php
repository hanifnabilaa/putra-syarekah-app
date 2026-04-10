<?php

namespace App\Models;

use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Daerah extends Model
{
    use HasFactory;

    protected $table = 'daerahs';

    protected $fillable = [
        'user_id',
        'name',
        'pic_name',
        'pic_phone',
        'address',
        'phone',
        'is_active',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}