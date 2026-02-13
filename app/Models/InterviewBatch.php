<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class InterviewBatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'batch_name',
        'interview_date',
        'start_time',
        'end_time',
        'batch_size',
        'location',
        'status',
    ];

    protected $casts = [
        'interview_date' => 'date',
    ];

    /*
    |--------------------------------------------------------------------------
    | Auto Close Batch After Interview Date
    |--------------------------------------------------------------------------
    */

    protected static function booted()
    {
        static::retrieved(function ($batch) {

            if (
                $batch->status === 'Scheduled' &&
                now()->greaterThan($batch->interview_date->endOfDay())
            ) {
                $batch->updateQuietly([
                    'status' => 'Closed'
                ]);
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function applications(): BelongsToMany
    {
         return $this->belongsToMany(
            \App\Models\Application::class,
            'interview_batch_intern'
        )->withPivot('is_present')
        ->withTimestamps();
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods (Optional but Recommended)
    |--------------------------------------------------------------------------
    */

    public function isClosed(): bool
    {
        return $this->status === 'Closed';
    }

    public function isScheduled(): bool
    {
        return $this->status === 'Scheduled';
    }

    public function presentCount(): int
    {
        return $this->applications()->where('attendance', 'Present')->count();
    }

    public function absentCount(): int
    {
        return $this->applications()->where('attendance', 'Absent')->count();
    }
}
