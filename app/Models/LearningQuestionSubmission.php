<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LearningQuestionSubmission extends Model
{
    protected $fillable = [
        'learning_progress_id',
        'learning_question_id',
        'submitted_answer',
        'is_correct',
        'submitted_at',
    ];

    protected $casts = [
        'is_correct'    => 'boolean',
        'submitted_at'  => 'datetime',
    ];
}
