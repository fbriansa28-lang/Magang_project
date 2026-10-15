<?php

namespace App\Services;

use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\QuestionBank;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Penyusun SET SOAL TETAP per challenge:
 *
 * - Admin hanya menentukan jumlah soal per tingkat (mudah/sedang/sulit).
 * - Tiap tingkat dipecah otomatis: PG = ceil(n/2), Uraian = floor(n/2)
 *   (ganjil -> PG mendapat tambahan).
 * - Soal dipilih ACAK dari bank soal dengan tipe & tingkat yang sama,
 *   SEKALI saat challenge disimpan, lalu permanen untuk semua peserta.
 * - Jika pool tipe tersebut habis, slot diisi tipe sebaliknya di tingkat
 *   yang sama (fallback); jika keduanya habis, dipakai semua yang tersedia
 *   ("tetap") — set sama untuk semua peserta.
 * - Bobot mengikuti tipe intrinsik soal: PG 1/2/3, Uraian 3/5/8.
 * - Form admin DIBLOKIR bila total stok satu tingkat (PG + Uraian) kurang
 *   dari jumlah yang diminta.
 */
class ChallengeQuestionService
{
    /**
     * Split jumlah soal satu tingkat menjadi slot PG & Uraian.
     * Ganjil -> PG mendapat tambahan.
     *
     * @return array{pilihan_ganda: int, essay: int}
     */
    public static function splitFor(int $count): array
    {
        $pg = (int) ceil($count / 2);
        $essay = $count - $pg;

        return [
            'pilihan_ganda' => $pg,
            'essay' => $essay,
        ];
    }

    /**
     * Stok soal aktif per tingkat di kategori tertentu.
     *
     * @return array<string, array{pilihan_ganda: int, essay: int, total: int}>
     */
    public function stockSummary(int $categoryId): array
    {
        $summary = [];

        $difficulties = [
            'easy' => 'Mudah',
            'medium' => 'Sedang',
            'hard' => 'Sulit',
        ];

        foreach ($difficulties as $difficulty => $label) {
            $counts = QuestionBank::query()
                ->where('kategori_id', $categoryId)
                ->where('difficulty', $difficulty)
                ->where('is_active', true)
                ->selectRaw('question_type, COUNT(*) as total')
                ->groupBy('question_type')
                ->pluck('total', 'question_type');

            $pg = (int) ($counts['pilihan_ganda'] ?? 0);
            $essay = (int) ($counts['essay'] ?? 0);

            $summary[$difficulty] = [
                'label' => $label,
                'pilihan_ganda' => $pg,
                'essay' => $essay,
                'total' => $pg + $essay,
            ];
        }

        return $summary;
    }

    /**
     * Validasi jumlah diminta vs stok: tiap tingkat, total stok (PG + Uraian)
     * harus >= jumlah diminta. Mengembalikan daftar error per tingkat
     * (kosong = lolos). Dipakai untuk memblokir simpan form challenge.
     *
     * @param array{easy: int, medium: int, hard: int} $counts
     * @return array<string, string>
     */
    public function validateRequestedCounts(int $categoryId, array $counts): array
    {
        $stock = $this->stockSummary($categoryId);
        $errors = [];

        foreach ($counts as $difficulty => $requested) {
            $requested = (int) $requested;
            if ($requested <= 0) {
                continue;
            }

            $row = $stock[$difficulty] ?? ['label' => $difficulty, 'pilihan_ganda' => 0, 'essay' => 0, 'total' => 0];

            if ($row['total'] < $requested) {
                $errors[$difficulty] = sprintf(
                    'Stok tingkat %s hanya %d soal (PG %d + Uraian %d), sedangkan diminta %d. Tambah soal di Bank Soal atau kurangi jumlah.',
                    $row['label'],
                    $row['total'],
                    $row['pilihan_ganda'],
                    $row['essay'],
                    $requested
                );
            }
        }

        return $errors;
    }

