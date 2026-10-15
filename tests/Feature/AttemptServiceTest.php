<?php

namespace Tests\Feature;

use App\Models\Attempt;
use App\Models\AttemptQuestion;
use App\Models\User;
use App\Services\AttemptService;
use App\Services\ChallengeQuestionService;
use App\Services\ScoringService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Concerns\CreatesChallenges;
use Tests\TestCase;

/**
 * Test pengerjaan attempt (set tetap): start, submit per-soal (PG lock,
 * uraian retry), hint, finish, timer.
 */
class AttemptServiceTest extends TestCase
{
    use RefreshDatabase;
    use CreatesChallenges;

    private function attemptService(): AttemptService
    {
        return app(AttemptService::class);
    }

    private function buildChallengeSet(int $perCombo = 8): array
    {
        $category = $this->makeCategory();
        $challenge = $this->makeChallenge($category);

        $this->seedStockAllCombos($category->id, $perCombo, withHint: true);

        // mudah=4, sedang=3, sulit=2
        app(ChallengeQuestionService::class)->buildSet($challenge, [
            'easy' => 4,
            'medium' => 3,
            'hard' => 2,
        ]);

        return [$challenge, $category];
    }

    public function test_start_menyalin_set_ke_attempt(): void
    {
        [$challenge] = $this->buildChallengeSet();
        $user = User::factory()->create();

        $attempt = $this->attemptService()->start($challenge, $user);

        $this->assertEquals(Attempt::STATUS_IN_PROGRESS, $attempt->status);
        $this->assertEquals($challenge->challengeQuestions()->count(), $attempt->attemptQuestions()->count());
    }

    public function test_start_dua_kali_mengembalikan_attempt_yang_sama(): void
    {
        [$challenge] = $this->buildChallengeSet();
        $user = User::factory()->create();

        $first = $this->attemptService()->start($challenge, $user);
        $second = $this->attemptService()->start($challenge, $user);

        $this->assertEquals($first->id, $second->id);
        $this->assertDatabaseCount('attempts', 1);
    }

    public function test_snapshot_bobot_tidak_berubah_saat_set_disusun_ulang(): void
    {
        [$challenge] = $this->buildChallengeSet();
        $user = User::factory()->create();

        $attempt = $this->attemptService()->start($challenge, $user);
        $weightBefore = (float) $attempt->attemptQuestions()->sum('weight');

        // susun ulang set challenge (edit)
        app(ChallengeQuestionService::class)->buildSet($challenge, ['easy' => 2, 'medium' => 0, 'hard' => 0]);

        $this->assertEquals($weightBefore, (float) $attempt->fresh()->attemptQuestions()->sum('weight'));
    }

    public function test_submit_pilgan_benar_langsung_final(): void
    {
        [$challenge] = $this->buildChallengeSet();
        $user = User::factory()->create();

        $attempt = $this->attemptService()->start($challenge, $user);
        $pg = $attempt->attemptQuestions()->where('type', 'pilihan_ganda')->first();

        $result = $this->attemptService()->submitAnswer($pg, 'a');

        $this->assertTrue($result['final']);
        $this->assertTrue($result['is_correct']);
        $this->assertEquals(AttemptQuestion::STATUS_CORRECT, $result['status']);
        $this->assertEquals((float) $pg->weight, $result['score']);
    }

    public function test_submit_pilgan_salah_langsung_final_nol(): void
    {
        [$challenge] = $this->buildChallengeSet();
        $user = User::factory()->create();

        $attempt = $this->attemptService()->start($challenge, $user);
        $pg = $attempt->attemptQuestions()->where('type', 'pilihan_ganda')->first();

        $result = $this->attemptService()->submitAnswer($pg, 'b');

        $this->assertTrue($result['final']);
        $this->assertFalse($result['is_correct']);
        $this->assertEquals(0.0, $result['score']);
    }

    public function test_uraian_salah_belum_final_dan_bisa_retry(): void
    {
        [$challenge] = $this->buildChallengeSet();
        $user = User::factory()->create();

        $attempt = $this->attemptService()->start($challenge, $user);
        $uraian = $attempt->attemptQuestions()->where('type', 'essay')->first();

        // jawaban ngawur -> belum final
        $first = $this->attemptService()->submitAnswer($uraian, 'jawaban ngawur xyz');

        $this->assertFalse($first['final']);
        $this->assertEquals(1, $first['wrong_count']);
        $this->assertEquals(0.0, $first['score']);

        // retry dengan jawaban sama dengan kunci -> final benar, bobot penuh
        $kunci = $uraian->question->flag;
        $second = $this->attemptService()->submitAnswer($uraian->fresh(), $kunci);

        $this->assertTrue($second['final']);
        $this->assertTrue($second['is_correct']);
        $this->assertEquals((float) $uraian->weight, $second['score']);
    }

