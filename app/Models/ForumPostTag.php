<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\UuidTrait;

class ForumPostTag extends Model
{
    use UuidTrait;

    protected $primaryKey = 'uuid';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = ['uuid', 'forum_post_id', 'forum_tag_id'];

    public function post()
    {
        return $this->belongsTo(ForumPost::class, 'forum_post_id', 'uuid');
    }

    public function tag()
    {
        return $this->belongsTo(ForumTag::class, 'forum_tag_id', 'uuid');
    }
}