<?php

namespace App\Models;

use App\Enums\VerifiedStatusEnum;
use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Profile extends Model
{
    use HasFactory, SoftDeletes, UuidTrait;

    protected $table = 'profiles';

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'phone',
        'address',
        'nic_number',
        'profile_picture_path',
        'verified_status',
        'completion_step',
    ];

    protected $casts = [
        'verified_status' => VerifiedStatusEnum::class,
        'completion_step' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'uuid');
    }

}