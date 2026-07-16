<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConcernAuditLog extends Model
{
    protected $fillable = ['concern_id', 'actor_id', 'action', 'metadata', 'ip_address', 'user_agent'];

    protected function casts(): array
    {
        return ['metadata' => 'array'];
    }

    public function concern() { return $this->belongsTo(Concern::class); }
    public function actor() { return $this->belongsTo(User::class, 'actor_id'); }
}