    public function test_uraian_similarity_dibawah_75_persen_gagal(): void
    {
        [$challenge] = $this->buildChallengeSet();
        $user = User::factory()->create();

        $attempt = $this->attemptService()->start($challenge, $user);
        $uraian = $attempt->attemptQuestions()->where('type', 'essay')->first();

        // jawaban yang mirip tapi jauh -> di bawah 75%
        $result = $this->attemptService()->submitAnswer($uraian, 'abc');

        $this->assertFalse($result['final']);
        $this->assertLessThan(0.75, $result['similarity']);
    }

    public function test_submit_ditolak_setelah_attempt_selesai(): void
    {
        [$challenge] = $this->buildChallengeSet();
        $user = User::factory()->create();

        $attempt = $this->attemptService()->start($challenge, $user);
        $this->attemptService()->finish($attempt);

        $pg = $attempt->attemptQuestions()->where('type', 'pilihan_ganda')->first();

        $this->expectException(\RuntimeException::class);
        $this->attemptService()->submitAnswer($pg->fresh(), 'a');
    }

    public function test_open_hint_menyimpan_waktu_dan_tidak_dobel(): void
    {
        [$challenge] = $this->buildChallengeSet();
        $user = User::factory()->create();

        $attempt = $this->attemptService()->start($challenge, $user);
        $withHint = $attempt->attemptQuestions()->whereHas('question', fn ($q) => $q->whereNotNull('hint'))->first();

        $this->attemptService()->openHint($withHint);
        $openedAt = $withHint->fresh()->hint_used_at;
        $this->assertNotNull($openedAt);

        $this->travel(5)->seconds();
        $this->attemptService()->openHint($withHint->fresh());

        $this->assertEquals($openedAt->timestamp, $withHint->fresh()->hint_used_at->timestamp);
    }

    public function test_hint_dipotong_2_persen_saat_benar(): void
    {
        [$challenge] = $this->buildChallengeSet();
        $user = User::factory()->create();

        $attempt = $this->attemptService()->start($challenge, $user);

        // cari PG dengan bobot 2 (sedang) yang punya hint
        $pg = $attempt->attemptQuestions()
            ->where('type', 'pilihan_ganda')
            ->where('weight', 2)
            ->whereHas('question', fn ($q) => $q->whereNotNull('hint'))
            ->first();

        $this->assertNotNull($pg, 'butuh PG sedang berhint untuk test ini');

        $this->attemptService()->openHint($pg);
        $result = $this->attemptService()->submitAnswer($pg->fresh(), 'a');

        $this->assertTrue($result['is_correct']);
        $this->assertEqualsWithDelta(1.96, $result['score'], 0.0001);
    }

    public function test_finish_menghitung_nilai_akhir_skala_100(): void
    {
        [$challenge] = $this->buildChallengeSet();
        $user = User::factory()->create();

        $attempt = $this->attemptService()->start($challenge, $user);

        // jawab semua soal benar (PG: a, uraian: kunci)
        foreach ($attempt->attemptQuestions()->with('question')->get() as $question) {
            $answer = $question->type === 'pilihan_ganda'
                ? 'a'
                : (string) $question->question->flag;

            $this->attemptService()->submitAnswer($question, $answer);
        }

        $attempt = $this->attemptService()->finish($attempt);

        $this->assertEquals(Attempt::STATUS_COMPLETED, $attempt->status);
        $this->assertNotNull($attempt->finished_at);
        $this->assertEqualsWithDelta(100.0, (float) $attempt->total_score, 0.01);
    }

