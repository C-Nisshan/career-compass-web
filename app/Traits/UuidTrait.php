<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait UuidTrait
{
    /**
     * Boot the trait.
     */
    protected static function bootUuidTrait()
    {
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $attempt = 0;
                $maxAttempts = 5;
                do {
                    $uuid = (string) Str::uuid();
                    $attempt++;
                    if ($attempt > $maxAttempts) {
                        throw new \Exception('Failed to generate unique UUID after max attempts');
                    }
                } while (static::where($model->getKeyName(), $uuid)->exists());

                $model->{$model->getKeyName()} = $uuid;
            }
        });
    }

    /**
     * Get the primary key for the model.
     *
     * @return string
     */
    public function getKeyName()
    {
        return 'uuid';
    }

    /**
     * Get the auto-incrementing key type.
     *
     * @return string
     */
    public function getKeyType()
    {
        return 'string';
    }

    /**
     * Indicate that the model's primary key is not incrementing.
     *
     * @return bool
     */
    public function getIncrementing()
    {
        return false;
    }
}