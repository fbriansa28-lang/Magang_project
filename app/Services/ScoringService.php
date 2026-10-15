<?php

namespace App\Services;

use App\Models\Attempt;
use App\Models\AttemptQuestion;

/**
 * Penilaian sistem challenge (set soal tetap, bobot otomatis):
 *
 * - Pilihan ganda : benar = bobot, salah = 0 (sekali jawab, terkunci).
 * - Uraian        : similarity >= 75% = bobot penuh, < 75% = 0 (boleh retry).
 * - Hint (semua)  : potongan 2% dari bobot, hanya bila akhirnya benar.
 * - Waktu habis   : skor soal = 0.
 *
 * Nilai akhir attempt = SUM(skor) / SUM(bobot soal) x 100, sehingga total
 * bobot soal selalu menjadi 100%. Pembulatan 2 desimal hanya pada hasil
 * akhir/tampilan, bukan di tahap tengah.
 */
class ScoringService
{
    /** Potongan bobot saat hint dibuka (2%, konstanta sistem). */
    const HINT_PENALTY = 0.02;

    /** Ambang similarity uraian dianggap benar (75%). */
    const T_MIN = 0.75;

    /**
     * Tabel bobot per tipe x tingkat.
     */
    const WEIGHTS = [
        'pilihan_ganda' => ['easy' => 1, 'medium' => 2, 'hard' => 3],
        'essay'         => ['easy' => 3, 'medium' => 5, 'hard' => 8],
    ];

    /**
     * Bobot soal berdasarkan tipe x tingkat.
     */
    public static function weightFor(string $type, string $difficulty): float
    {
        $typeValue = (string) $type;
        $difficultyValue = (string) $difficulty;

        return (float) (self::WEIGHTS[$typeValue][$difficultyValue] ?? 0);
    }

    /**
     * Skor murni pilihan ganda: benar = bobot, salah = 0.
     */
    public function rawScorePilihanGanda(bool $isCorrect, float $weight): float
    {
        return $isCorrect ? $weight : 0.0;
    }

    /**
     * Skor murni uraian: similarity >= 75% = bobot penuh, selain itu 0.
     */
    public function rawScoreUraian(float $weight, float $similarity): float
    {
        return $similarity >= self::T_MIN ? $weight : 0.0;
    }

    /**
     * True bila similarity uraian mencapai ambang benar (75%).
     */
    public function similarityCukup(float $similarity): bool
    {
        return $similarity >= self::T_MIN;
    }

    /**
     * Penalti hint: potongan 2% dari bobot.
     * Hanya berlaku bila hint dipakai DAN skor soal > 0 (jawaban benar).
     */
    public function hintPenalty(float $weight, bool $hintUsed, float $rawScore): float
    {
        if (! $hintUsed || $rawScore <= 0.0) {
            return 0.0;
        }

        return $weight * self::HINT_PENALTY;
    }

    /**
     * Skor akhir satu soal: max(0, skor murni - penalti hint).
     * Tidak dibulatkan di tahap ini.
     */
    public function finalScoreSoal(float $rawScore, float $penalty): float
    {
        return max(0.0, $rawScore - $penalty);
    }

    /**
     * Bandingkan jawaban pilihan ganda dengan kunci correct_option.
     */
    public function pilihanGandaBenar(AttemptQuestion $attemptQuestion): bool
    {
        $answer = $attemptQuestion->answer;
        $key = $attemptQuestion->question?->correct_option;

        if ($answer === null || $key === null) {
            return false;
        }

        return strtolower(trim((string) $answer)) === strtolower(trim((string) $key));
    }

    /**
     * Nilai akhir attempt skala 0-100:
     * (SUM(skor) / SUM(bobot)) x 100. Total bobot = 100%.
     *
     * @param iterable<AttemptQuestion> $attemptQuestions
     * @return array{score: float, total_weight: float, total_raw: float}
     */
    public function nilaiAkhirAttempt(iterable $attemptQuestions): array
    {
        $totalScore = 0.0;
        $totalWeight = 0.0;
        $totalRaw = 0.0;

        foreach ($attemptQuestions as $attemptQuestion) {
            $totalWeight += (float) $attemptQuestion->weight;
            $totalScore += (float) ($attemptQuestion->score ?? 0);
            $totalRaw += (float) ($attemptQuestion->raw_score ?? 0);
        }

        return [
            'score' => $totalWeight > 0 ? ($totalScore / $totalWeight) * 100 : 0.0,
            'total_weight' => $totalWeight,
            'total_raw' => $totalRaw,
        ];
    }

    /**
     * Hitung ulang raw_score/score seluruh soal attempt dari state terkini
     * (jawaban, status, hint), lalu simpan total_score (dibulatkan 2 desimal).
     *
     * Aturan per soal:
     * - waktu habis          : skor 0.
     * - pilihan ganda        : benar = bobot, salah = 0.
     * - uraian status correct: bobot penuh; selain itu 0 (masih bisa retry).
     * - penalti hint diterapkan bila hint dipakai dan soal benar.
     */
    public function recalculateAttempt(Attempt $attempt): Attempt
    {
        // refresh() memuat ulang atribut + relasi yang sudah ter-load,
        // supaya perubahan terbaru (jawaban/hint/status) selalu ikut dihitung.
        $attempt->refresh();
        $attempt->loadMissing(['attemptQuestions.question']);

        foreach ($attempt->attemptQuestions as $attemptQuestion) {
            $weight = (float) $attemptQuestion->weight;
            $hintUsed = $attemptQuestion->hintUsed();

            if ($attemptQuestion->status === AttemptQuestion::STATUS_TIME_EXPIRED) {
                $raw = 0.0;
            } elseif ($attemptQuestion->type === 'pilihan_ganda' || $attemptQuestion->type === AttemptQuestion::TYPE_PILIHAN_GANDA) {
                $raw = $this->rawScorePilihanGanda($this->pilihanGandaBenar($attemptQuestion), $weight);
            } else {
                $raw = $attemptQuestion->status === AttemptQuestion::STATUS_CORRECT ? $weight : 0.0;
            }

            $penalty = $this->hintPenalty($weight, $hintUsed, $raw);

            $attemptQuestion->updateQuietly([
                'raw_score' => $raw,
                'score' => $this->finalScoreSoal($raw, $penalty),
            ]);
        }

        $attempt->refresh()->load('attemptQuestions');

        $nilai = $this->nilaiAkhirAttempt($attempt->attemptQuestions);

        $attempt->update([
            'total_score' => round($nilai['score'], 2),
        ]);

        return $attempt->refresh();
    }
}
