<?php

namespace Tests\Feature;

use App\Models\Attempt;
use App\Models\Challenge;
use App\Models\Category;
use App\Models\Materi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Role;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedDefaultRoles();
    }

    public function test_admin_dashboard_loads_with_empty_data(): void
    {
        Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        $response = $this->actingAs($admin)->get(route('dashboard'));

        $response->assertOk();
        $response->assertViewIs('dashboard.admin');
        $response->assertSee('TOTAL PENGGUNA');
        $response->assertSee('Belum ada peserta.');
    }

    public function test_admin_dashboard_renders_statistics_and_filters(): void
    {
        Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Peserta', 'guard_name' => 'web']);
        $admin = User::factory()->create();
        $participant = User::factory()->create();
        $admin->assignRole('Admin');
        $participant->assignRole('Peserta');

        $category = Category::create([
            'name' => 'Web Security',
            'slug' => 'web-security',
            'is_active' => true,
            'point_ambang_batas' => 60,
        ]);
        $challenge = Challenge::create([
            'kategori_id' => $category->id,
            'title' => 'Challenge Admin',
            'description' => 'desc',
            'duration_minutes' => 10,
            'is_active' => true,
            'created_by' => $admin->id,
        ]);
        Materi::create([
            'kategori_id' => $category->id,
            'level' => 'easy',
            'title' => 'Materi Aktif',
            'description' => 'desc',
            'content' => 'content',
            'is_active' => true,
            'created_by' => $admin->id,
        ]);
        Attempt::create([
            'challenge_id' => $challenge->id,
            'user_id' => $participant->id,
            'started_at' => now()->subMinutes(8),
            'finished_at' => now(),
            'total_score' => 87.5,
            'status' => Attempt::STATUS_COMPLETED,
        ]);

        $response = $this->actingAs($admin)->get(route('dashboard', [
            'category_id' => $category->id,
            'challenge_id' => $challenge->id,
            'period' => '7_days',
        ]));

        $response->assertOk();
        $response->assertViewIs('dashboard.admin');
        $response->assertSee('87.50');
        // Nama kategori dirender uppercase di chart "Sukses Rate Per Kategori".
        $response->assertSee('WEB SECURITY');
    }

    public function test_peserta_dashboard_loads_without_relation_error(): void
    {
        Role::firstOrCreate(['name' => 'Peserta', 'guard_name' => 'web']);
        $user = User::factory()->create();
        $user->assignRole('Peserta');

        $category = Category::create([
            'name' => 'Web Security',
            'slug' => 'web-security',
            'is_active' => true,
            'point_ambang_batas' => 60,
        ]);

        Challenge::create([
            'kategori_id' => $category->id,
            'title' => 'Challenge Aktif',
            'description' => 'desc',
            'duration_minutes' => 10,
            'is_active' => true,
            'created_by' => $user->id,
        ]);

        $this->actingAs($user);

        $response = $this->get(route('dashboard'));

        $response->assertOk();
        $response->assertViewIs('dashboard.peserta');
    }
}