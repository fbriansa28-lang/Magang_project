<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Snapshot satu soal di dalam attempt: weight disalin dari set challenge
 * saat attempt dibuat sehingga tidak berubah walau challenge diedit.
 * raw_score & score disimpan untuk audit.
 */
class AttemptQuestion extends Model
{
    const TYPE_PILIHAN_GANDA = 'pilihan_ganda';
    const TYPE_URAIAN = 'essay';

    protected $fillable = [
        'attempt_id',
        'question_id',
        'type',
        'difficulty',
        'weight',
        'order_no',
        'answer',
        'status',
        'wrong_count',
        'hint_used_at',
        'raw_score',
        'score',
    ];

    protected $casts = [
        'weight' => 'decimal:2',
        'order_no' => 'integer',
        'wrong_count' => 'integer',
        'hint_used_at' => 'datetime',
        'raw_score' => 'decimal:4',
        'score' => 'decimal:4',
    ];

    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_CORRECT = 'correct';
    const STATUS_WRONG = 'wrong';
    const STATUS_TIME_EXPIRED = 'time_expired';

    public function attempt(): BelongsTo
    {
        return $this->belongsTo(Attempt::class, 'attempt_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(QuestionBank::class, 'question_id');
    }

    public function hintUsed(): bool
    {
        return $this->hint_used_at !== null;
    }

    /**
     * Soal sudah final (tidak bisa dikirim ulang):
     * - pilihan ganda   : sekali kirim langsung final (benar/salah).
     * - uraian benar    : final (bobot penuh).
     * - waktu habis     : final (skor 0).
     * - uraian SALAH    : BELUM final — masih boleh retry sampai benar.
     */
    public function isFinalized(): bool
    {
        if ($this->status === self::STATUS_IN_PROGRESS) {
            return false;
        }

        // Uraian dengan status wrong masih bisa dicoba lagi.
        if (($this->type === self::TYPE_URAIAN || $this->type === 'essay') && $this->status === self::STATUS_WRONG) {
            return false;
        }

        return true;
    }
}
