<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NewsletterSubscription extends Model
{
    use HasFactory, SoftDeletes, UuidTrait;

    protected $table = 'newsletter_subscriptions';

    protected $fillable = [
        'email',
        'user_id',
        'subscribed_at',
    ];

    protected $casts = [
        'subscribed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'uuid')->nullable();
    }
}
