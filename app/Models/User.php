<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\ValidationException;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    protected static function booted(): void
    {
        static::deleting(function (User $user) {
            if ($user->isLastActiveManager()) {
                throw ValidationException::withMessages([
                    'user' => 'Keep at least one active administrator account.',
                ]);
            }

            if ($user->hasOperationalRecords()) {
                throw ValidationException::withMessages([
                    'user' => 'This account has operational history and cannot be deleted. Deactivate it instead.',
                ]);
            }
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'profile_photo_path',
        'password',
        'role',
        'room_number',
        'resident_number',
        'phone_number',
        'is_active',
        'residency_status',
        'deactivated_at',
        'moved_out_at',
        'must_change_password',
        'password_reset_at',
        'temporary_password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'temporary_password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_active' => 'boolean',
            'deactivated_at' => 'datetime',
            'moved_out_at' => 'datetime',
            'must_change_password' => 'boolean',
            'password_reset_at' => 'datetime',
            'temporary_password' => 'encrypted',
            'last_login_at' => 'datetime',
        ];
    }

    /**
     * Helper methods for role checking
     */
    public function isManager(): bool
    {
        return $this->role === 'manager';
    }

    public function isHandyman(): bool
    {
        return $this->role === 'handyman';
    }

    public function isResident(): bool
    {
        return $this->role === 'resident';
    }

    public function getProfilePhotoUrlAttribute(): ?string
    {
        return $this->profile_photo_path
            ? route('media.users.profile-photo', ['user' => $this, 'v' => optional($this->updated_at)->timestamp])
            : null;
    }

    public function getProfileInitialsAttribute(): string
    {
        return collect(explode(' ', trim($this->name)))
            ->filter()
            ->take(2)
            ->map(fn ($part) => strtoupper(substr($part, 0, 1)))
            ->implode('');
    }

    public function concerns()
    {
        return $this->hasMany(Concern::class);
    }

    public function handledConcerns()
    {
        return $this->hasMany(Concern::class, 'handled_by');
    }

    public function maintenanceTickets()
    {
        return $this->hasMany(MaintenanceTicket::class);
    }

    public function assignedTickets()
    {
        return $this->hasMany(MaintenanceTicket::class, 'assigned_to');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function communityPosts()
    {
        return $this->hasMany(CommunityPost::class);
    }

    public function communityComments()
    {
        return $this->hasMany(CommunityComment::class);
    }

    public function announcements()
    {
        return $this->hasMany(Announcement::class);
    }

    public function activityLogs()
    {
        return $this->hasMany(UserActivityLog::class, 'subject_user_id');
    }

    public function performedActivityLogs()
    {
        return $this->hasMany(UserActivityLog::class, 'actor_user_id');
    }

    public function notificationReads()
    {
        return $this->hasMany(NotificationRead::class);
    }

    public function rosterEntry()
    {
        return $this->hasOne(ResidentRosterEntry::class, 'claimed_by_user_id');
    }

    public function getRoleLabelAttribute(): string
    {
        return match ($this->role) {
            'manager' => 'Administrator',
            'handyman' => 'Staff',
            default => 'Resident',
        };
    }

    public function isLastActiveManager(): bool
    {
        return $this->isManager()
            && $this->is_active !== false
            && static::query()
                ->where('role', 'manager')
                ->where('is_active', true)
                ->where('id', '!=', $this->id)
                ->doesntExist();
    }

    public function approvedCommunityPosts()
    {
        return $this->hasMany(CommunityPost::class, 'approved_by');
    }

    public function operationalRecordCounts(): array
    {
        return [
            'maintenance requests'    => $this->maintenanceTickets()->count(),
            'assigned requests'       => $this->assignedTickets()->count(),
            'bookings'                => $this->bookings()->count(),
            'community posts'         => $this->communityPosts()->count(),
            'community comments'      => $this->communityComments()->count(),
            'concern reports'         => $this->concerns()->count(),
            'handled concerns'        => $this->handledConcerns()->count(),
            'announcements'           => $this->announcements()->count(),
            'approved community posts'=> $this->approvedCommunityPosts()->count(),
        ];
    }

    public function hasOperationalRecords(): bool
    {
        return collect($this->operationalRecordCounts())->contains(fn (int $count) => $count > 0);
    }
}
