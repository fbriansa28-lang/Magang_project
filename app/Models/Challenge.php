<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Challenge extends Model
{
    use HasFactory;

    protected $table = 'challenges';

    protected $fillable = [
        'kategori_id',
        'title',
        'description',
        'duration_minutes',
        'questions_easy',
        'questions_medium',
        'questions_hard',
        'is_active',
        'starts_at',
        'ends_at',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'starts_at' => 'datetime',
        'ends_at'   => 'datetime',
        'questions_easy' => 'integer',
        'questions_medium' => 'integer',
        'questions_hard' => 'integer',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'kategori_id');
    }

    /**
     * Attempt peserta pada challenge ini (satu attempt per user).
     */
    public function attempts()
    {
        return $this->hasMany(Attempt::class, 'challenge_id');
    }

    /**
     * Set soal tetap challenge (dipilih sekali saat save, sama untuk semua peserta).
     */
    public function challengeQuestions()
    {
        return $this->hasMany(ChallengeQuestion::class, 'challenge_id')->orderBy('order_no');
    }

    public function isUsed(): bool
    {
        return $this->attempts()->exists();
    }

    public function scopeActiveByTime($query)
    {
        $now = now();
        return $query->where('is_active', true)
            ->where(function($q) use ($now) {
                $q->whereNull('starts_at')
                  ->orWhere('starts_at', '<=', $now);
            })
            ->where(function($q) use ($now) {
                $q->whereNull('ends_at')
                  ->orWhere('ends_at', '>=', $now);
            });
    }

    public function isActiveNow(): bool
    {
        $now = now();
        $isActive = $this->is_active;
        $hasStarted = is_null($this->starts_at) || $this->starts_at->lte($now);
        $notEnded = is_null($this->ends_at) || $this->ends_at->gte($now);
        
        return $isActive && $hasStarted && $notEnded;
    }
}
