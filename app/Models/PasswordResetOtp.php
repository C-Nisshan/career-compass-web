<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Model;

class PasswordResetOtp extends Model
{
    use UuidTrait;

    protected $fillable = ['email', 'otp', 'expires_at'];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function isExpired()
    {
        return $this->expires_at->isPast();
    }
}