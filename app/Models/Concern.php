<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Concern extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'handled_by',
        'category',
        'subject',
        'involved_person',
        'location',
        'incident_at',
        'details',
        'status',
        'admin_reply',
        'replied_at',
    ];

    protected function casts(): array
    {
        return [
            'incident_at' => 'datetime',
            'replied_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function handler()
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'in_review' => 'Under Review',
            'responded' => 'Responded',
            'closed' => 'Closed',
            default => 'Submitted',
        };
    }
}
