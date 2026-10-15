<?php

namespace Database\Factories;

use App\Models\QuestionBank;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\QuestionBank>
 */
class QuestionBankFactory extends Factory
{
    protected $model = QuestionBank::class;

    public function definition(): array
    {
        return [
            'kategori_id' => null,
            'group_id' => null,
            'content' => fake()->unique()->sentence(),
            'question_type' => 'pilihan_ganda',
            'correct_option' => 'a',
            'difficulty' => 'easy',
            'hint' => null,
            'is_active' => true,
        ];
    }

    public function pilihanGanda(): static
    {
        return $this->state(fn () => [
            'question_type' => 'pilihan_ganda',
            'flag' => null,
            'correct_option' => 'a',
        ]);
    }

    public function essay(): static
    {
        return $this->state(fn () => [
            'question_type' => 'essay',
            'correct_option' => null,
            'flag' => fake()->sentence(),
        ]);
    }

    public function withHint(): static
    {
        return $this->state(fn () => [
            'hint' => fake()->sentence(),
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn () => [
            'is_active' => false,
        ]);
    }
}
