<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\UuidTrait;

class ForumComment extends Model
{
    use UuidTrait;

    protected $primaryKey = 'uuid';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = ['uuid', 'forum_post_id', 'user_id', 'comment', 'status'];

    protected $casts = [
        'status' => 'string',
    ];

    public function post()
    {
        return $this->belongsTo(ForumPost::class, 'forum_post_id', 'uuid');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'uuid');
    }
}