<?php

namespace App\Services;

use App\Models\Attempt;
use App\Models\AttemptQuestion;
use App\Models\Challenge;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Pengerjaan challenge (set soal tetap, sama untuk semua peserta):
 *
 * - start()        : kembalikan attempt yang ada, atau buat attempt baru
 *                    dengan menyalin set soal challenge (snapshot weight)
 *                    dan URUTAN TAMPIL diacak per peserta.
 * - submitAnswer() : nilai satu soal (per-soal seperti sistem lama):
 *                    PG sekali kirim langsung terkunci; uraian < 75% boleh
 *                    retry sampai benar/waktu habis.
 * - openHint()     : buka hint (sekali, penalti 2% konstanta sistem).
 * - finish()       : tutup attempt (SELESAI manual atau waktu habis),
 *                    hitung nilai akhir via ScoringService.
 *
 * Satu user = satu attempt per challenge (unique), tidak bisa mengulang.
 */
class AttemptService
{
    public function __construct(
        private readonly ScoringService $scoringService,
        private readonly SimilarityService $similarityService,
    ) {}

    /**
     * Mulai attempt. Jika sudah ada attempt (in_progress/completed) untuk
     * user + challenge ini, kembalikan yang ada (tidak mengulang).
     */
    public function start(Challenge $challenge, User $user): Attempt
    {
        return DB::transaction(function () use ($challenge, $user) {
            // Kunci baris challenge agar dua request start paralel tidak
            // membuat dua attempt sekaligus.
            $locked = Challenge::query()
                ->whereKey($challenge->id)
                ->lockForUpdate()
                ->firstOrFail();

            $existing = Attempt::query()
                ->where('user_id', $user->id)
                ->where('challenge_id', $locked->id)
                ->first();

            if ($existing) {
                return $existing;
            }

            $set = $locked->challengeQuestions()->get();

            if ($set->isEmpty()) {
                throw new RuntimeException('Challenge ini belum memiliki set soal. Hubungi admin.');
            }

            $attempt = Attempt::create([
                'challenge_id' => $locked->id,
                'user_id' => $user->id,
                'started_at' => now(),
                'total_score' => 0,
                'status' => Attempt::STATUS_IN_PROGRESS,
            ]);

            // Salin set ke attempt_questions dengan urutan tampil diacak
            // per peserta (soal tetap sama untuk semua).
            foreach ($set->shuffle()->values() as $index => $challengeQuestion) {
                AttemptQuestion::create([
                    'attempt_id' => $attempt->id,
                    'question_id' => $challengeQuestion->question_id,
                    'type' => $challengeQuestion->type,
                    'difficulty' => $challengeQuestion->difficulty,
                    'weight' => $challengeQuestion->weight,
                    'order_no' => $index + 1,
                    'status' => AttemptQuestion::STATUS_IN_PROGRESS,
                ]);
            }

            $this->logActivity($attempt, \App\Models\ActivityLog::ACTION_START, [
                'description' => $locked->title,
            ]);

            return $attempt->fresh(['attemptQuestions']);
        });
    }

    /**
     * Catat aktivitas peserta ke log sistem (dipakai halaman LOG SISTEM).
     */
    private function logActivity(Attempt $attempt, string $action, array $extra = []): void
    {
        try {
            \App\Models\ActivityLog::record($attempt, $action, $extra);
        } catch (\Throwable $e) {
            // Log tidak boleh menggagalkan proses pengerjaan peserta.
        }
    }

