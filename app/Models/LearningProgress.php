<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LearningProgress extends Model
{
    protected $table = 'learning_progresses';

    protected $fillable = [
        'user_id',
        'learning_material_id',
        'status',
        'correct_count',
        'total_questions',
        'completed_at',
    ];

    protected $casts = [
        'correct_count'   => 'integer',
        'total_questions' => 'integer',
        'completed_at'    => 'datetime',
    ];

    public function submissions(): HasMany
    {
        return $this->hasMany(LearningQuestionSubmission::class, 'learning_progress_id');
    }
}
