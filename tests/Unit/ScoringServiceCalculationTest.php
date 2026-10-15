<?php

namespace Tests\Unit;

use App\Services\ScoringService;
use PHPUnit\Framework\TestCase;

/**
 * Test murni perhitungan skor (tanpa database).
 * Bobot: PG 1/2/3, Uraian 3/5/8. Similarity >= 75% = benar. Hint -2%.
 */
class ScoringServiceCalculationTest extends TestCase
{
    private ScoringService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ScoringService();
    }

    public function test_tabel_bobot_pilgan(): void
    {
        $this->assertEquals(1.0, ScoringService::weightFor('pilihan_ganda', 'easy'));
        $this->assertEquals(2.0, ScoringService::weightFor('pilihan_ganda', 'medium'));
        $this->assertEquals(3.0, ScoringService::weightFor('pilihan_ganda', 'hard'));
    }

    public function test_tabel_bobot_uraian(): void
    {
        $this->assertEquals(3.0, ScoringService::weightFor('essay', 'easy'));
        $this->assertEquals(5.0, ScoringService::weightFor('essay', 'medium'));
        $this->assertEquals(8.0, ScoringService::weightFor('essay', 'hard'));
    }

    public function test_tabel_bobot_bisa_dipanggil_dengan_string(): void
    {
        $this->assertEquals(3.0, ScoringService::weightFor('pilihan_ganda', 'hard'));
        $this->assertEquals(8.0, ScoringService::weightFor('essay', 'hard'));
    }

    public function test_pilgan_benar_mendapat_bobot_penuh(): void
    {
        $this->assertEquals(3.0, $this->service->rawScorePilihanGanda(true, 3.0));
    }

    public function test_pilgan_salah_mendapat_nol(): void
    {
        $this->assertEquals(0.0, $this->service->rawScorePilihanGanda(false, 3.0));
    }

    public function test_uraian_similarity_tepat_75_persen_dianggap_benar(): void
    {
        $this->assertTrue($this->service->similarityCukup(0.75));
        $this->assertEquals(5.0, $this->service->rawScoreUraian(5.0, 0.75));
    }

    public function test_uraian_similarity_di_bawah_75_persen_mendapat_nol(): void
    {
        $this->assertFalse($this->service->similarityCukup(0.7499));
        $this->assertEquals(0.0, $this->service->rawScoreUraian(5.0, 0.7499));
    }

    public function test_uraian_similarity_100_persen_mendapat_bobot_penuh(): void
    {
        $this->assertEquals(8.0, $this->service->rawScoreUraian(8.0, 1.0));
    }

    public function test_penalti_hint_2_persen_dari_bobot(): void
    {
        // PG sedang (bobot 2) benar + hint -> 2 - 0.04 = 1.96
        $penalty = $this->service->hintPenalty(2.0, true, 2.0);
        $this->assertEqualsWithDelta(0.04, $penalty, 0.0001);
        $this->assertEqualsWithDelta(1.96, $this->service->finalScoreSoal(2.0, $penalty), 0.0001);

        // Uraian sulit (bobot 8) benar + hint -> 8 - 0.16 = 7.84
        $penalty8 = $this->service->hintPenalty(8.0, true, 8.0);
        $this->assertEqualsWithDelta(0.16, $penalty8, 0.0001);
        $this->assertEqualsWithDelta(7.84, $this->service->finalScoreSoal(8.0, $penalty8), 0.0001);
    }

    public function test_penalti_tidak_berlaku_tanpa_hint(): void
    {
        $this->assertEquals(0.0, $this->service->hintPenalty(5.0, false, 5.0));
    }

    public function test_penalti_tidak_berlaku_saat_skor_nol(): void
    {
        $this->assertEquals(0.0, $this->service->hintPenalty(5.0, true, 0.0));
    }

    public function test_skor_tidak_pernah_negatif(): void
    {
        $this->assertEquals(0.0, $this->service->finalScoreSoal(0.0, 5.0));
    }
}
