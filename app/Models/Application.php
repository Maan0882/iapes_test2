<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Application extends Model
{
    use HasFactory; //

   protected $fillable = [
        'name', 'email', 'phone', 'college', 'degree',
        'last_exam_appeared', 'cgpa', 'domain', 'skills', 
        'resume_path', 'status', 'interview_batch_id',
    ];

    /**
     * Relationship: Application belongs to Interview Batch
     */
    public function interviewBatches()
    {
        return $this->belongsToMany(
            \App\Models\InterviewBatch::class,
            'interview_batch_intern'
        )->withPivot('is_present')
        ->withTimestamps();
    }

    public function attendance()
    {
        return $this->hasOne(Attendance::class);
    }

    public function evaluation()
    {
        return $this->hasOne(InterviewEvaluation::class);
    }

    // protected static function booted()
    // {
    //     static::updated(function ($application) {

    //         if ($application->isDirty('status') &&
    //             $application->status === 'interview_scheduled') {

    //             // You can log or trigger notification here
    //         }
    //     });
    // }

    /**
     * Optional: Casts
     */
    protected $casts = [
        'cgpa' => 'float',
    ];
}
