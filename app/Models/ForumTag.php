<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\UuidTrait;

class ForumTag extends Model
{
    use UuidTrait;

    protected $primaryKey = 'uuid';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = ['uuid', 'name'];

    public function posts()
    {
        return $this->belongsToMany(ForumPost::class, 'forum_post_tag', 'forum_tag_id', 'forum_post_id', 'uuid', 'uuid');
    }
}