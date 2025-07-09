<?php

namespace App\Models;

use App\Enums\RoleEnum;
use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use UuidTrait, SoftDeletes;

    protected $table = 'users';

    protected $fillable = [
        'email',
        'password',
        'role',
        'status',
        'is_active',
        'email_verified_at',
    ];

    protected $casts = [
        'role' => RoleEnum::class,
        'is_active' => 'boolean',
        'email_verified_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $hidden = ['password'];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function profile()
    {
        return $this->hasOne(Profile::class, 'user_id', 'uuid');
    }
}