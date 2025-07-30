<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Enums\RoleEnum;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable, UuidTrait, SoftDeletes;

    protected $primaryKey = 'uuid';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'uuid',
        'email',
        'password',
        'role',
        'status',
        'is_active',
        'token_version',
        'first_name',
        'last_name',
        'phone',
        'address',
        'nic_number',
        'profile_picture',
    ];

    protected $hidden = [
        'password',
        'token_version',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'token_version' => 'integer',
        'role' => RoleEnum::class, // Cast role to enum
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [
            'token_version' => $this->token_version,
        ];
    }

    public function profile()
    {
        return $this->hasOne(Profile::class, 'user_id', 'uuid');
    }

    public function studentProfile()
    {
        return $this->hasOne(StudentProfile::class, 'user_id', 'uuid');
    }

    public function mentorProfile()
    {
        return $this->hasOne(MentorProfile::class, 'user_id', 'uuid');
    }
}