<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\AttemptService;
use App\Services\ChallengeQuestionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Concerns\CreatesChallenges;
use Tests\TestCase;

/**
 * Test penyusun set soal tetap (ChallengeQuestionService):
 * split otomatis, acak tipe+tingkat sama, fallback tipe, blocking stok.
 */
class ChallengeQuestionServiceTest extends TestCase
{
    use RefreshDatabase;
    use CreatesChallenges;

    private function service(): ChallengeQuestionService
    {
        return app(ChallengeQuestionService::class);
    }

    public function test_split_ganjil_ke_pilgan(): void
    {
        $this->assertEquals(['pilihan_ganda' => 2, 'essay' => 2], ChallengeQuestionService::splitFor(4));
        $this->assertEquals(['pilihan_ganda' => 2, 'essay' => 1], ChallengeQuestionService::splitFor(3));
        $this->assertEquals(['pilihan_ganda' => 1, 'essay' => 0], ChallengeQuestionService::splitFor(1));
        $this->assertEquals(['pilihan_ganda' => 0, 'essay' => 0], ChallengeQuestionService::splitFor(0));
    }

    public function test_set_berisi_jumlah_dan_tipe_sesuai_split(): void
    {
        $category = $this->makeCategory();
        $challenge = $this->makeChallenge($category);

        $this->seedStockAllCombos($category->id, 5);

        $this->service()->buildSet($challenge, ['easy' => 4, 'medium' => 0, 'hard' => 0]);

        $set = $challenge->challengeQuestions()->get();

        $this->assertCount(4, $set);
        $this->assertEquals(2, $set->filter(fn ($q) => $q->type === 'pilihan_ganda')->count());
        $this->assertEquals(2, $set->filter(fn ($q) => $q->type === 'essay')->count());
        $this->assertEquals(4, $set->filter(fn ($q) => $q->difficulty === 'easy')->count());
    }

    public function test_bobot_mengikuti_tipe_intrinsik(): void
    {
        $category = $this->makeCategory();
        $challenge = $this->makeChallenge($category);

        $this->seedStockAllCombos($category->id, 5);

        // mudah=2 -> 1 PG (bobot 1) + 1 uraian (bobot 3)
        $this->service()->buildSet($challenge, ['easy' => 2, 'medium' => 0, 'hard' => 0]);

        $set = $challenge->challengeQuestions()->get();

        $pg = $set->first(fn ($q) => $q->type === 'pilihan_ganda');
        $essay = $set->first(fn ($q) => $q->type === 'essay');

        $this->assertEquals(1.0, (float) $pg->weight);
        $this->assertEquals(3.0, (float) $essay->weight);
    }

    public function test_set_sama_untuk_semua_peserta(): void
    {
        $category = $this->makeCategory();
        $challenge = $this->makeChallenge($category);

        $this->seedStockAllCombos($category->id, 5);
        $this->service()->buildSet($challenge, ['easy' => 4, 'medium' => 2, 'hard' => 2]);

        $attemptService = app(AttemptService::class);

        $attemptA = $attemptService->start($challenge, User::factory()->create());
        $attemptB = $attemptService->start($challenge, User::factory()->create());

        $idsA = $attemptA->attemptQuestions()->pluck('question_id')->sort()->values()->all();
        $idsB = $attemptB->attemptQuestions()->pluck('question_id')->sort()->values()->all();

        $this->assertEquals($idsA, $idsB, 'semua peserta harus menerima set soal yang sama');
    }

    public function test_urutan_tampil_diacak_per_peserta(): void
    {
        $category = $this->makeCategory();
        $challenge = $this->makeChallenge($category);

        $this->seedStockAllCombos($category->id, 10);
        $this->service()->buildSet($challenge, ['easy' => 8, 'medium' => 0, 'hard' => 0]);

        $attemptService = app(AttemptService::class);

        // Buat beberapa peserta; minimal satu urutan berbeda dari peserta pertama.
        $orders = [];
        for ($i = 0; $i < 6; $i++) {
            $attempt = $attemptService->start($challenge, User::factory()->create());
            $orders[] = $attempt->attemptQuestions()->orderBy('order_no')->pluck('question_id')->all();
        }

        $uniqueOrders = array_unique(array_map(fn ($o) => implode(',', $o), $orders));

        $this->assertGreaterThan(1, count($uniqueOrders), 'urutan tampil harus bervariasi antarpeserta');
    }