    public function test_finish_time_expired_membuat_soal_belum_final_jadi_nol(): void
    {
        [$challenge] = $this->buildChallengeSet();
        $user = User::factory()->create();

        $attempt = $this->attemptService()->start($challenge, $user);

        // jawab 1 PG benar, sisanya biarkan
        $pg = $attempt->attemptQuestions()->where('type', 'pilihan_ganda')->first();
        $this->attemptService()->submitAnswer($pg, 'a');

        $attempt = $this->attemptService()->finish($attempt, timeExpired: true);

        $this->assertEquals(Attempt::STATUS_COMPLETED, $attempt->status);

        // soal yang belum final -> time_expired semua
        $this->assertEquals(
            0,
            $attempt->attemptQuestions()->where('status', AttemptQuestion::STATUS_IN_PROGRESS)->count()
        );

        // nilai = bobot PG benar / total bobot x 100
        $totalWeight = (float) $attempt->attemptQuestions()->sum('weight');
        $expected = ((float) $pg->weight / $totalWeight) * 100;
        $this->assertEqualsWithDelta($expected, (float) $attempt->total_score, 0.01);
    }

    public function test_finish_idempoten(): void
    {
        [$challenge] = $this->buildChallengeSet();
        $user = User::factory()->create();

        $attempt = $this->attemptService()->start($challenge, $user);

        $first = $this->attemptService()->finish($attempt);
        $second = $this->attemptService()->finish($attempt->fresh());

        $this->assertEquals($first->id, $second->id);
        $this->assertEquals($first->total_score, $second->total_score);
    }

    public function test_timer_remaining_dan_time_up(): void
    {
        $category = $this->makeCategory();
        $challenge = $this->makeChallenge($category, ['duration_minutes' => 10]);

        $this->seedStockAllCombos($category->id, 4);
        app(ChallengeQuestionService::class)->buildSet($challenge, ['easy' => 2, 'medium' => 0, 'hard' => 0]);

        $attempt = $this->attemptService()->start($challenge, User::factory()->create());
        $attempt->load('challenge');

        $this->assertGreaterThan(0, $this->attemptService()->remainingSeconds($attempt));
        $this->assertFalse($this->attemptService()->isTimeUp($attempt));

        // mundurkan waktu mulai 11 menit -> habis
        $attempt->update(['started_at' => now()->subMinutes(11)]);
        $attempt = $attempt->fresh()->load('challenge');

        $this->assertEquals(0, $this->attemptService()->remainingSeconds($attempt));
        $this->assertTrue($this->attemptService()->isTimeUp($attempt));
    }

    public function test_questions_payload_tidak_membocorkan_hint(): void
    {
        [$challenge] = $this->buildChallengeSet();
        $user = User::factory()->create();

        $attempt = $this->attemptService()->start($challenge, $user);
        $payload = $this->attemptService()->questionsPayload($attempt);

        foreach ($payload as $row) {
            if ($row['has_hint'] && ! $row['hint_opened']) {
                $this->assertArrayNotHasKey('hint', $row, 'teks hint tidak boleh ikut sebelum dibuka');
            }
        }
    }

    public function test_questions_payload_menyertakan_hint_setelah_dibuka(): void
    {
        [$challenge] = $this->buildChallengeSet();
        $user = User::factory()->create();

        $attempt = $this->attemptService()->start($challenge, $user);
        $withHint = $attempt->attemptQuestions()->whereHas('question', fn ($q) => $q->whereNotNull('hint'))->first();

        $this->attemptService()->openHint($withHint);

        $payload = $this->attemptService()->questionsPayload($attempt->fresh());
        $row = collect($payload)->firstWhere('id', $withHint->id);

        $this->assertTrue($row['hint_opened']);
        $this->assertEquals($withHint->question->hint, $row['hint']);
    }

    public function test_total_bobot_sama_antarpeserta(): void
    {
        [$challenge] = $this->buildChallengeSet();

        $attemptA = $this->attemptService()->start($challenge, User::factory()->create());
        $attemptB = $this->attemptService()->start($challenge, User::factory()->create());

        $this->assertEquals(
            (float) $attemptA->attemptQuestions()->sum('weight'),
            (float) $attemptB->attemptQuestions()->sum('weight')
        );
    }

    public function test_recalculate_attempt_menyimpan_total_score(): void
    {
        [$challenge] = $this->buildChallengeSet();
        $user = User::factory()->create();

        $attempt = $this->attemptService()->start($challenge, $user);
        $pg = $attempt->attemptQuestions()->where('type', 'pilihan_ganda')->first();
        $this->attemptService()->submitAnswer($pg, 'a');

        $attempt = app(ScoringService::class)->recalculateAttempt($attempt->fresh());

        $totalWeight = (float) $attempt->attemptQuestions()->sum('weight');
        $expected = ((float) $pg->weight / $totalWeight) * 100;

        $this->assertEqualsWithDelta($expected, (float) $attempt->total_score, 0.01);
    }
}
