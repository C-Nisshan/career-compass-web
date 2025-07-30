<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PasswordResetOtp extends Model
{
    use HasFactory, UuidTrait;
    protected $fillable = ['email', 'otp', 'expires_at'];
    
    public $incrementing = false;
    protected $keyType = 'string';

    public function isExpired()
    {
        return $this->expires_at < now();
    }
}