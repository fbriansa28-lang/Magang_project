<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Satu soal dalam set tetap challenge. Dipilih sekali saat challenge
 * disimpan dan dipakai sama untuk semua peserta (urutan tampil diacak
 * per peserta di attempt).
 */
class ChallengeQuestion extends Model
{
    protected $fillable = [
        'challenge_id',
        'question_id',
        'type',
        'difficulty',
        'weight',
        'order_no',
    ];

    protected $casts = [
        'weight' => 'decimal:2',
        'order_no' => 'integer',
    ];

    public function question(): BelongsTo
    {
        return $this->belongsTo(QuestionBank::class, 'question_id');
    }
}
