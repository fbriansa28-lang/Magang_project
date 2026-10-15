<?php

namespace Tests\Feature;

use App\Models\Attempt;
use App\Models\AttemptQuestion;
use App\Models\QuestionBank;
use App\Models\User;
use App\Services\ScoringService;
use App\Services\SimilarityService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Concerns\CreatesChallenges;
use Tests\TestCase;

/**
 * Test integrasi penilaian attempt (raw_score, score, nilai akhir skala 100)
 * + SimilarityService.
 */
class ScoringServiceAttemptTest extends TestCase
{
    use RefreshDatabase;
    use CreatesChallenges;

    private function service(): ScoringService
    {
        return app(ScoringService::class);
    }

    private function makeAttempt(): Attempt
    {
        $category = $this->makeCategory();
        $challenge = $this->makeChallenge($category);

        return Attempt::create([
            'challenge_id' => $challenge->id,
            'user_id' => User::factory()->create()->id,
            'started_at' => now(),
            'status' => Attempt::STATUS_IN_PROGRESS,
        ]);
    }

    private function addAttemptQuestion(Attempt $attempt, array $overrides = []): AttemptQuestion
    {
        $type = $overrides['type'] ?? 'pilihan_ganda';
        $difficulty = $overrides['difficulty'] ?? 'easy';

        $factory = QuestionBank::factory();
        $question = ($type === 'pilihan_ganda' ? $factory->pilihanGanda() : $factory->essay())
            ->state(['difficulty' => $difficulty])
            ->create();

        return AttemptQuestion::create(array_merge([
            'attempt_id' => $attempt->id,
            'question_id' => $question->id,
            'type' => $type,
            'difficulty' => $difficulty,
            'weight' => ScoringService::weightFor($type, $difficulty),
            'order_no' => $attempt->attemptQuestions()->count() + 1,
            'status' => AttemptQuestion::STATUS_IN_PROGRESS,
        ], $overrides));
    }

    public function test_similarity_service_jawaban_sama_persis_bernilai_satu(): void
    {
        $similarity = app(SimilarityService::class);

        $this->assertEqualsWithDelta(1.0, $similarity->hitungSimilarity('FLAG{rahasia}', 'FLAG{rahasia}'), 0.0001);
        $this->assertEquals(0.0, $similarity->hitungSimilarity('apa saja', ''));
    }

    public function test_pilgan_benar_dan_salah(): void
    {
        $attempt = $this->makeAttempt();

        $benar = $this->addAttemptQuestion($attempt, ['answer' => 'a', 'status' => AttemptQuestion::STATUS_CORRECT]);
        $salah = $this->addAttemptQuestion($attempt, ['answer' => 'b', 'status' => AttemptQuestion::STATUS_WRONG]);

        $this->service()->recalculateAttempt($attempt);

        $this->assertEquals(1.0, (float) $benar->fresh()->raw_score);
        $this->assertEquals(1.0, (float) $benar->fresh()->score);

        $this->assertEquals(0.0, (float) $salah->fresh()->raw_score);
        $this->assertEquals(0.0, (float) $salah->fresh()->score);
    }

    public function test_pilgan_benar_dengan_hint_dipotong_2_persen(): void
    {
        $attempt = $this->makeAttempt();

        // PG sedang: bobot 2, hint -> 1.96
        $question = $this->addAttemptQuestion($attempt, [
            'difficulty' => 'medium',
            'weight' => 2,
            'answer' => 'a',
            'status' => AttemptQuestion::STATUS_CORRECT,
            'hint_used_at' => now(),
        ]);

        $this->service()->recalculateAttempt($attempt);

        $this->assertEqualsWithDelta(2.0, (float) $question->fresh()->raw_score, 0.0001);
        $this->assertEqualsWithDelta(1.96, (float) $question->fresh()->score, 0.0001);
    }

    public function test_pilgan_salah_dengan_hint_tetap_nol(): void
    {
        $attempt = $this->makeAttempt();

        $question = $this->addAttemptQuestion($attempt, [
            'answer' => 'b',
            'status' => AttemptQuestion::STATUS_WRONG,
            'hint_used_at' => now(),
        ]);

        $this->service()->recalculateAttempt($attempt);

        $this->assertEquals(0.0, (float) $question->fresh()->score);
    }

    public function test_uraian_correct_mendapat_bobot_penuh(): void
    {
        $attempt = $this->makeAttempt();

        // Uraian sedang: bobot 5
        $question = $this->addAttemptQuestion($attempt, [
            'type' => 'essay',
            'difficulty' => 'medium',
            'weight' => 5,
            'answer' => 'jawaban benar',
            'status' => AttemptQuestion::STATUS_CORRECT,
        ]);

        $this->service()->recalculateAttempt($attempt);

        $this->assertEqualsWithDelta(5.0, (float) $question->fresh()->raw_score, 0.0001);
        $this->assertEqualsWithDelta(5.0, (float) $question->fresh()->score, 0.0001);
    }

