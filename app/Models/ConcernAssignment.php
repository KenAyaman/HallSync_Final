<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConcernAssignment extends Model
{
    protected $fillable = ['concern_id', 'assigned_by', 'assigned_to', 'assignment_role', 'notes', 'ended_at'];

    protected function casts(): array
    {
        return ['ended_at' => 'datetime'];
    }

    public function concern() { return $this->belongsTo(Concern::class); }
    public function assigner() { return $this->belongsTo(User::class, 'assigned_by'); }
    public function assignee() { return $this->belongsTo(User::class, 'assigned_to'); }
}
