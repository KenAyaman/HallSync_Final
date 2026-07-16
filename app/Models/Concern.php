<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class Concern extends Model
{
    use HasFactory;

    public const CATEGORIES = [
        'noise' => 'Noise Complaint',
        'substance' => 'Drinking / Substance Use',
        'roommate' => 'Roommate Conflict',
        'harassment' => 'Harassment',
        'safety' => 'Safety Concern',
        'facility_misuse' => 'Facility Misuse',
        'policy' => 'Rule Violation',
        'other' => 'Other',
    ];

    public const STATUSES = [
        'draft'                      => 'Draft',
        'submitted'                  => 'Submitted',
        'under_review'               => 'Under Review',
        'investigation_ongoing'      => 'Investigation Ongoing',
        'awaiting_resident_response' => 'Awaiting Resident Response',
        // 'responded' was retired by migration 2026_06_02_010000 but kept here so old records
        // display a readable label rather than a raw status string (H-03).
        'responded'                  => 'Replied (Legacy)',
        'resolved'                   => 'Resolved',
        'closed'                     => 'Closed',
        'rejected'                   => 'Rejected',
        'reopened'                   => 'Reopened',
    ];

    private const TRANSITIONS = [
        'draft' => ['submitted'],
        'submitted' => ['under_review', 'rejected'],
        'under_review' => ['investigation_ongoing', 'awaiting_resident_response', 'resolved', 'rejected'],
        'investigation_ongoing' => ['awaiting_resident_response', 'resolved'],
        'awaiting_resident_response' => ['investigation_ongoing', 'resolved'],
        'responded' => ['investigation_ongoing', 'resolved'],
        'resolved' => ['closed', 'reopened'],
        'reopened' => ['under_review', 'investigation_ongoing', 'resolved'],
    ];

    protected $fillable = [
        'concern_id',
        'user_id',
        'handled_by',
        'category',
        'subject',
        'involved_person',
        'location',
        'incident_at',
        'details',
        'status',
        'priority',
        'is_anonymous',
        'admin_reply',
        'replied_at',
        'submitted_at',
        'review_started_at',
        'resolved_at',
        'closed_at',
        'due_at',
        'reopen_count',
        'resolution_notes',
    ];

    protected function casts(): array
    {
        return [
            'incident_at' => 'datetime',
            'replied_at' => 'datetime',
            'is_anonymous' => 'boolean',
            'submitted_at' => 'datetime',
            'review_started_at' => 'datetime',
            'resolved_at' => 'datetime',
            'closed_at' => 'datetime',
            'due_at' => 'datetime',
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

    public function messages() { return $this->hasMany(ConcernMessage::class); }
    public function evidence() { return $this->hasMany(ConcernEvidence::class); }
    public function assignments() { return $this->hasMany(ConcernAssignment::class); }
    public function statusHistories() { return $this->hasMany(ConcernStatusHistory::class); }
    public function internalNotes() { return $this->hasMany(ConcernInternalNote::class); }
    public function auditLogs() { return $this->hasMany(ConcernAuditLog::class); }

    public function getCategoryLabelAttribute(): string
    {
        return self::CATEGORIES[$this->category] ?? Str::headline($this->category);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? Str::headline($this->status);
    }

    public function isEditableByResident(): bool
    {
        return in_array($this->status, ['draft', 'submitted'], true);
    }

    public function canTransitionTo(string $status): bool
    {
        return in_array($status, self::TRANSITIONS[$this->status] ?? [], true);
    }

    public function transitionTo(string $status, ?User $actor, ?string $reason = null): void
    {
        if (! $this->canTransitionTo($status)) {
            throw ValidationException::withMessages([
                'status' => "A concern cannot move from {$this->status_label} to " . (self::STATUSES[$status] ?? Str::headline($status)) . '.',
            ]);
        }

        $from = $this->status;
        $timestamps = match ($status) {
            'submitted' => ['submitted_at' => now()],
            'under_review' => ['review_started_at' => $this->review_started_at ?? now()],
            'resolved' => ['resolved_at' => now()],
            'closed' => ['closed_at' => now()],
            default => [],
        };

        $this->forceFill(array_merge(['status' => $status], $timestamps))->save();
        $this->statusHistories()->create([
            'changed_by' => $actor?->id,
            'from_status' => $from,
            'to_status' => $status,
            'reason' => $reason,
        ]);
        $this->recordAudit('status.changed', $actor, compact('from', 'status', 'reason'));
    }

    public function recordAudit(string $action, ?User $actor, array $metadata = []): ConcernAuditLog
    {
        return $this->auditLogs()->create([
            'actor_id' => $actor?->id,
            'action' => $action,
            'metadata' => $metadata ?: null,
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->userAgent(),
        ]);
    }
}