    public function test_fallback_ke_tipe_sebaliknya_bila_pool_habis(): void
    {
        $category = $this->makeCategory();
        $challenge = $this->makeChallenge($category);

        // Hanya ada PG mudah (4 soal), uraian mudah kosong.
        $this->seedStock($category->id, 'pilihan_ganda', 'easy', 4);

        // butuh 4 -> split 2 PG + 2 uraian; uraian kosong -> 2 slot diisi PG
        $this->service()->buildSet($challenge, ['easy' => 4, 'medium' => 0, 'hard' => 0]);

        $set = $challenge->challengeQuestions()->get();

        $this->assertCount(4, $set);
        $this->assertEquals(4, $set->filter(fn ($q) => $q->type === 'pilihan_ganda')->count());
        $this->assertEquals(0, $set->filter(fn ($q) => $q->type === 'essay')->count());
    }

    public function test_stok_kurang_dipakai_semua_yang_tersedia(): void
    {
        $category = $this->makeCategory();
        $challenge = $this->makeChallenge($category);

        // butuh 4, hanya ada 3 soal (2 PG + 1 uraian)
        $this->seedStock($category->id, 'pilihan_ganda', 'easy', 2);
        $this->seedStock($category->id, 'essay', 'easy', 1);

        $this->service()->buildSet($challenge, ['easy' => 4, 'medium' => 0, 'hard' => 0]);

        $this->assertCount(3, $challenge->challengeQuestions()->get());
    }

    public function test_validate_menolak_saat_stok_kurang_dengan_pesan_jelas(): void
    {
        $category = $this->makeCategory();

        $this->seedStock($category->id, 'pilihan_ganda', 'easy', 2);
        $this->seedStock($category->id, 'essay', 'easy', 1);

        $errors = $this->service()->validateRequestedCounts($category->id, ['easy' => 4, 'medium' => 0, 'hard' => 0]);

        $this->assertArrayHasKey('easy', $errors);
        $this->assertStringContainsString('hanya 3 soal', $errors['easy']);
        $this->assertStringContainsString('PG 2 + Uraian 1', $errors['easy']);
        $this->assertStringContainsString('diminta 4', $errors['easy']);
    }

    public function test_validate_lolos_saat_stok_cukup(): void
    {
        $category = $this->makeCategory();

        $this->seedStockAllCombos($category->id, 5);

        $errors = $this->service()->validateRequestedCounts($category->id, ['easy' => 4, 'medium' => 4, 'hard' => 4]);

        $this->assertEmpty($errors);
    }

    public function test_stock_summary_menghitung_per_tipe_dan_tingkat(): void
    {
        $category = $this->makeCategory();

        $this->seedStock($category->id, 'pilihan_ganda', 'easy', 3);
        $this->seedStock($category->id, 'essay', 'easy', 2);
        $this->seedStock($category->id, 'essay', 'hard', 1);

        $summary = $this->service()->stockSummary($category->id);

        $this->assertEquals(3, $summary['easy']['pilihan_ganda']);
        $this->assertEquals(2, $summary['easy']['essay']);
        $this->assertEquals(5, $summary['easy']['total']);
        $this->assertEquals(0, $summary['medium']['total']);
        $this->assertEquals(1, $summary['hard']['total']);
    }

