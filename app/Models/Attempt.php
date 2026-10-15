<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Satu attempt peserta pada satu challenge (satu attempt per user).
 * total_score = nilai akhir skala 0-100.
 */
class Attempt extends Model
{
    protected $fillable = [
        'challenge_id',
        'user_id',
        'started_at',
        'finished_at',
        'total_score',
        'status',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'total_score' => 'decimal:2',
    ];

    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';

    public function challenge(): BelongsTo
    {
        return $this->belongsTo(Challenge::class, 'challenge_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function attemptQuestions(): HasMany
    {
        return $this->hasMany(AttemptQuestion::class, 'attempt_id')->orderBy('order_no');
    }

    public function isInProgress(): bool
    {
        return $this->status === self::STATUS_IN_PROGRESS;
    }

    /**
     * Attempt yang sudah selesai dikerjakan — dipakai leaderboard, rapot,
     * dan statistik.
     */
    public function scopeFinished($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }
}
