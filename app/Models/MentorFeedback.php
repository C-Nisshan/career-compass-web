<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\UuidTrait;

class MentorFeedback extends Model
{
    use UuidTrait;

    protected $primaryKey = 'uuid';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'uuid',
        'student_id',
        'mentor_id',
        'feedback',
        'rating',
        'status',
    ];

    protected $casts = [
        'rating' => 'integer',
        'status' => 'string',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id', 'uuid');
    }

    public function mentor()
    {
        return $this->belongsTo(User::class, 'mentor_id', 'uuid');
    }
}