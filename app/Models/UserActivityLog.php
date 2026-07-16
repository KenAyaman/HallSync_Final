<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject_user_id',
        'actor_user_id',
        'action',
        'description',
        'metadata',
        'ip_address',
        'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
        ];
    }

    public static function record(
        string $action,
        string $description,
        ?User $subject = null,
        ?User $actor = null,
        array $metadata = []
    ): self {
        return self::create([
            'subject_user_id' => $subject?->id,
            'actor_user_id' => $actor?->id,
            'action' => $action,
            'description' => $description,
            'metadata' => $metadata === [] ? null : $metadata,
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->userAgent(),
        ]);
    }

    public static function recordDomain(string $action, string $description, ?User $actor = null, array $metadata = []): self
    {
        return self::record($action, $description, null, $actor, $metadata);
    }

    public function subject()
    {
        return $this->belongsTo(User::class, 'subject_user_id');
    }

    public function actor()
    {
        return $this->belongsTo(User::class, 'actor_user_id');
    }
}
