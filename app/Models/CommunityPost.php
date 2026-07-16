<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CommunityPost extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'content',
        'type',
        'status',
        'image_path',
        'video_path',
        'rejection_reason',
        'approved_at',
        'approved_by'
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function comments()
    {
        return $this->hasMany(CommunityComment::class);
    }

    public function likes()
    {
        return $this->hasMany(CommunityPostLike::class);
    }

    public function isApproved()
    {
        return $this->status === 'approved';
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image_path
            ? route('media.community.show', ['communityPost' => $this, 'type' => 'image'])
            : null;
    }

    public function getVideoUrlAttribute(): ?string
    {
        return $this->video_path
            ? route('media.community.show', ['communityPost' => $this, 'type' => 'video'])
            : null;
    }

    public function isLikedBy(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        if (isset($this->liked_by_user)) {
            return (bool) $this->liked_by_user;
        }

        return $this->likes()->where('user_id', $user->id)->exists();
    }
}
