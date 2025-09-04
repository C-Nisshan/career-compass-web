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

    protected $fillable = ['uuid', 'forum_post_id', 'reported_by_user_id', 'reason', 'status'];

    protected $casts = [
        'status' => 'string',
    ];

    public function post()
    {
        return $this->belongsTo(ForumPost::class, 'forum_post_id', 'uuid');
    }

    public function reportedBy()
    {
        return $this->belongsTo(User::class, 'reported_by_user_id', 'uuid');
    }
}