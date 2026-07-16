<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConcernMessage extends Model
{
    protected $fillable = ['concern_id', 'user_id', 'message', 'read_at'];

    protected function casts(): array
    {
        return ['read_at' => 'datetime'];
    }

    public function concern() { return $this->belongsTo(Concern::class); }
    public function user() { return $this->belongsTo(User::class); }
}
