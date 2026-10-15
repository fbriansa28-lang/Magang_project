<?php

namespace Tests\Feature\Concerns;

use App\Models\Category;
use App\Models\Challenge;
use App\Models\QuestionBank;
use App\Models\User;

/**
 * Helper bersama untuk test fitur challenge (set soal tetap):
 * membuat kategori, challenge, bank soal, dan admin pembuat.
 */
trait CreatesChallenges
{
    protected function makeCategory(array $attributes = []): Category
    {
        return Category::create(array_merge([
            'name' => 'Kategori '.uniqid(),
            'weight_score' => 10,
            'point_ambang_batas' => 60,
            'is_active' => true,
        ], $attributes));
    }

    protected function makeChallenge(Category $category, array $attributes = []): Challenge
    {
        return Challenge::create(array_merge([
            'kategori_id' => $category->id,
            'title' => 'Challenge Uji',
            'description' => 'deskripsi',
            'duration_minutes' => 30,
            'questions_easy' => 0,
            'questions_medium' => 0,
            'questions_hard' => 0,
            'is_active' => true,
            'created_by' => User::factory()->create()->id,
        ], $attributes));
    }

    /**
     * Isi bank soal untuk satu kombinasi tipe x tingkat.
     */
    protected function seedStock(int $categoryId, string $type, string $difficulty, int $count, bool $withHint = false): void
    {
        QuestionBank::factory()
            ->count($count)
            ->state(array_merge([
                'kategori_id' => $categoryId,
                'question_type' => $type,
                'difficulty' => $difficulty,
            ], $type === 'essay' ? ['flag' => 'Jawaban referensi '.uniqid()] : []))
            ->when($withHint, fn ($factory) => $factory->withHint())
            ->create();
    }

    /**
     * Isi bank soal merata: tiap kombinasi tipe x tingkat dapat $perCombo soal.
     */
    protected function seedStockAllCombos(int $categoryId, int $perCombo = 5, bool $withHint = false): void
    {
        foreach (['pilihan_ganda', 'essay'] as $type) {
            foreach (['easy', 'medium', 'hard'] as $difficulty) {
                $this->seedStock($categoryId, $type, $difficulty, $perCombo, $withHint);
            }
        }
    }
}
