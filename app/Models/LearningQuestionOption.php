<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LearningQuestionOption extends Model
{
    protected $fillable = [
        'learning_question_id',
        'option_key',
        'option_text',
    ];
}
