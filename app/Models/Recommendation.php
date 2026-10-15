<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Recommendation extends Model
{
    protected $table = 'recommendations';

    protected $fillable = [
        'user_id', 'kategori_id', 'learning_material_id', 'challenge_id', 'question_id',
        'average_score', 'threshold', 'status', 'metadata',
    ];

    protected $casts = ['average_score' => 'integer', 'threshold' => 'integer', 'metadata' => 'array'];

    public function category(): BelongsTo { return $this->belongsTo(Category::class, 'kategori_id'); }
    public function material(): BelongsTo { return $this->belongsTo(Materi::class, 'learning_material_id'); }
    public function challenge(): BelongsTo { return $this->belongsTo(Challenge::class, 'challenge_id'); }
}