    /**
     * Nilai satu soal (per-soal submit):
     * - waktu habis     : status time_expired, skor 0, final.
     * - pilihan ganda   : sekali kirim langsung final (benar = bobot, salah = 0).
     * - uraian          : similarity >= 75% = final benar (bobot penuh);
     *                     < 75% = status wrong, belum final, boleh retry
     *                     (wrong_count bertambah).
     *
     * @return array{final: bool, is_correct: bool, status: string, score: float, similarity: ?float, wrong_count: int, attempt_score: float, time_expired: bool}
     */
    public function submitAnswer(AttemptQuestion $attemptQuestion, string $answer): array
    {
        $attemptQuestion->loadMissing(['attempt.challenge', 'question']);
        $attempt = $attemptQuestion->attempt;

        if (! $attempt?->isInProgress()) {
            throw new RuntimeException('Attempt ini sudah selesai.');
        }

        $weight = (float) $attemptQuestion->weight;
        $hintUsed = $attemptQuestion->hintUsed();

        // GATE WAKTU: waktu habis -> soal langsung final skor 0.
        if ($this->isTimeUp($attempt)) {
            $attemptQuestion->update([
                'answer' => $answer,
                'status' => AttemptQuestion::STATUS_TIME_EXPIRED,
            ]);

            $this->scoringService->recalculateAttempt($attempt);

            $this->logActivity($attempt, \App\Models\ActivityLog::ACTION_TIME_EXPIRED, [
                'description' => $attempt->challenge?->title,
            ]);

            return $this->resultPayload($attemptQuestion, final: true, correct: false, similarity: null, timeExpired: true);
        }

        // ===== Pilihan ganda: sekali kirim, langsung final =====
        if ($attemptQuestion->type === 'pilihan_ganda' || $attemptQuestion->type === AttemptQuestion::TYPE_PILIHAN_GANDA) {
            $attemptQuestion->update(['answer' => $answer]);

            $isCorrect = $this->scoringService->pilihanGandaBenar($attemptQuestion);

            $attemptQuestion->update([
                'status' => $isCorrect ? AttemptQuestion::STATUS_CORRECT : AttemptQuestion::STATUS_WRONG,
            ]);

            $this->scoringService->recalculateAttempt($attempt);

            $this->logActivity($attempt, \App\Models\ActivityLog::ACTION_SUBMIT_ANSWER, [
                'description' => 'SOAL_'.str_pad((string) $attemptQuestion->order_no, 2, '0', STR_PAD_LEFT),
            ]);

            return $this->resultPayload($attemptQuestion, final: true, correct: $isCorrect, similarity: null, timeExpired: false);
        }

        // ===== Uraian: similarity >= 75% benar; < 75% boleh retry =====
        $similarity = $this->similarityService->hitungSimilarity($answer, (string) ($attemptQuestion->question?->flag ?? ''));

        if ($this->scoringService->similarityCukup($similarity)) {
            $attemptQuestion->update([
                'answer' => $answer,
                'status' => AttemptQuestion::STATUS_CORRECT,
            ]);

            $this->scoringService->recalculateAttempt($attempt);

            $this->logActivity($attempt, \App\Models\ActivityLog::ACTION_SUBMIT_ANSWER, [
                'description' => 'SOAL_'.str_pad((string) $attemptQuestion->order_no, 2, '0', STR_PAD_LEFT),
            ]);

            return $this->resultPayload($attemptQuestion, final: true, correct: true, similarity: $similarity, timeExpired: false);
        }

        // Belum tepat: simpan jawaban + tambah percobaan salah, belum final.
        $attemptQuestion->update([
            'answer' => $answer,
            'status' => AttemptQuestion::STATUS_WRONG,
            'wrong_count' => $attemptQuestion->wrong_count + 1,
        ]);

        $this->scoringService->recalculateAttempt($attempt);

        return $this->resultPayload($attemptQuestion, final: false, correct: false, similarity: $similarity, timeExpired: false);
    }

    /**
     * Buka hint satu soal: hint_used_at diisi sekali (membuka berulang tidak
     * menambah penalti). Penalti 2% dihitung ScoringService saat penilaian.
     */
    public function openHint(AttemptQuestion $attemptQuestion): string
    {
        return DB::transaction(function () use ($attemptQuestion) {
            $locked = AttemptQuestion::query()
                ->whereKey($attemptQuestion->id)
                ->lockForUpdate()
                ->firstOrFail();

            $locked->loadMissing(['attempt', 'question']);

            if (! $locked->attempt?->isInProgress()) {
                throw new RuntimeException('Hint tidak dapat dibuka setelah attempt selesai.');
            }

            $hint = (string) ($locked->question?->hint ?? '');
            if ($hint === '') {
                throw new RuntimeException('Soal ini tidak memiliki hint.');
            }

            if ($locked->hint_used_at === null) {
                $locked->update(['hint_used_at' => now()]);
            }

            return $hint;
        });
    }

