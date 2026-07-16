<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConcernStatusHistory extends Model
{
    protected $fillable = ['concern_id', 'changed_by', 'from_status', 'to_status', 'reason'];

    public function concern() { return $this->belongsTo(Concern::class); }
    public function actor() { return $this->belongsTo(User::class, 'changed_by'); }
}
