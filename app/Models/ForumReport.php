<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\UuidTrait;

class ForumReport extends Model
{
    use UuidTrait;

    protected $primaryKey = 'uuid';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = ['uuid', 'reportable_id', 'reportable_type', 'reported_by_user_id', 'reason', 'status'];

    protected $casts = [
        'status' => 'string',
    ];

    public function reportable()
    {
        return $this->morphTo();
    }

    public function reportedBy()
    {
        return $this->belongsTo(User::class, 'reported_by_user_id', 'uuid');
    }
}