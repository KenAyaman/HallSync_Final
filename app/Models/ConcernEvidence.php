<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConcernEvidence extends Model
{
    protected $table = 'concern_evidence';

    protected $fillable = [
        'concern_id', 'uploaded_by', 'disk', 'path', 'original_name', 'mime_type', 'size', 'sha256',
    ];

    public function concern() { return $this->belongsTo(Concern::class); }
    public function uploader() { return $this->belongsTo(User::class, 'uploaded_by'); }
}
