<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Challenge;
use App\Models\User;
use App\Services\ChallengeQuestionService;
use Illuminate\Database\Seeder;

/**
 * Challenge contoh untuk kategori "Dasar Keamanan Siber".
 *
 * Pool soal TIDAK dibuat di sini -- soal bank diisi oleh
 * QuestionBankVariedSeeder (soal variatif yang mudah dibedakan), sehingga
 * set soal challenge benar-benar berisi soal berbeda-beda untuk dicek.
 *
 * Jalankan QuestionBankVariedSeeder LEBIH DULU (lihat DatabaseSeeder).
 */
class ChallengeRandomSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@mail.com')->first() ?? User::first();
        if (! $admin) return;

        $category = Category::firstOrCreate(
            ['name' => 'Dasar Keamanan Siber'],
            ['weight_score' => 10, 'point_ambang_batas' => 60, 'is_active' => true]
        );

        // Challenge contoh + set soal tetap (dibangun via service, sama
        // seperti saat admin menyimpan challenge lewat aplikasi).
        $challenge = Challenge::updateOrCreate(
            ['kategori_id' => $category->id, 'title' => 'Simulasi Keamanan Dasar'],
            [
                'description' => 'Challenge contoh dengan set soal tetap dan hint berpenalti.',
                'duration_minutes' => 30,
                'questions_easy' => 4,
                'questions_medium' => 3,
                'questions_hard' => 2,
                'is_active' => true,
                'starts_at' => null,
                'ends_at' => null,
                'created_by' => $admin->id,
            ]
        );

        $questionService = app(ChallengeQuestionService::class);

        // Bangun set bila belum ada (idempoten saat seeder diulang).
        // Dilewati bila sudah ada attempt peserta.
        if ($challenge->challengeQuestions()->doesntExist() && ! $challenge->attempts()->exists()) {
            $questionService->buildSet($challenge, [
                'easy' => (int) $challenge->questions_easy,
                'medium' => (int) $challenge->questions_medium,
                'hard' => (int) $challenge->questions_hard,
            ]);
        }

        $summary = $questionService->setSummary($challenge);

        $this->command?->info(sprintf(
            'Challenge contoh "%s" siap: %d soal, total bobot %s (= 100%%).',
            $challenge->title,
            $summary['total_questions'],
            number_format($summary['total_weight'], 2)
        ));

        // Tampilkan isi set supaya langsung bisa dicek.
        foreach ($challenge->challengeQuestions()->with('question')->get() as $row) {
            $this->command?->line(sprintf(
                '  #%02d [%s/%s, bobot %s] %s',
                $row->order_no,
                $row->type,
                $row->difficulty,
                number_format((float) $row->weight, 2),
                mb_substr((string) $row->question?->content, 0, 72)
            ));
        }
    }
}
