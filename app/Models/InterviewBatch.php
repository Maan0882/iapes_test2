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

        // static::updated(function ($batch) {
        //     // If batch is canceled
        //     if ($batch->status === 'Canceled') {
        //         // Update all related applications
        //         $batch->applications()->update([
        //             'status' => 'Applied', // rollback to applied
        //         ]);
        //     }

        //     // Auto-close scheduled batch (existing code)
        //     if ($batch->status === 'Scheduled' && now()->greaterThan($batch->interview_date->endOfDay())) {
        //         $batch->updateQuietly([
        //             'status' => 'Closed'
        //         ]);
        //     }
        // });
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

    public function assignIntern($applicationId)
    {
        // Prevent duplicate assignment
        if ($this->applications()->where('application_id', $applicationId)->exists()) {
            return;
        }

        // Attach intern to batch
        $this->applications()->attach($applicationId);

        // Update application status
        $application = Application::find($applicationId);

        if ($application) {
            $application->status = 'interview_scheduled';
            $application->save();
        }

        // Update batch capacity
        $this->updateCapacityStatus();
    }
    
    public function updateCapacityStatus()
    {
        $currentCount = $this->applications()->count();

        if ($currentCount >= $this->batch_size) {
            $this->status = 'full';
        } else {
            $this->status = 'open';
        }

        $this->save();
    }

    public function isClosed(): bool
    {
        return $this->status === 'Closed';
    }

    public function isScheduled(): bool
    {
        return $this->status === 'Scheduled';
    }

    // public function cancelBatch(): void
    // {
    //     // Update batch status
    //     $this->update(['status' => 'Canceled']);

    //     // Update related applications
    //     foreach ($this->applications as $application) {
    //         $application->update([
    //             'status' => 'Applied' // or 'Canceled' if you want separate status
    //         ]);
    //     }
    // }

    public function presentCount(): int
    {
        return $this->applications()->where('attendance', 'Present')->count();
    }

    public function absentCount(): int
    {
        return $this->applications()->where('attendance', 'Absent')->count();
    }

}
