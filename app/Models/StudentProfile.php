<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentProfile extends Model
{
    use HasFactory, UuidTrait, SoftDeletes;

    protected $fillable = [
        'user_id',
        'date_of_birth',
        'school',
        'grade_level',
        'learning_style',
        'subjects_interested',
        'career_goals',
        'location',
    ];

    protected $casts = [
        'subjects_interested' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'uuid');
    }
}
