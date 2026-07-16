<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResidentRosterEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'resident_number',
        'name',
        'email',
        'room_number',
        'is_active',
        'claimed_by_user_id',
        'claimed_at',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'claimed_at' => 'datetime',
        ];
    }

    public function claimedBy()
    {
        return $this->belongsTo(User::class, 'claimed_by_user_id');
    }
}