    /**
     * Bangun & simpan set soal tetap challenge (dipanggil saat store/update).
     * Set lama dihapus, set baru dipilih acak sekali.
     *
     * @param array{easy: int, medium: int, hard: int} $counts
     */
    public function buildSet(Challenge $challenge, array $counts): void
    {
        $rows = $this->pickQuestions((int) $challenge->kategori_id, $counts);

        DB::transaction(function () use ($challenge, $rows) {
            $challenge->challengeQuestions()->delete();

            foreach ($rows as $index => $row) {
                ChallengeQuestion::create([
                    'challenge_id' => $challenge->id,
                    'question_id' => $row['question_id'],
                    'type' => $row['type'],
                    'difficulty' => $row['difficulty'],
                    'weight' => $row['weight'],
                    'order_no' => $index + 1,
                ]);
            }
        });
    }

    /**
     * Pilih soal untuk seluruh tingkat: acak dengan tipe & tingkat sama,
     * fallback ke tipe sebaliknya bila pool habis, dan pakai semua yang
     * tersedia ("tetap") bila keduanya habis.
     *
     * @param array{easy: int, medium: int, hard: int} $counts
     * @return array<int, array{question_id: int, type: string, difficulty: string, weight: float}>
     */
    public function pickQuestions(int $categoryId, array $counts): array
    {
        $picked = [];

        foreach ($counts as $difficulty => $requested) {
            $requested = (int) $requested;
            if ($requested <= 0) {
                continue;
            }

            $split = self::splitFor($requested);

            // Pool per tipe pada tingkat yang sama.
            $poolPg = $this->pool($categoryId, 'pilihan_ganda', $difficulty);
            $poolEssay = $this->pool($categoryId, 'essay', $difficulty);

            // Ambil acak sesuai split; jika pool kurang, ambil sebanyak tersedia.
            $pickedPg = $poolPg->shuffle()->take($split['pilihan_ganda']);
            $pickedEssay = $poolEssay->shuffle()->take($split['essay']);

            $chosenIds = $pickedPg->pluck('id')
                ->merge($pickedEssay->pluck('id'))
                ->all();

            // Fallback: kekurangan slot diisi tipe sebaliknya di tingkat yang sama
            // (acak dari sisa kedua pool). Jika keduanya habis -> "tetap" apa adanya.
            $shortfall = $requested - ($pickedPg->count() + $pickedEssay->count());

            if ($shortfall > 0) {
                $leftover = $poolPg->merge($poolEssay)
                    ->reject(fn (QuestionBank $question) => in_array($question->id, $chosenIds, true))
                    ->shuffle()
                    ->take($shortfall);

                foreach ($leftover as $question) {
                    $chosenIds[] = $question->id;

                    $picked[] = $this->rowFor($question);
                }
            }

            foreach ($pickedPg as $question) {
                $picked[] = $this->rowFor($question);
            }

            foreach ($pickedEssay as $question) {
                $picked[] = $this->rowFor($question);
            }
        }

        return $picked;
    }

    /**
     * Ringkasan set: jumlah soal & total bobot (skala penuh = 100%).
     *
     * @return array{total_questions: int, total_weight: float}
     */
    public function setSummary(Challenge $challenge): array
    {
        $questions = $challenge->challengeQuestions()->get();

        return [
            'total_questions' => $questions->count(),
            'total_weight' => round((float) $questions->sum(fn (ChallengeQuestion $q) => (float) $q->weight), 2),
        ];
    }

    /**
     * @return Collection<int, QuestionBank>
     */
    private function pool(int $categoryId, string $type, string $difficulty): Collection
    {
        return QuestionBank::query()
            ->where('kategori_id', $categoryId)
            ->where('question_type', $type)
            ->where('difficulty', $difficulty)
            ->where('is_active', true)
            ->get();
    }

    /**
     * @return array{question_id: int, type: string, difficulty: string, weight: float}
     */
    private function rowFor(QuestionBank $question): array
    {
        return [
            'question_id' => $question->id,
            'type' => $question->question_type,
            'difficulty' => $question->difficulty,
            'weight' => ScoringService::weightFor($question->question_type, $question->difficulty),
        ];
    }
}
