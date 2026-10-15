<?php

namespace Tests\Feature;

use App\Models\Attempt;
use App\Models\Category;
use App\Models\Challenge;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Permission;
use App\Models\Role;
use Tests\TestCase;

class LeaderboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedDefaultRoles();
    }

    private function admin(): User
    {
        $admin = User::factory()->create(['name' => 'Admin Tester']);
        $admin->givePermissionTo(Permission::firstOrCreate(['name' => 'view leaderboard', 'guard_name' => 'web']));
        $admin->givePermissionTo(Permission::firstOrCreate(['name' => 'show leaderboard', 'guard_name' => 'web']));

        return $admin;
    }

    private function seedChallenge(User $admin): Challenge
    {
        $category = Category::create(['name' => 'Web Security', 'slug' => 'web-security', 'is_active' => true, 'point_ambang_batas' => 60]);

        return Challenge::create(['kategori_id' => $category->id, 'title' => 'Challenge Test', 'description' => 'desc', 'duration_minutes' => 10, 'is_active' => true, 'created_by' => $admin->id]);
    }

    public function test_index_shows_challenge_list(): void
    {
        $admin = $this->admin();
        $challenge = $this->seedChallenge($admin);

        $response = $this->actingAs($admin)->get(route('leaderboard.index'));

        $response->assertOk()->assertViewIs('leaderboard.index');
        // Judul dirender apa adanya (bukan uppercase) di dalam tabel.
        $response->assertSee($challenge->title);
        $response->assertSee(route('leaderboard.show', $challenge), false);
    }

    public function test_index_filters_by_category(): void
    {
        $admin = $this->admin();
        $challenge = $this->seedChallenge($admin);

        $response = $this->actingAs($admin)->get(route('leaderboard.index', ['category_id' => $challenge->kategori_id]));

        $response->assertOk();
        $response->assertSee($challenge->title);
    }

    public function test_show_displays_all_participants(): void
    {
        $admin = $this->admin();
        Role::firstOrCreate(['name' => 'Peserta', 'guard_name' => 'web']);
        $participant = User::factory()->create(['name' => 'Peserta Show']);
        $participant->assignRole('Peserta');
        $challenge = $this->seedChallenge($admin);

        Attempt::create(['challenge_id' => $challenge->id, 'user_id' => $participant->id, 'started_at' => now()->subMinutes(5), 'finished_at' => now(), 'total_score' => 75, 'status' => Attempt::STATUS_COMPLETED]);

        $response = $this->actingAs($admin)->get(route('leaderboard.show', $challenge));

        $response->assertOk()->assertViewIs('leaderboard.show');
        // Nama dirender apa adanya (bukan uppercase) + skor 2 desimal.
        $response->assertSee('Peserta Show');
        $response->assertSee('75.00');
    }

    public function test_show_back_button_links_to_index(): void
    {
        $admin = $this->admin();
        $challenge = $this->seedChallenge($admin);

        // Tombol "KEMBALI KE BERANDA" hanya dirender untuk non-admin,
        // jadi test dijalankan sebagai peserta yang sudah menyelesaikan challenge.
        $participant = User::factory()->create(['name' => 'Peserta Kembali']);
        $participant->assignRole('Peserta');
        Attempt::create([
            'challenge_id' => $challenge->id,
            'user_id' => $participant->id,
            'started_at' => now()->subMinutes(5),
            'finished_at' => now(),
            'total_score' => 60,
            'status' => Attempt::STATUS_COMPLETED,
        ]);

        $response = $this->actingAs($participant)->get(route('leaderboard.show', $challenge));

        $response->assertOk();
        $response->assertSee('PAPAN PERINGKAT');
        $response->assertSee('KEMBALI KE BERANDA');
    }
}
