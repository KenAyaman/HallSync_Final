<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    public const STATUS_APPROVED  = 'approved';
    public const STATUS_CANCELLED = 'cancelled';
    // STATUS_REJECTED removed — no booking rejection workflow exists (L-03).

    use HasFactory;

    protected $fillable = [
        'user_id',
        'facility_name',
        'booking_date',
        'end_time',
        'status',
        'notes',
        'group_members',
        'rejection_reason',
        'rejected_at',
    ];

    protected $casts = [
        'booking_date' => 'datetime',
        'end_time'     => 'datetime',
        'rejected_at'  => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeActive($query)
    {
        return $query->where('end_time', '>', now());
    }
}
