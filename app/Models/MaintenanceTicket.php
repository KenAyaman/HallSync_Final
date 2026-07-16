<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaintenanceTicket extends Model
{
    public const STATUS_PENDING_APPROVAL = 'pending_approval';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_ASSIGNED = 'assigned';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_RESOLVED = 'resolved';
    public const STATUS_CLOSED = 'closed';
    public const STATUS_CANCELLED = 'cancelled';

    public const OPEN_STATUSES = [
        self::STATUS_PENDING_APPROVAL,
        self::STATUS_APPROVED,
        self::STATUS_ASSIGNED,
        self::STATUS_IN_PROGRESS,
        self::STATUS_RESOLVED,
    ];

    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'assigned_to',
        'ticket_id',
        'title',
        'description',
        'category',
        'location',
        'priority',
        'status',
        'image_path',
        'video_path',
        'rejection_reason',
        'work_started_at',
        'task_started_at',
        'task_completed_at',
        'task_duration_minutes',
        'completion_note',
        'satisfaction_rating',
        'satisfaction_note',
        'satisfaction_rated_at',
        'resolved_at',
        'closed_at',
        'reopen_count',
        'cancellation_requested_at',
        'cancellation_reason',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'work_started_at' => 'datetime',
        'task_started_at' => 'datetime',
        'task_completed_at' => 'datetime',
        'satisfaction_rated_at' => 'datetime',
        'resolved_at' => 'datetime',
        'closed_at' => 'datetime',
        'cancellation_requested_at' => 'datetime',
    ];

    public static function normalizePriorityValue(?string $priority): string
    {
        return match ($priority) {
            'urgent', 'high', 'critical' => 'critical',
            'low' => 'low',
            default => 'medium',
        };
    }

    public static function isRecentDuplicate(
        int $userId,
        string $category,
        string $title,
        string $description,
        int $withinMinutes = 15
    ): bool {
        $normalize = fn (string $value) => strtolower(trim(preg_replace('/[^a-z0-9]+/i', ' ', $value)));
        $normalizedTitle = $normalize($title);
        $normalizedDescription = $normalize($description);

        return static::query()
            ->where('user_id', $userId)
            ->where('category', $category)
            ->where('created_at', '>=', now()->subMinutes($withinMinutes))
            ->whereNotIn('status', ['rejected'])
            ->get(['title', 'description'])
            ->contains(function (self $ticket) use ($normalize, $normalizedTitle, $normalizedDescription) {
                similar_text($normalize($ticket->description), $normalizedDescription, $descriptionSimilarity);

                return $normalize($ticket->title) === $normalizedTitle && $descriptionSimilarity >= 90;
            });
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

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING_APPROVAL => 'Submitted for Review',
            self::STATUS_APPROVED         => 'Approved',
            self::STATUS_ASSIGNED         => 'Assigned to Staff',
            self::STATUS_IN_PROGRESS      => 'Work in Progress',
            self::STATUS_RESOLVED         => 'Resolved — Awaiting Confirmation',
            self::STATUS_CLOSED           => 'Closed',
            self::STATUS_CANCELLED        => 'Cancelled',
            self::STATUS_REJECTED         => 'Rejected',
            default => ucfirst(str_replace('_', ' ', $this->status)),
        };
    }

    public function getTaskDurationLabelAttribute(): string
    {
        if ($this->task_duration_minutes === null) {
            return 'Not completed yet';
        }

        $hours = intdiv($this->task_duration_minutes, 60);
        $minutes = $this->task_duration_minutes % 60;

        return trim(($hours ? "{$hours} hr " : '') . ($minutes ? "{$minutes} min" : '')) ?: '0 min';
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image_path
            ? route('media.tickets.show', ['ticket' => $this, 'type' => 'image'])
            : null;
    }

    public function getVideoUrlAttribute(): ?string
    {
        return $this->video_path
            ? route('media.tickets.show', ['ticket' => $this, 'type' => 'video'])
            : null;
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function getTrackerStepsAttribute(): array
    {
        // Cancelled and rejected tickets show only the first step as complete,
        // plus a terminal step reflecting their final state (H-07, M-10).
        if ($this->status === self::STATUS_CANCELLED) {
            return [
                ['status' => 'pending_approval', 'label' => 'Submitted for Review', 'complete' => true],
                ['status' => 'cancelled', 'label' => 'Cancelled', 'complete' => true, 'terminal' => true],
            ];
        }

        if ($this->status === self::STATUS_REJECTED) {
            return [
                ['status' => 'pending_approval', 'label' => 'Submitted for Review', 'complete' => true],
                ['status' => 'rejected', 'label' => 'Rejected', 'complete' => true, 'terminal' => true],
            ];
        }

        $steps = [
            'pending_approval' => 'Submitted for Review',
            'assigned'         => 'Assigned to Staff',
            'in_progress'      => 'Work in Progress',
            'resolved'         => 'Resolved',
        ];

        $currentStatus = match ($this->status) {
            self::STATUS_APPROVED => self::STATUS_PENDING_APPROVAL,
            self::STATUS_CLOSED, 'completed' => self::STATUS_RESOLVED,
            default => $this->status,
        };
        $stepKeys      = array_keys($steps);
        $currentIndex  = array_search($currentStatus, $stepKeys, true);

        return collect($steps)->map(function ($label, $status) use ($currentIndex, $stepKeys) {
            $stepIndex = array_search($status, $stepKeys, true);

            return [
                'status'   => $status,
                'label'    => $label,
                'complete' => $currentIndex !== false && $stepIndex <= $currentIndex,
            ];
        })->values()->all();
    }
}