    public function test_uraian_salah_dan_retry_menjadi_correct_dinilai_penuh(): void
    {
        $attempt = $this->makeAttempt();

        $question = $this->addAttemptQuestion($attempt, [
            'type' => 'essay',
            'difficulty' => 'hard',
            'weight' => 8,
            'answer' => 'jawaban ngasal',
            'status' => AttemptQuestion::STATUS_WRONG,
            'wrong_count' => 1,
        ]);

        $this->service()->recalculateAttempt($attempt);
        $this->assertEquals(0.0, (float) $question->fresh()->score);

        // retry berhasil -> bobot penuh (bukan flat 50%)
        $question->update(['status' => AttemptQuestion::STATUS_CORRECT, 'answer' => 'jawaban benar']);
        $this->service()->recalculateAttempt($attempt);

        $this->assertEqualsWithDelta(8.0, (float) $question->fresh()->score, 0.0001);
    }

    public function test_uraian_correct_dengan_hint_dipotong_2_persen(): void
    {
        $attempt = $this->makeAttempt();

        // Uraian sulit: bobot 8, hint -> 7.84
        $question = $this->addAttemptQuestion($attempt, [
            'type' => 'essay',
            'difficulty' => 'hard',
            'weight' => 8,
            'answer' => 'jawaban benar',
            'status' => AttemptQuestion::STATUS_CORRECT,
            'hint_used_at' => now(),
        ]);

        $this->service()->recalculateAttempt($attempt);

        $this->assertEqualsWithDelta(8.0, (float) $question->fresh()->raw_score, 0.0001);
        $this->assertEqualsWithDelta(7.84, (float) $question->fresh()->score, 0.0001);
    }

    public function test_soal_waktu_habis_skor_nol(): void
    {
        $attempt = $this->makeAttempt();

        $question = $this->addAttemptQuestion($attempt, [
            'type' => 'essay',
            'difficulty' => 'easy',
            'weight' => 3,
            'answer' => 'jawaban benar',
            'status' => AttemptQuestion::STATUS_TIME_EXPIRED,
        ]);

        $this->service()->recalculateAttempt($attempt);

        $this->assertEquals(0.0, (float) $question->fresh()->score);
    }

    public function test_nilai_akhir_skala_100(): void
    {
        $attempt = $this->makeAttempt();

        // PG mudah benar (1) + PG sedang salah (0) + uraian sulit benar (8)
        // total bobot = 1 + 2 + 8 = 11; skor = 1 + 0 + 8 = 9 -> 81.82
        $this->addAttemptQuestion($attempt, ['answer' => 'a', 'status' => AttemptQuestion::STATUS_CORRECT]);
        $this->addAttemptQuestion($attempt, ['difficulty' => 'medium', 'weight' => 2, 'answer' => 'b', 'status' => AttemptQuestion::STATUS_WRONG]);
        $this->addAttemptQuestion($attempt, [
            'type' => 'essay',
            'difficulty' => 'hard',
            'weight' => 8,
            'answer' => 'benar',
            'status' => AttemptQuestion::STATUS_CORRECT,
        ]);

        $attempt = $this->service()->recalculateAttempt($attempt);

        $this->assertEqualsWithDelta(81.82, (float) $attempt->total_score, 0.01);
    }

    public function test_sum_weight_peserta_hint_sama_dengan_tanpa_hint(): void
    {
        $attemptA = $this->makeAttempt();
        $attemptB = $this->makeAttempt();

        $this->addAttemptQuestion($attemptA, ['difficulty' => 'medium', 'weight' => 2, 'answer' => 'a', 'status' => AttemptQuestion::STATUS_CORRECT]);
        $this->addAttemptQuestion($attemptB, ['difficulty' => 'medium', 'weight' => 2, 'answer' => 'a', 'status' => AttemptQuestion::STATUS_CORRECT, 'hint_used_at' => now()]);

        $attemptA = $this->service()->recalculateAttempt($attemptA);
        $attemptB = $this->service()->recalculateAttempt($attemptB);

        $weightA = (float) $attemptA->attemptQuestions()->sum('weight');
        $weightB = (float) $attemptB->attemptQuestions()->sum('weight');

        $this->assertEquals($weightA, $weightB, 'SUM(weight) harus sama walau hint dipakai');

        // A = 100; B = 1.96/2 x 100 = 98
        $this->assertEqualsWithDelta(100.0, (float) $attemptA->total_score, 0.0001);
        $this->assertEqualsWithDelta(98.0, (float) $attemptB->total_score, 0.0001);
    }
}
