<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class QuizResult extends Model
{
    use HasFactory;

    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'quiz_id',
        'score',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    /**
     * Get the user that owns the quiz result.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'uuid');
    }

    /**
     * Get the quiz associated with this result.
     */
    public function quiz()
    {
        return $this->belongsTo(Quiz::class, 'quiz_id', 'uuid');
    }
}