    public function test_statistics_for_category_membatasi_stok_per_kategori(): void
    {
        $categoryA = $this->makeCategory();
        $categoryB = $this->makeCategory();

        $this->seedStock($categoryA->id, 'pilihan_ganda', 'easy', 4);
        $this->seedStock($categoryA->id, 'essay', 'medium', 2);
        $this->seedStock($categoryB->id, 'essay', 'hard', 3);

        $statsA = \App\Models\QuestionBank::statisticsForCategory($categoryA->id);

        $this->assertEquals(6, $statsA['total']['total']);
        $this->assertEquals(4, $statsA['easy']['total']);
        $this->assertEquals(2, $statsA['medium']['total']);
        $this->assertEquals(0, $statsA['hard']['total']);

        // Tanpa kategori (null) = seluruh kategori.
        $statsAll = \App\Models\QuestionBank::statisticsForCategory(null);

        $this->assertEquals(9, $statsAll['total']['total']);
        $this->assertEquals(3, $statsAll['hard']['total']);
    }

    public function test_statistics_for_category_menghitung_soal_baru_dan_abaikan_nonaktif(): void
    {
        $category = $this->makeCategory();

        $this->seedStock($category->id, 'pilihan_ganda', 'easy', 3);

        // Soal nonaktif tidak boleh dihitung.
        \App\Models\QuestionBank::factory()->count(2)->inactive()->state([
            'kategori_id' => $category->id,
            'question_type' => 'pilihan_ganda',
            'difficulty' => 'easy',
        ])->create();

        // Soal lama (10 hari) tidak masuk hitungan "Baru".
        \App\Models\QuestionBank::factory()->state([
            'kategori_id' => $category->id,
            'question_type' => 'essay',
            'difficulty' => 'easy',
            'created_at' => now()->subDays(10),
        ])->create();

        $stats = \App\Models\QuestionBank::statisticsForCategory($category->id);

        $this->assertEquals(4, $stats['total']['total']);
        $this->assertEquals(3, $stats['easy']['recent']);
    }

    public function test_set_summary_total_bobot(): void
    {
        $category = $this->makeCategory();
        $challenge = $this->makeChallenge($category);

        $this->seedStockAllCombos($category->id, 5);

        // mudah=4 -> 2 PG(1) + 2 uraian(3) = 8; sedang=3 -> 2 PG(2) + 1 uraian(5) = 9
        $this->service()->buildSet($challenge, ['easy' => 4, 'medium' => 3, 'hard' => 0]);

        $summary = $this->service()->setSummary($challenge);

        $this->assertEquals(7, $summary['total_questions']);
        $this->assertEquals(17.0, $summary['total_weight']);
    }

    public function test_build_set_ulang_menghapus_set_lama(): void
    {
        $category = $this->makeCategory();
        $challenge = $this->makeChallenge($category);

        $this->seedStockAllCombos($category->id, 5);

        $this->service()->buildSet($challenge, ['easy' => 4, 'medium' => 0, 'hard' => 0]);
        $this->assertDatabaseCount('challenge_questions', 4);

        $this->service()->buildSet($challenge, ['easy' => 2, 'medium' => 2, 'hard' => 0]);

        $this->assertDatabaseCount('challenge_questions', 4);
        $this->assertEquals(2, $challenge->challengeQuestions()->where('difficulty', 'medium')->count());
    }

    public function test_soal_nonaktif_tidak_dipilih(): void
    {
        $category = $this->makeCategory();
        $challenge = $this->makeChallenge($category);

        $this->seedStock($category->id, 'pilihan_ganda', 'easy', 2);

        // 3 soal PG mudah nonaktif -> tidak boleh terpilih
        \App\Models\QuestionBank::factory()->count(3)->inactive()->state([
            'kategori_id' => $category->id,
            'question_type' => 'pilihan_ganda',
            'difficulty' => 'easy',
        ])->create();

        $this->service()->buildSet($challenge, ['easy' => 2, 'medium' => 0, 'hard' => 0]);

        $set = $challenge->challengeQuestions()->get();

        $this->assertCount(2, $set);
        $this->assertEquals(2, $set->filter(fn ($q) => $q->type === 'pilihan_ganda')->count());
    }

    public function test_start_melempar_exception_saat_set_kosong(): void
    {
        $category = $this->makeCategory();
        $challenge = $this->makeChallenge($category);

        $this->expectException(\RuntimeException::class);

        app(AttemptService::class)->start($challenge, User::factory()->create());
    }
}
