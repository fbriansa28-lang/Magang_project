<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Buat semua permission
       $permissions = [
            'kelola managemen user',
           
            'view user',
            'edit user',
            'delete user',
            
            'view role',
            'create role',
            'edit role',
            'delete role',
            
            'view permission',
            'create permission',
            'edit permission',
            'delete permission',

            'view challenge',
            'start challenge',
            'create challenge',
            'edit challenge',
            'delete challenge',

            'view bank soal',
            'create bank soal',
            'edit bank soal',
            'delete bank soal',

            'view kelola learning',
            'create kelola learning',
            'edit kelola learning',
            'delete kelola learning',

            'view managemen konten',

            'view kategori',
            'create kategori',
            'edit kategori',
            'delete kategori',

            'view leaderboard',
            'show leaderboard',
            'view log sistem',
            'view all log sistem',
            'view recommendation',

            'view rapot',
            'view rapot sendiri',

            'view learning',
            'show learning',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Permission khusus untuk Peserta.
        $pesertaPermissions = [
            'view learning',
            'show learning',
            'view challenge',
            'start challenge',
            'view log sistem',
            'view recommendation',
            'view rapot sendiri',
        ];

        $adminPermissions = array_values(array_diff($permissions, [
            'view learning',
            'show learning',
            'view recommendation',
            'view rapot sendiri',
        ]));

        // Buat role Admin, assign semua permission KECUALI permission peserta.
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $adminRole->syncPermissions($adminPermissions);

        // Buat role Peserta.
        $pesertaRole = Role::firstOrCreate(['name' => 'Peserta']);
        $pesertaRole->syncPermissions($pesertaPermissions);

        // Buat user admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@mail.com'],
            [
                'name'     => 'Admin',
                'username' => 'adminMin',
                'password' => Hash::make('password'),
            ]
        );

        // Assign role admin ke user
        $admin->assignRole('Admin');

        // Buat beberapa user peserta contoh untuk testing/demo
        $participants = [
            ['name' => 'Budi Santoso',   'username' => 'budisantoso',   'email' => 'budi@mail.com'],
            ['name' => 'Siti Aminah',    'username' => 'sitiaminah',    'email' => 'siti@mail.com'],
            ['name' => 'Andi Wijaya',    'username' => 'andiwijaya',    'email' => 'andi@mail.com'],
            ['name' => 'Dewi Lestari',   'username' => 'dewilestari',   'email' => 'dewi@mail.com'],
            ['name' => 'Rian Pratama',   'username' => 'rianpratama',   'email' => 'rian@mail.com'],
        ];

        foreach ($participants as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name'     => $data['name'],
                    'username' => $data['username'],
                    'password' => Hash::make('password'),
                ]
            );

            if (! $user->hasRole('Peserta')) {
                $user->assignRole('Peserta');
            }
        }
    }
}