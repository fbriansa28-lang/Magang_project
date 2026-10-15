<?php

namespace Tests\Feature;

use App\Models\Attempt;
use App\Models\Category;
use App\Models\Challenge;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Perilaku penanganan error:
 * - Request WEB  -> redirect + flash error (tanpa halaman 403/404).
 * - Request JSON -> tetap status code (dibaca JavaScript halaman kerja).
 */
class ErrorHandlingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedDefaultRoles();
    }

    private function makeUserWithPermission(string $permission): User
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']));

        return $user;
    }

    private function makeAttempt(): Attempt
    {
        $category = Category::create([
            'name' => 'Kategori Error Test',
            'is_active' => true,
            'point_ambang_batas' => 60,
        ]);

        $owner = User::factory()->create();

        $challenge = Challenge::create([
            'kategori_id' => $category->id,
            'title' => 'Challenge Error Test',
            'description' => 'desc',
            'duration_minutes' => 10,
            'is_active' => true,
            'created_by' => $owner->id,
        ]);

        return Attempt::create([
            'challenge_id' => $challenge->id,
            'user_id' => $owner->id,
            'started_at' => now(),
            'status' => Attempt::STATUS_IN_PROGRESS,
        ]);
    }

    public function test_data_tidak_ditemukan_dialihkan_dengan_flash_bukan_404(): void
    {
        $user = $this->makeUserWithPermission('start challenge');
        $this->actingAs($user);

        // Attempt ID tidak ada -> route model binding 404 -> dialihkan + flash.
        $response = $this->get(route('attempts.work', 999999));

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_request_json_404_tetap_status_code(): void
    {
        $user = $this->makeUserWithPermission('start challenge');
        $this->actingAs($user);

        $this->postJson(route('attempts.submit-answer', 999999), [
            'attempt_question_id' => 1,
            'answer' => 'a',
        ])->assertNotFound();
    }

    public function test_request_json_403_tetap_status_code(): void
    {
        // Attempt milik orang lain -> authorizeAnswer abort 403.
        // Request JSON harus tetap 403 (dibaca JavaScript sebagai notifikasi).
        $attempt = $this->makeAttempt();

        $intruder = $this->makeUserWithPermission('start challenge');
        $this->actingAs($intruder);

        $this->postJson(route('attempts.submit-answer', $attempt), [
            'attempt_question_id' => 1,
            'answer' => 'a',
        ])->assertForbidden();
    }

    public function test_halaman_login_menampilkan_flash_success(): void
    {
        $response = $this->withSession(['success' => 'Registrasi berhasil! Silakan login.'])
            ->get(route('login'));

        $response->assertOk();
        $response->assertSee('Registrasi berhasil! Silakan login.');
    }

    public function test_halaman_login_menampilkan_flash_error_session_habis(): void
    {
        $response = $this->withSession(['error' => 'Session habis, silakan login kembali.'])
            ->get(route('login'));

        $response->assertOk();
        $response->assertSee('Session habis, silakan login kembali.');
    }
}
