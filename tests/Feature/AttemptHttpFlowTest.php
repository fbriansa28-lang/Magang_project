<?php

namespace Tests\Feature;

use App\Models\Attempt;
use App\Models\AttemptQuestion;
use App\Models\User;
use App\Services\ChallengeQuestionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Permission;
use Tests\Feature\Concerns\CreatesChallenges;
use Tests\TestCase;

/**
 * Test HTTP alur challenge (set soal tetap):
 * admin simpan challenge (blocking stok + build set) -> peserta kerja
 * (per-soal submit, retry, hint) -> SELESAI -> hasil.
 */
class AttemptHttpFlowTest extends TestCase
{
    use RefreshDatabase;
    use CreatesChallenges;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedDefaultRoles();
    }

    private function makePeserta(): User
    {
        $user = User::factory()->create();
        $user->assignRole('Peserta');

        foreach (['start challenge'] as $permission) {
            $user->givePermissionTo(Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']));
        }

        return $user;
    }

    private function makeAdmin(): User
    {
        $user = User::factory()->create();
        $user->assignRole('Admin');

        foreach (['view challenge', 'create challenge', 'edit challenge', 'delete challenge'] as $permission) {
            $user->givePermissionTo(Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']));
        }

        return $user;
    }

    private function seedChallengeWithSet(array $counts = ['easy' => 4, 'medium' => 3, 'hard' => 2]): array
    {
        $category = $this->makeCategory();
        $challenge = $this->makeChallenge($category, $counts);

        $this->seedStockAllCombos($category->id, 8, withHint: true);
        app(ChallengeQuestionService::class)->buildSet($challenge, $counts);

        return [$challenge, $category];
    }

    public function test_admin_bisa_menyimpan_challenge_dan_set_dibangun(): void
    {
        $category = $this->makeCategory();
        $this->seedStockAllCombos($category->id, 8);
        $admin = $this->makeAdmin();

        $this->actingAs($admin);

        $response = $this->post(route('challenges.store'), [
            'kategori_id' => $category->id,
            'title' => 'Challenge Baru',
            'description' => 'deskripsi',
            'duration_minutes' => 30,
            'questions_easy' => 4,
            'questions_medium' => 2,
            'questions_hard' => 0,
            'is_active' => 1,
        ]);

        $response->assertRedirect(route('challenges.index'));

        $challenge = \App\Models\Challenge::where('title', 'Challenge Baru')->firstOrFail();

        // mudah=4 -> 2 PG + 2 uraian; sedang=2 -> 1 PG + 1 uraian = 6 soal
        $this->assertEquals(6, $challenge->challengeQuestions()->count());
    }

    public function test_simpan_ditolak_saat_stok_kurang_dengan_alert(): void
    {
        $category = $this->makeCategory();
        // hanya 3 soal mudah, tapi diminta 5
        $this->seedStock($category->id, 'pilihan_ganda', 'easy', 2);
        $this->seedStock($category->id, 'essay', 'easy', 1);

        $admin = $this->makeAdmin();
        $this->actingAs($admin);

        $response = $this->post(route('challenges.store'), [
            'kategori_id' => $category->id,
            'title' => 'Challenge Gagal',
            'description' => 'deskripsi',
            'duration_minutes' => 30,
            'questions_easy' => 5,
            'questions_medium' => 0,
            'questions_hard' => 0,
            'is_active' => 1,
        ]);

        $response->assertSessionHasErrors('questions_easy');
        $this->assertDatabaseMissing('challenges', ['title' => 'Challenge Gagal']);
        $this->assertDatabaseMissing('challenge_questions', []);
    }

    public function test_peserta_mulai_dan_melihat_halaman_kerja(): void
    {
        [$challenge] = $this->seedChallengeWithSet();
        $user = $this->makePeserta();

        $this->actingAs($user);

        $start = $this->get(route('attempts.start', $challenge));
        $start->assertRedirect();

        $attempt = Attempt::where('user_id', $user->id)->firstOrFail();

        $work = $this->get(route('attempts.work', $attempt));
        $work->assertOk();
        $work->assertViewIs('challenge.attempt');
        $work->assertSee($challenge->title);
    }

    public function test_halaman_kerja_menampilkan_layout_pengerjaan_baru(): void
    {
        [$challenge] = $this->seedChallengeWithSet();
        $user = $this->makePeserta();

        $this->actingAs($user);
        $this->get(route('attempts.start', $challenge));

        $attempt = Attempt::where('user_id', $user->id)->firstOrFail();

        $work = $this->get(route('attempts.work', $attempt));

        $work->assertOk();
        $work->assertViewIs('challenge.attempt');

        // Sidebar status pengerjaan.
        $work->assertSee('Batas_Waktu');
        $work->assertSee('STATUS_PENGERJAAN');
        $work->assertSee('REWARD');
        $work->assertSee('LEVEL PROGRESS');
        $work->assertSee('PESERTA');
        $work->assertSee('HINT_SYSTEM');

        // Kartu soal & jawaban.
        $work->assertSee('JAWABAN');
        $work->assertSee('ALIRAN DATA TERENKRIPSI');

        // Modal hint.
        $work->assertSee('SUBSISTEM_PETUNJUK');
        $work->assertSee('KEMBALI');

        // Navigator soal: tombol bernomor sebanyak soal.
        $work->assertSee('data-nav-index="0"', false);
        $work->assertSee('Belum dikerjakan');
        $work->assertSee('Selesai dikerjakan');

        // Tombol global SELESAI tersedia (muncul via JS saat semua soal terjawab).
        $work->assertSee('finishGlobalButton', false);
        $work->assertSee('finishAttempt', false);
    }

    public function test_peserta_submit_pilgan_via_http(): void
    {
        [$challenge] = $this->seedChallengeWithSet();
        $user = $this->makePeserta();

        $this->actingAs($user);
        $this->get(route('attempts.start', $challenge));

        $attempt = Attempt::where('user_id', $user->id)->firstOrFail();
        $pg = $attempt->attemptQuestions()->where('type', 'pilihan_ganda')->first();

        $response = $this->postJson(route('attempts.submit-answer', $attempt), [
            'attempt_question_id' => $pg->id,
            'answer' => 'a',
        ]);

        $response->assertOk();
        $response->assertJson(['success' => true, 'final' => true, 'is_correct' => true]);
    }

    public function test_peserta_retry_uraian_via_http(): void
    {
        [$challenge] = $this->seedChallengeWithSet();
        $user = $this->makePeserta();

        $this->actingAs($user);
        $this->get(route('attempts.start', $challenge));

        $attempt = Attempt::where('user_id', $user->id)->firstOrFail();
        $uraian = $attempt->attemptQuestions()->where('type', 'essay')->first();

        // jawaban ngawur -> belum final
        $first = $this->postJson(route('attempts.submit-answer', $attempt), [
            'attempt_question_id' => $uraian->id,
            'answer' => 'ngawur total',
        ]);
        $first->assertOk();
        $first->assertJson(['final' => false]);

        // retry dengan kunci -> final benar
        $kunci = $uraian->question->flag;
        $second = $this->postJson(route('attempts.submit-answer', $attempt), [
            'attempt_question_id' => $uraian->id,
            'answer' => $kunci,
        ]);
        $second->assertOk();
        $second->assertJson(['final' => true, 'is_correct' => true]);
    }

    /**
     * Regresi: uraian yang masih salah (< 75%) BELUM final, sehingga saat
     * halaman kerja dimuat ulang tombol KIRIM JAWABAN harus tetap muncul.
     */
    public function test_uraian_salah_belum_final_dan_tombol_kirim_tetap_muncul(): void
    {
        [$challenge] = $this->seedChallengeWithSet();
        $user = $this->makePeserta();

        $this->actingAs($user);
        $this->get(route('attempts.start', $challenge));

        $attempt = Attempt::where('user_id', $user->id)->firstOrFail();
        $uraian = $attempt->attemptQuestions()->where('type', 'essay')->first();

        // Jawaban ngawur -> status wrong, belum final.
        $this->postJson(route('attempts.submit-answer', $attempt), [
            'attempt_question_id' => $uraian->id,
            'answer' => 'ngawur total',
        ])->assertOk()->assertJson(['final' => false]);

        $uraian->refresh();

        $this->assertEquals(AttemptQuestion::STATUS_WRONG, $uraian->status);
        $this->assertFalse($uraian->isFinalized(), 'uraian salah harus belum final agar bisa retry');

        // Halaman kerja dimuat ulang: tombol kirim harus tetap ada di soal uraian.
        $work = $this->get(route('attempts.work', $attempt));
        $work->assertOk();
        $work->assertSee('KIRIM JAWABAN');
        $work->assertSee('answer-footer', false);
    }

    /**
     * Regresi: PG salah tetap final (sekali jawab) sehingga tombol kirimnya hilang.
     */
    public function test_pilgan_salah_tetap_final(): void
    {
        [$challenge] = $this->seedChallengeWithSet();
        $user = $this->makePeserta();

        $this->actingAs($user);
        $this->get(route('attempts.start', $challenge));

        $attempt = Attempt::where('user_id', $user->id)->firstOrFail();
        $pg = $attempt->attemptQuestions()->where('type', 'pilihan_ganda')->first();

        // Pilih opsi yang bukan kunci -> final salah.
        $key = $pg->question->correct_option;
        $wrongOption = $key === 'a' ? 'b' : 'a';

        $this->postJson(route('attempts.submit-answer', $attempt), [
            'attempt_question_id' => $pg->id,
            'answer' => $wrongOption,
        ])->assertOk()->assertJson(['final' => true, 'is_correct' => false]);

        $pg->refresh();

        $this->assertEquals(AttemptQuestion::STATUS_WRONG, $pg->status);
        $this->assertTrue($pg->isFinalized(), 'PG salah tetap final (sekali jawab)');
    }

    public function test_hint_via_http_mengembalikan_persentase_konstanta(): void
    {
        [$challenge] = $this->seedChallengeWithSet();
        $user = $this->makePeserta();

        $this->actingAs($user);
        $this->get(route('attempts.start', $challenge));

        $attempt = Attempt::where('user_id', $user->id)->firstOrFail();
        $withHint = $attempt->attemptQuestions()->whereHas('question', fn ($q) => $q->whereNotNull('hint'))->first();

        $response = $this->postJson(route('attempt-questions.hint', $withHint));

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'hint_penalty_percent' => 2.0,
            'hint_opened' => true,
        ]);
    }

    public function test_user_lain_tidak_bisa_mengakses_attempt(): void
    {
        [$challenge] = $this->seedChallengeWithSet();
        $owner = $this->makePeserta();

        $this->actingAs($owner);
        $this->get(route('attempts.start', $challenge));
        $attempt = Attempt::where('user_id', $owner->id)->firstOrFail();

        $intruder = $this->makePeserta();
        $this->actingAs($intruder);

        // Request WEB -> dialihkan (redirect) + flash error, bukan halaman 403.
        $this->get(route('attempts.work', $attempt))
            ->assertRedirect()
            ->assertSessionHas('error');

        // Request JSON -> tetap status 403 + JSON (dibaca JavaScript).
        $this->postJson(route('attempts.submit-answer', $attempt), [
            'attempt_question_id' => $attempt->attemptQuestions()->first()->id,
            'answer' => 'a',
        ])->assertForbidden();
    }

    public function test_finish_manual_redirect_ke_hasil(): void
    {
        [$challenge] = $this->seedChallengeWithSet();
        $user = $this->makePeserta();

        $this->actingAs($user);
        $this->get(route('attempts.start', $challenge));

        $attempt = Attempt::where('user_id', $user->id)->firstOrFail();

        $response = $this->post(route('attempts.finish', $attempt));

        $response->assertRedirect(route('attempts.result', $attempt));

        $attempt->refresh();
        $this->assertEquals(Attempt::STATUS_COMPLETED, $attempt->status);

        $result = $this->get(route('attempts.result', $attempt));
        $result->assertOk();
        $result->assertViewIs('challenge.result');

        // Layout laporan hasil baru.
        $result->assertSee('LAPORAN HASIL PENGERJAAN');
        $result->assertSee('Terima Kasih Sudah mengerjakan Challenge ini');
        $result->assertSee('KONEKSI BERHASIL');
        $result->assertSee('PERINGKAT');
        $result->assertSee('JUMLAH BENAR');
        $result->assertSee('PENGGUNAAN HINT');
        $result->assertSee('WAKTU PENGERJAAN');
        $result->assertSee('LIHAT PAPAN PERINGKAT');
    }

    /**
     * Peserta yang SUDAH menyelesaikan challenge boleh membuka papan
     * peringkat challenge tersebut (tanpa permission admin).
     */
    public function test_peserta_bisa_membuka_papan_peringkat_setelah_selesai(): void
    {
        [$challenge] = $this->seedChallengeWithSet();
        $user = $this->makePeserta();

        $this->actingAs($user);
        $this->get(route('attempts.start', $challenge));

        $attempt = Attempt::where('user_id', $user->id)->firstOrFail();
        $this->post(route('attempts.finish', $attempt));

        $response = $this->get(route('leaderboard.show', $challenge));

        $response->assertOk();
        $response->assertViewIs('leaderboard.show');
        $response->assertSee('PAPAN PERINGKAT');
        $response->assertSee('Nama Pengguna');
        $response->assertSee('KEMBALI KE BERANDA');
        // Baris peserta sendiri ditandai YOU.
        $response->assertSee('YOU');
    }

    /**
     * Peserta yang BELUM menyelesaikan challenge tidak boleh membuka
     * papan peringkat challenge itu (403).
     */
    public function test_peserta_tidak_bisa_membuka_papan_peringkat_challenge_lain(): void
    {
        [$challenge] = $this->seedChallengeWithSet();
        $user = $this->makePeserta();

        $this->actingAs($user);

        // Belum punya attempt sama sekali -> dialihkan + flash error.
        $this->get(route('leaderboard.show', $challenge))
            ->assertRedirect()
            ->assertSessionHas('error');

        // Attempt masih berjalan (belum finish) juga ditolak.
        $this->get(route('attempts.start', $challenge));
        $this->get(route('leaderboard.show', $challenge))
            ->assertRedirect()
            ->assertSessionHas('error');
    }

    public function test_work_redirect_ke_hasil_bila_sudah_selesai(): void
    {
        [$challenge] = $this->seedChallengeWithSet();
        $user = $this->makePeserta();

        $this->actingAs($user);
        $this->get(route('attempts.start', $challenge));

        $attempt = Attempt::where('user_id', $user->id)->firstOrFail();
        $this->post(route('attempts.finish', $attempt));

        $this->get(route('attempts.work', $attempt))->assertRedirect(route('attempts.result', $attempt));
    }

    public function test_mulai_ulang_mengembalikan_attempt_yang_sama(): void
    {
        [$challenge] = $this->seedChallengeWithSet();
        $user = $this->makePeserta();

        $this->actingAs($user);

        $this->get(route('attempts.start', $challenge));
        $this->get(route('attempts.start', $challenge));

        $this->assertEquals(1, Attempt::where('user_id', $user->id)->count());
    }

    public function test_halaman_index_dan_edit_challenge_terrender(): void
    {
        [$challenge] = $this->seedChallengeWithSet();
        $admin = $this->makeAdmin();

        $this->actingAs($admin);

        $this->get(route('challenges.index'))->assertOk()->assertSee($challenge->title);
        $this->get(route('challenges.create'))->assertOk();
        $this->get(route('challenges.edit', $challenge))->assertOk();
    }

    public function test_form_create_challenge_menampilkan_layout_kelola_soal(): void
    {
        $admin = $this->makeAdmin();
        $this->actingAs($admin);

        $response = $this->get(route('challenges.create'));

        $response->assertOk();
        $response->assertSee('KELOLA SOAL');
        $response->assertSee('BUAT_CHALLENGE');
        $response->assertSee('Judul Challenge');
        $response->assertSee('Deskripsi');
        $response->assertSee('Total Soal');
        $response->assertSee('Durasi');
        $response->assertSee('Total Soal Mudah');
        $response->assertSee('Total Soal Sedang');
        $response->assertSee('Total Soal Sulit');
        $response->assertSee('name="questions_easy"', false);
        $response->assertSee('name="questions_medium"', false);
        $response->assertSee('name="questions_hard"', false);
        $response->assertSee('BATAL');
        $response->assertSee('SIMPAN');
    }

    public function test_kartu_stok_dan_batas_level_mengikuti_stok_bank_soal_kategori(): void
    {
        $category = $this->makeCategory();
        $this->seedStock($category->id, 'pilihan_ganda', 'easy', 4);
        $this->seedStock($category->id, 'essay', 'easy', 1);
        $this->seedStock($category->id, 'essay', 'hard', 2);

        $admin = $this->makeAdmin();
        $this->actingAs($admin);

        $response = $this->get(route('challenges.create'));

        $response->assertOk();

        // Data stok per kategori tersedia untuk JS (kartu & batas level).
        $response->assertSee('csStockByCategory', false);

        // Peringatan stok merah tersedia sebagai blok terpisah di bawah level
        // (muncul via JS bila input > stok).
        $response->assertSee('id="cs-stock-warning"', false);
        $response->assertSee('csUpdateStockWarnings', false);

        // Batas input level = stok kategori terpilih (total 7 = 5 mudah + 0 sedang + 2 sulit).
        $response->assertSee('data-stock-input="easy"', false);
        $response->assertSee('data-stock-input="medium"', false);
        $response->assertSee('data-stock-input="hard"', false);
        $response->assertSee('data-stock-max="5"', false);
        $response->assertSee('data-stock-max="0"', false);
        $response->assertSee('data-stock-max="2"', false);

        $stockMap = $this->getStockMapFromResponse($response->getContent());
        $categoryKey = (string) $category->id;

        $this->assertEquals(7, $stockMap[$categoryKey]['total']);
        $this->assertEquals(5, $stockMap[$categoryKey]['easy']);
        $this->assertEquals(0, $stockMap[$categoryKey]['medium']);
        $this->assertEquals(2, $stockMap[$categoryKey]['hard']);
    }

    /**
     * Ambil window.csStockByCategory dari HTML halaman create/edit challenge.
     *
     * @return array<string, array{total: int, recent: int, easy: int, medium: int, hard: int}>
     */
    private function getStockMapFromResponse(string $html): array
    {
        preg_match('/window\.csStockByCategory = (\{.*?\});/s', $html, $matches);

        $this->assertNotEmpty($matches, 'window.csStockByCategory tidak ditemukan di halaman.');

        return json_decode($matches[1], true) ?: [];
    }

    public function test_form_edit_challenge_menampilkan_layout_kelola_soal(): void
    {
        [$challenge] = $this->seedChallengeWithSet();
        $admin = $this->makeAdmin();
        $this->actingAs($admin);

        $response = $this->get(route('challenges.edit', $challenge));

        $response->assertOk();
        $response->assertSee('KELOLA SOAL');
        $response->assertSee('EDIT_CHALLENGE');
        $response->assertSee($challenge->title);
        $response->assertSee('name="questions_easy"', false);
        $response->assertSee('SIMPAN');
    }
}