    /**
     * Tutup attempt (manual atau waktu habis), hitung nilai akhir.
     */
    public function finish(Attempt $attempt, bool $timeExpired = false): Attempt
    {
        return DB::transaction(function () use ($attempt, $timeExpired) {
            $locked = Attempt::query()
                ->whereKey($attempt->id)
                ->lockForUpdate()
                ->firstOrFail();

            if (! $locked->isInProgress()) {
                return $locked;
            }

            if ($timeExpired) {
                // Semua soal yang BELUM final -> time_expired (skor 0).
                // Termasuk uraian berstatus wrong yang belum benar (masih bisa
                // retry selama waktu berjalan) — saat waktu habis harus ditutup.
                $locked->attemptQuestions()
                    ->whereNotIn('status', [
                        AttemptQuestion::STATUS_CORRECT,
                        AttemptQuestion::STATUS_TIME_EXPIRED,
                    ])
                    ->where(function ($query) {
                        // PG salah tetap final (sekali jawab) -> jangan diubah.
                        $query->where('type', '!=', 'pilihan_ganda')
                            ->orWhere('status', AttemptQuestion::STATUS_IN_PROGRESS);
                    })
                    ->update(['status' => AttemptQuestion::STATUS_TIME_EXPIRED]);
            }

            $this->scoringService->recalculateAttempt($locked);

            $locked->update([
                'status' => Attempt::STATUS_COMPLETED,
                'finished_at' => now(),
            ]);

            $this->logActivity(
                $locked,
                $timeExpired ? \App\Models\ActivityLog::ACTION_TIME_EXPIRED : \App\Models\ActivityLog::ACTION_FINISH,
                ['description' => $locked->challenge?->title]
            );

            DB::afterCommit(fn () => app(RecommendationService::class)->refreshForChallenge(
                $locked->user_id,
                $locked->challenge_id
            ));

            return $locked->refresh();
        });
    }

    /**
     * Sisa waktu pengerjaan (detik). 0 bila habis / durasi tidak diatur.
     */
    public function remainingSeconds(Attempt $attempt): int
    {
        $durationMinutes = (int) ($attempt->challenge?->duration_minutes ?? 0);

        if ($durationMinutes <= 0) {
            return 0;
        }

        $elapsed = $attempt->started_at?->diffInSeconds(now()) ?? 0;

        return (int) max(0, ($durationMinutes * 60) - $elapsed);
    }

    /**
     * True bila waktu pengerjaan sudah habis (durasi > 0).
     */
    public function isTimeUp(Attempt $attempt): bool
    {
        $durationMinutes = (int) ($attempt->challenge?->duration_minutes ?? 0);

        if ($durationMinutes <= 0) {
            return false;
        }

        return $this->remainingSeconds($attempt) <= 0;
    }

    /**
     * Payload daftar soal untuk halaman kerja peserta.
     * ATURAN: teks hint TIDAK dikirim sebelum peserta membukanya.
     *
     * @return array<int, array<string, mixed>>
     */
    public function questionsPayload(Attempt $attempt): array
    {
        $attempt->loadMissing(['attemptQuestions.question']);

        return $attempt->attemptQuestions->map(function (AttemptQuestion $attemptQuestion) {
            $question = $attemptQuestion->question;

            $payload = [
                'id' => $attemptQuestion->id,
                'order_no' => $attemptQuestion->order_no,
                'type' => $attemptQuestion->type,
                'difficulty' => $attemptQuestion->difficulty,
                'weight' => (float) $attemptQuestion->weight,
                'content' => $question?->content,
                'image_url' => $question?->image_url,
                'options' => ($attemptQuestion->type === 'pilihan_ganda' || $attemptQuestion->type === AttemptQuestion::TYPE_PILIHAN_GANDA)
                    ? $question?->options()
                    : null,
                'has_hint' => filled($question?->hint),
                'hint_opened' => $attemptQuestion->hintUsed(),
                'hint_penalty_percent' => round(ScoringService::HINT_PENALTY * 100, 2),
                'answer' => $attemptQuestion->answer,
                'status' => $attemptQuestion->status,
                'wrong_count' => $attemptQuestion->wrong_count,
                'score' => (float) ($attemptQuestion->score ?? 0),
                'final' => $attemptQuestion->isFinalized(),
            ];

            if ($attemptQuestion->hintUsed()) {
                $payload['hint'] = $question?->hint;
            }

            return $payload;
        })->values()->all();
    }

    /**
     * @return array{final: bool, is_correct: bool, status: string, score: float, similarity: ?float, wrong_count: int, attempt_score: float, time_expired: bool}
     */
    private function resultPayload(AttemptQuestion $attemptQuestion, bool $final, bool $correct, ?float $similarity, bool $timeExpired): array
    {
        $fresh = $attemptQuestion->refresh();

        return [
            'final' => $final,
            'is_correct' => $correct,
            'status' => $fresh->status,
            'score' => (float) ($fresh->score ?? 0),
            'similarity' => $similarity,
            'wrong_count' => $fresh->wrong_count,
            'attempt_score' => (float) ($fresh->attempt?->total_score ?? 0),
            'time_expired' => $timeExpired,
        ];
    }
}
