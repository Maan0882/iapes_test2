<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InterviewEvaluation extends Model
{
    protected $fillable = [
        'application_id',
        'problem_solving',
        'aptitude',
        'total',
        'remarks'
    ];

    public function application()
    {
        return $this->belongsTo(Application::class);
    }
}
