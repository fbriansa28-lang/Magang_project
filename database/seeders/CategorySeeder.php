<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Web Exploitation',  'weight_score' => 10,  'is_active' => true],
            ['name' => 'Cryptography',      'weight_score' => 10,  'is_active' => true],
            ['name' => 'Forensics',         'weight_score' => 10,  'is_active' => true],
            ['name' => 'Reverse Engineering','weight_score' => 10,  'is_active' => true],
            ['name' => 'Network Security',   'weight_score' => 10,  'is_active' => true],
            ['name' => 'Binary Exploitation','weight_score' => 10,   'is_active' => true],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['name' => $category['name']],
                [
                    'weight_score'       => $category['weight_score'],
                    'is_active'          => $category['is_active'],
                ]
            );
        }
    }
}
