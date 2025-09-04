<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\UuidTrait;

class ForumPost extends Model
{
    use UuidTrait;

    protected $primaryKey = 'uuid';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = ['uuid', 'user_id', 'title', 'body', 'status', 'pinned'];

    protected $casts = [
        'pinned' => 'boolean',
        'status' => 'string',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'uuid');
    }

    public function tags()
    {
        return $this->belongsToMany(ForumTag::class, 'forum_post_tag', 'forum_post_id', 'forum_tag_id', 'uuid', 'uuid');
    }

    public function comments()
    {
        return $this->hasMany(ForumComment::class, 'forum_post_id', 'uuid')->where('status', 'active');
    }

    public function votes()
    {
        return $this->hasMany(ForumVote::class, 'forum_post_id', 'uuid');
    }

    public function reports()
    {
        return $this->morphMany(ForumReport::class, 'reportable');
    }
}