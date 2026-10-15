<?php

namespace Tests;

use App\Models\Role;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Pastikan role inti (Admin & Peserta) tersedia di database test.
     *
     * RefreshDatabase tidak menjalankan UserSeeder, sedangkan beberapa
     * controller memanggil User::role('Peserta') / hasRole('Admin')
     * yang membutuhkan role tsb sudah ada.
     */
    protected function seedDefaultRoles(): void
    {
        Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Peserta', 'guard_name' => 'web']);
    }
}
