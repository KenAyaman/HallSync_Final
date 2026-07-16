<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConcernInternalNote extends Model
{
    protected $fillable = ['concern_id', 'author_id', 'note'];

    public function concern() { return $this->belongsTo(Concern::class); }
    public function author() { return $this->belongsTo(User::class, 'author_id'); }
}
