<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CategoryScore extends Model
{
    protected $table = 'category_scores';

    protected $fillable = [
        'user_id',
        'kategori_id',
        'total_score',
        'attempts_count',
        'average_score',
        'status',
    ];

    protected $casts = [
        'total_score' => 'integer',
        'attempts_count' => 'integer',
        'average_score' => 'integer',
    ];

    const STATUS_NORMAL = 'normal';
    const STATUS_LEMAH = 'lemah';
    const DEFAULT_AMBANG_BATAS = 60;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'kategori_id');
    }
}
