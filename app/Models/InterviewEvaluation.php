<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InterviewEvaluation extends Model
{
    protected $fillable = [
        'application_id',
        'interview_batch_id',
        'problem_solving',
        'aptitude',
        'total',
        'remarks',
        'is_selected'
    ];

    protected static function booted()
    {
        static::saving(function ($model) {
            $model->total =
                (int) $model->problem_solving +
                (int) $model->aptitude +
                (int) $model->communication;
        });
    }

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function batch()
    {
        return $this->belongsTo(InterviewBatch::class, 'interview_batch_id');
    }

    public function interviewBatch()
    {
        return $this->belongsTo(InterviewBatch::class)->withDefault([
            'batch_size' => 0,  // Prevents overflow checks from failing
        ]);
    }

    public function scopeSelected($query)
    {
        return $query->where('is_selected', true);
    }

    public static function getRankedByBatch($batchId)
    {
        return self::with('application')
            ->where('interview_batch_id', $batchId)
            ->orderByDesc('total')
            ->get()
            ->values()
            ->map(function ($item, $index) {
                $item->rank = $index + 1;
                return $item;
            });
    }

    public static function selectTopByBatch($batchId, $limit)
    {
        $topCandidates = self::where('interview_batch_id', $batchId)
            ->orderByDesc('total')
            ->take($limit)
            ->get();

        foreach ($topCandidates as $candidate) {
            $candidate->update(['is_selected' => true]);

            if ($candidate->application) {
                $candidate->application->update([
                    'status' => 'selected'
                ]);
            }
        }

        return $topCandidates;
    }

    public function getAiSuggestionAttribute()
    {
        return match (true) {
            $this->total >= 45 => 'Highly Recommended',
            $this->total >= 30 => 'Recommended',
            default => 'Not Recommended',
        };
    }

    public function selectIntern()
    {
        $this->update([
            'is_selected' => true
        ]);

        if ($this->application) {
            $this->application->update([
                'status' => 'selected'
            ]);
        }

        // Send Email
        \Mail::to($this->application->email)
            ->queue(new \App\Mail\InternSelectedMail($this->application));
    }

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Intern Status',
                    'data' => [
                        InterviewEvaluation::where('is_selected', true)->count(),
                        InterviewEvaluation::where('is_rejected', true)->count(),
                    ],
                ],
            ],
            'labels' => ['Selected', 'Rejected'],
        ];
    }

}

