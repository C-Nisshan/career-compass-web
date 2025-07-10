<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\SoftDeletes;

class MentorProfile extends Model
{
    use HasFactory, UuidTrait, SoftDeletes;

    protected $fillable = [
        'user_id',
        'profession_title',
        'industry',
        'experience_years',
        'bio',
        'areas_of_expertise',
        'linkedin_url',
        'portfolio_url',
        'availability',
    ];

    protected $casts = [
        'areas_of_expertise' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'uuid');
    }
}
