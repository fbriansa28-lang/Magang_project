<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

        public function run(): void
    {
        $this->call([
            UserSeeder::class,
            CategorySeeder::class,
            QuestionBankSeeder::class,
            // Soal variatif harus masuk pool SEBELUM set challenge dibangun,
            // supaya soal acak yang terpilih mencakup pool variatif.
            QuestionBankVariedSeeder::class,
            ChallengeRandomSeeder::class,
            LearningMaterialSeeder::class,
        ]);
    }
}