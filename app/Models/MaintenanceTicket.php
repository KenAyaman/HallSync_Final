<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'assigned_to',  // Add this line
        'ticket_id',
        'title',
        'description',
        'category',
        'priority',
        'status',
        'image_path',
        'video_path',
        'rejection_reason',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public static function normalizePriorityValue(?string $priority): string
    {
        return match ($priority) {
            'urgent', 'high', 'critical' => 'critical',
            'low' => 'low',
            default => 'medium',
        };
    }

    public function getNormalizedPriorityAttribute(): string
    {
        return self::normalizePriorityValue($this->priority);
    }

    public function getPriorityLabelAttribute(): string
    {
        return match ($this->normalized_priority) {
            'critical' => 'Critical',
            'low' => 'Low Priority',
            'medium' => 'Medium',
            default => ucfirst($this->normalized_priority),
        };
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
